<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // Subscriptions
    // ════════════════════════════════════════════════════════════════
    public function subscriptions(Request $request)
    {
        $q = Subscription::query();

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email',               'like', "%{$search}%")
                  ->orWhere('first_name',        'like', "%{$search}%")
                  ->orWhere('last_name',         'like', "%{$search}%")
                  ->orWhere('phone',             'like', "%{$search}%")
                  ->orWhere('invoice_number',    'like', "%{$search}%")
                  ->orWhere('transaction_id',    'like', "%{$search}%")
                  ->orWhere('arb_subscription_id','like', "%{$search}%")
                  ->orWhere('plan_label',        'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        if ($plan = $request->query('plan')) {
            $q->where('plan_key', $plan);
        }

        // KPIs at the top of the index
        $kpis = [
            'total'      => Subscription::count(),
            'active'     => Subscription::where('status', 'active')->count(),
            'past_due'   => Subscription::where('status', 'past_due')->count(),
            'terminated' => Subscription::where('status', 'terminated')->count(),
            'mrr'        => (float) Subscription::where('status', 'active')
                                ->whereNotNull('recurring_amount')
                                ->sum('recurring_amount'),
            'gross_today' => (float) Payment::whereDate('charged_at', today())
                                ->whereIn('status', ['captured'])
                                ->sum('amount'),
        ];

        return view('admin.payments.subscriptions', [
            'rows' => $q->latest()->paginate(25)->withQueryString(),
            'kpis' => $kpis,
        ]);
    }

    public function subscriptionShow(Subscription $subscription)
    {
        $subscription->load([
            'payments' => fn ($q) => $q->latest('charged_at'),
            'events'   => fn ($q) => $q->latest(),
        ]);

        // Related webhook rows (by invoice OR arb id OR txn id on any payment)
        $relatedWebhooks = WebhookEvent::query()
            ->where(function ($w) use ($subscription) {
                $w->where('matched_subscription_id', $subscription->id)
                  ->orWhere('invoice_number', $subscription->invoice_number);
                if ($subscription->arb_subscription_id) {
                    $w->orWhere('entity_id', $subscription->arb_subscription_id);
                }
            })
            ->latest('received_at')
            ->limit(50)
            ->get();

        $totals = [
            'captured'       => (float) $subscription->payments
                                    ->whereIn('type', ['initial', 'recurring'])
                                    ->where('status', 'captured')
                                    ->sum('amount'),
            'refunded'       => (float) $subscription->payments
                                    ->whereIn('type', ['refund', 'void'])
                                    ->sum('amount'),
            'lifetime_net'   => (float) $subscription->lifetimeRevenue(),
            'payment_count'  => $subscription->payments->count(),
        ];

        return view('admin.payments.subscription-show', [
            'subscription'   => $subscription,
            'totals'         => $totals,
            'relatedWebhooks'=> $relatedWebhooks,
        ]);
    }

    public function subscriptionStatus(Subscription $subscription, Request $request)
    {
        $request->validate(['status' => 'required|in:active,past_due,terminated']);
        $updates = ['status' => $request->status];
        if ($request->status === 'terminated' && !$subscription->terminated_at) {
            $updates['terminated_at'] = now();
        }
        if ($request->status === 'active') {
            $updates['failed_payment_count'] = 0;
            $updates['first_failed_at']      = null;
            $updates['grace_period_ends_at'] = null;
        }
        $subscription->update($updates);

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'manual_note',
            'payload'         => null,
            'note'            => 'Status manually changed to ' . $request->status . ' by admin.',
        ]);

        return back()->with('success', 'Subscription status updated.');
    }

    // ════════════════════════════════════════════════════════════════
    // Payments
    // ════════════════════════════════════════════════════════════════
    public function payments(Request $request)
    {
        $q = Payment::query()->with('subscription');

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('subscription', function ($s) use ($search) {
                      $s->where('email',      'like', "%{$search}%")
                        ->orWhere('first_name','like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->query('type')) {
            $q->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        $kpis = [
            'gross_lifetime' => (float) Payment::where('status', 'captured')
                                    ->whereIn('type', ['initial', 'recurring'])
                                    ->sum('amount'),
            'refunded_lifetime' => (float) Payment::whereIn('type', ['refund', 'void'])
                                    ->sum('amount'),
            'gross_today'  => (float) Payment::whereDate('charged_at', today())
                                    ->where('status', 'captured')
                                    ->sum('amount'),
            'gross_mtd'    => (float) Payment::whereMonth('charged_at', now()->month)
                                    ->whereYear('charged_at', now()->year)
                                    ->where('status', 'captured')
                                    ->sum('amount'),
            'count_total'  => Payment::count(),
            'count_today'  => Payment::whereDate('charged_at', today())->count(),
        ];

        return view('admin.payments.payments', [
            'rows' => $q->latest('charged_at')->paginate(50)->withQueryString(),
            'kpis' => $kpis,
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // Webhooks
    // ════════════════════════════════════════════════════════════════
    public function webhooks(Request $request)
    {
        $q = WebhookEvent::query();

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('event_type',     'like', "%{$search}%")
                  ->orWhere('entity_id',     'like', "%{$search}%")
                  ->orWhere('invoice_number','like', "%{$search}%")
                  ->orWhere('customer_email','like', "%{$search}%")
                  ->orWhere('customer_first_name','like', "%{$search}%")
                  ->orWhere('customer_last_name', 'like', "%{$search}%")
                  ->orWhere('description',   'like', "%{$search}%");
            });
        }

        if ($type = $request->query('event_type')) {
            $q->where('event_type', $type);
        }

        if ($sig = $request->query('sig')) {
            if ($sig === 'invalid') {
                $q->where('signature_valid', false);
            } elseif ($sig === 'valid') {
                $q->where('signature_valid', true);
            } elseif ($sig === 'unverified') {
                $q->whereNull('signature_valid');
            }
        }

        $kpis = [
            'total'        => WebhookEvent::count(),
            'today'        => WebhookEvent::whereDate('received_at', today())->count(),
            'invalid_sig'  => WebhookEvent::where('signature_valid', false)->count(),
            'unverified'   => WebhookEvent::whereNull('signature_valid')->count(),
            'unique_types' => WebhookEvent::distinct('event_type')->count('event_type'),
        ];

        // For the filter pill row — top event types
        $topTypes = WebhookEvent::query()
            ->selectRaw('event_type, COUNT(*) as c')
            ->groupBy('event_type')
            ->orderByDesc('c')
            ->limit(8)
            ->get();

        return view('admin.payments.webhooks', [
            'rows'     => $q->latest('received_at')->paginate(50)->withQueryString(),
            'kpis'     => $kpis,
            'topTypes' => $topTypes,
        ]);
    }

    public function webhookShow(WebhookEvent $webhook)
    {
        $subscription = null;
        if ($webhook->matched_subscription_id) {
            $subscription = Subscription::find($webhook->matched_subscription_id);
        } elseif ($webhook->invoice_number) {
            $subscription = Subscription::where('invoice_number', $webhook->invoice_number)->first();
        }

        return view('admin.payments.webhook-show', [
            'webhook'      => $webhook,
            'subscription' => $subscription,
        ]);
    }
}
