<?php

namespace App\Services;

use App\Models\BurgundyClient;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;

/**
 * Money and counts for the partnership dashboard.
 *
 * One definition of "a Burgundy payment" — every charge against a subscription
 * on the partnership plan — so the overview tiles and the payments page can
 * never disagree with each other.
 *
 * Nothing here is stored. Every figure is read live from the subscriptions and
 * payments the Authorize.Net webhook already maintains.
 */
class BurgundyLedger
{
    private function planKey(): string
    {
        return (string) config('partnership.plan.key', 'burgundy-100');
    }

    /** Subscriptions belonging to the partnership plan. */
    public function subscriptions(): Builder
    {
        return Subscription::where('plan_key', $this->planKey());
    }

    /** Every charge, refund and void against those subscriptions. */
    public function payments(): Builder
    {
        return Payment::whereIn('subscription_id', $this->subscriptions()->select('id'));
    }

    /**
     * The overview tiles.
     *
     * Deliberately excludes anything about Apex charges, Apex pricing, support
     * costs or internal service-provider accounting — this dashboard is client,
     * payment and onboarding visibility only.
     */
    public function metrics(): array
    {
        $clients = BurgundyClient::query();

        $byContact = BurgundyClient::selectRaw('contact_status, COUNT(*) as n')
            ->groupBy('contact_status')
            ->pluck('n', 'contact_status');

        $paid = BurgundyClient::whereNotNull('subscription_id')->count();

        $collected = (float) $this->payments()
            ->where('status', 'captured')
            ->whereIn('type', ['initial', 'recurring'])
            ->sum('amount');

        $refunded = (float) $this->payments()
            ->whereIn('type', ['refund', 'void'])
            ->sum('amount');

        $activeSubs = $this->subscriptions()->where('status', 'active')->count();
        $monthly    = (float) config('partnership.plan.monthly', 100);

        return [
            'legacy_clients'     => (clone $clients)->where('source', 'legacy')->count(),
            'new_clients'        => (clone $clients)->where('source', 'new')->count(),
            'total_clients'      => (clone $clients)->count(),

            'not_contacted'      => (int) ($byContact['not_contacted'] ?? 0),
            'contacted'          => (int) ($byContact['contacted'] ?? 0),
            'interested'         => (int) ($byContact['interested'] ?? 0),
            'not_interested'     => (int) ($byContact['not_interested'] ?? 0),
            'payment_links_sent' => (int) ($byContact['payment_link_sent'] ?? 0),

            'paid'               => $paid,
            'onboarding_pending' => (clone $clients)->whereNotNull('subscription_id')
                                        ->where('onboarding_status', 'pending')->count(),
            'onboarding_complete'=> (clone $clients)->where('onboarding_status', 'complete')->count(),
            'active'             => (clone $clients)->where('client_status', 'active')->count(),
            'cancelled'          => (clone $clients)->where('client_status', 'cancelled')->count(),

            'needs_review'       => (clone $clients)->where('match_status', 'needs_review')->count(),
            'possible_duplicates'=> (clone $clients)->whereNotNull('possible_duplicate_of')->count(),

            // Money
            'monthly_revenue'    => $activeSubs * $monthly,
            'payments_collected' => $collected,
            'net_collected'      => $collected - $refunded,
            'refunded'           => $refunded,
            'failed_payments'    => $this->subscriptions()->where('status', 'past_due')->count(),
            'refund_count'       => $this->payments()->whereIn('type', ['refund', 'void'])->count(),

            // Conversion through the ladder — what Victoria actually wants to know.
            'transition_rate'    => $this->rate($paid, (clone $clients)->where('source', 'legacy')->count()),
        ];
    }

    private function rate(int $part, int $whole): float
    {
        return $whole > 0 ? round(($part / $whole) * 100, 1) : 0.0;
    }
}
