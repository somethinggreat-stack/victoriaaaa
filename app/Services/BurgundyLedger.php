<?php

namespace App\Services;

use App\Models\BurgundyClient;
use App\Models\BurgundyExpense;
use App\Models\BurgundyPayment;
use App\Models\BurgundyRound;
use App\Models\Payment;
use App\Models\PaymentLink;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Money maths for Burgundy Clients.
 *
 *   Net Profit     = Collected Revenue − Backend Costs − Customer Support − Approved Expenses
 *   Burgundy Share = 50% of Net Profit
 *   Victoria Share = 50% of Net Profit
 *
 * Collected revenue is read from where the money already lives:
 *   - the linked subscription's rows in `payments` (Authorize.Net checkout / ARB)
 *   - payment links sent from the Burgundy page (via burgundy_payments.payment_link_id)
 *   - manually recorded off-platform payments (burgundy_payments without a link)
 */
class BurgundyLedger
{
    public const TABLES = ['burgundy_clients', 'burgundy_rounds', 'burgundy_payments', 'burgundy_expenses'];

    public static function isInstalled(): bool
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }
        return true;
    }

    public static function roundCost(): float
    {
        return (float) config('services.burgundy.round_cost', 15);
    }

    public static function supportWeekly(): float
    {
        return (float) config('services.burgundy.support_weekly', 100);
    }

    public static function burgundyShare(): float
    {
        return (float) config('services.burgundy.burgundy_share', 0.5);
    }

    /**
     * Pull state from existing payment records: mark link-backed rows paid/void
     * and advance clients still waiting on payment. Idempotent.
     */
    public static function sync(): void
    {
        if (Schema::hasTable('payment_links')) {
            $pending = BurgundyPayment::with('client')
                ->where('status', 'pending')
                ->whereNotNull('payment_link_id')
                ->get();

            if ($pending->isNotEmpty()) {
                $links = PaymentLink::whereIn('id', $pending->pluck('payment_link_id'))->get()->keyBy('id');

                foreach ($pending as $row) {
                    $link = $links->get($row->payment_link_id);
                    if (! $link) {
                        continue;
                    }
                    if ($link->status === 'paid') {
                        $row->update(['status' => 'paid', 'paid_at' => $link->paid_at ?? now()]);
                        $row->client?->markPaidIfWaiting($row->paid_at);
                    } elseif ($link->status === 'void') {
                        $row->update(['status' => 'void']);
                    }
                }
            }
        }

        $waiting = BurgundyClient::whereIn('status', BurgundyClient::AWAITING_PAYMENT)
            ->whereNotNull('subscription_id')
            ->get();

        foreach ($waiting as $client) {
            $first = Payment::where('subscription_id', $client->subscription_id)
                ->where('status', 'captured')
                ->whereIn('type', ['initial', 'recurring'])
                ->orderBy('charged_at')
                ->first();

            if ($first) {
                $client->markPaidIfWaiting($first->charged_at);
            }
        }
    }

    /**
     * Collected revenue per client id, optionally limited to a date window.
     *
     * @param  Collection<int, BurgundyClient>  $clients
     * @param  bool  $forProfit  exclude pre-transition payments (profit split only)
     * @return array<int, float>
     */
    public static function collectedByClient(Collection $clients, ?CarbonInterface $from = null, ?CarbonInterface $to = null, bool $forProfit = false): array
    {
        $out = [];
        foreach ($clients as $client) {
            $out[$client->id] = 0.0;
        }
        if (! $out) {
            return $out;
        }

        $own = BurgundyPayment::whereIn('burgundy_client_id', array_keys($out))->where('status', 'paid');
        if ($forProfit) {
            $own->where('counts_toward_profit', true);
        }
        if ($from) $own->where('paid_at', '>=', $from);
        if ($to)   $own->where('paid_at', '<=', $to);

        foreach ($own->get(['burgundy_client_id', 'amount']) as $p) {
            $out[$p->burgundy_client_id] += (float) $p->amount;
        }

        // subscription_id => client id
        $bySubscription = $clients->filter(fn ($c) => $c->subscription_id)->mapWithKeys(fn ($c) => [$c->subscription_id => $c->id]);

        if ($bySubscription->isNotEmpty()) {
            $gateway = Payment::whereIn('subscription_id', $bySubscription->keys())
                ->where(function ($w) {
                    $w->where(fn ($x) => $x->where('status', 'captured')->whereIn('type', ['initial', 'recurring']))
                      ->orWhereIn('type', ['refund', 'void']);
                });
            if ($from) $gateway->where('charged_at', '>=', $from);
            if ($to)   $gateway->where('charged_at', '<=', $to);

            foreach ($gateway->get(['subscription_id', 'type', 'amount']) as $p) {
                $out[$bySubscription[$p->subscription_id]] += $p->signedAmount();
            }
        }

        return array_map(fn ($v) => round($v, 2), $out);
    }

    /**
     * Customer support is billed at a flat weekly rate from the program start
     * (BURGUNDY_SUPPORT_START, else the first Burgundy client) up to today.
     *
     * @return array{amount: float, weeks: float}
     */
    public static function supportCost(?CarbonInterface $from, CarbonInterface $to): array
    {
        $configured = config('services.burgundy.support_start');
        $start = $configured ? Carbon::parse($configured) : (($min = BurgundyClient::min('created_at')) ? Carbon::parse($min) : null);

        if (! $start) {
            return ['amount' => 0.0, 'weeks' => 0.0];
        }

        $begin = ($from && $from->greaterThan($start) ? Carbon::instance($from) : $start)->copy()->startOfDay();
        $end   = (now()->lessThan($to) ? now() : Carbon::instance($to))->copy()->startOfDay();

        if ($end->lessThan($begin)) {
            return ['amount' => 0.0, 'weeks' => 0.0];
        }

        $days  = (int) round(abs($begin->diffInDays($end))) + 1;
        $weeks = $days / 7;

        return ['amount' => round($weeks * self::supportWeekly(), 2), 'weeks' => round($weeks, 1)];
    }

    /** All summary-card money figures for a window (null $from = all time). */
    public static function financials(?CarbonInterface $from, CarbonInterface $to): array
    {
        $clients = BurgundyClient::get(['id', 'subscription_id']);

        $revenue = array_sum(self::collectedByClient($clients, $from, $to, true));

        $rounds = BurgundyRound::query();
        if ($from) $rounds->where('processed_at', '>=', $from);
        $rounds->where('processed_at', '<=', $to);
        $roundCount = (clone $rounds)->count();
        $backend    = (float) $rounds->sum('cost');

        $support = self::supportCost($from, $to);

        $expenses = BurgundyExpense::where('status', 'approved')->whereDate('expense_date', '<=', $to);
        if ($from) $expenses->whereDate('expense_date', '>=', $from);
        $expenses = (float) $expenses->sum('amount');

        $net      = round($revenue - $backend - $support['amount'] - $expenses, 2);
        $burgundy = round($net * self::burgundyShare(), 2);

        return [
            'revenue'       => round($revenue, 2),
            'backend'       => round($backend, 2),
            'rounds'        => $roundCount,
            'support'       => $support['amount'],
            'support_weeks' => $support['weeks'],
            'expenses'      => round($expenses, 2),
            'net'           => $net,
            'burgundy'      => $burgundy,
            'victoria'      => round($net - $burgundy, 2),
        ];
    }
}
