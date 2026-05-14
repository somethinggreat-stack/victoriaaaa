@extends('admin.layout')
@section('title', 'Subscription · ' . $subscription->first_name . ' ' . $subscription->last_name)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $subscription->first_name }} {{ $subscription->last_name }}
      <span class="badge {{ $subscription->status === 'active' ? 'active' : ($subscription->status === 'past_due' ? 'in_progress' : 'archived') }}" style="vertical-align:middle;margin-left:8px">{{ str_replace('_',' ', $subscription->status) }}</span>
    </h1>
    <div class="sub">{{ $subscription->plan_label }} · invoice {{ $subscription->invoice_number }} · subscribed {{ $subscription->subscribed_at?->format('M j, Y · g:ia') ?? $subscription->created_at->format('M j, Y · g:ia') }}</div>
  </div>
  <div>
    <a class="adm-btn ghost" href="{{ route('admin.subscriptions') }}">← All subscriptions</a>
  </div>
</div>

<!-- ════════ KPI ROW ════════ -->
<div class="adm-stats" style="grid-template-columns: repeat(4, 1fr);">
  <div class="adm-stat">
    <div class="lab">Today's charge</div>
    <div class="val">${{ number_format((float) $subscription->amount, 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Recurring</div>
    <div class="val">{{ $subscription->recurring_amount ? '$' . number_format((float) $subscription->recurring_amount, 2) . '/mo' : '— one-time' }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Captured to date</div>
    <div class="val" style="color:#157a3d">${{ number_format($totals['captured'], 2) }}</div>
    <div class="delta">{{ $totals['payment_count'] }} payment record(s)</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Refunded / voided</div>
    <div class="val" style="color:#991b1b">${{ number_format($totals['refunded'], 2) }}</div>
    <div class="delta">Net: <strong>${{ number_format($totals['lifetime_net'], 2) }}</strong></div>
  </div>
</div>

<div class="adm-grid-2">
  <!-- ════════ CUSTOMER DETAILS ════════ -->
  <div class="adm-card">
    <div class="adm-card-head"><h2>Customer</h2></div>
    <div class="detail-grid">
      <div class="lab">Name</div>
      <div class="val">{{ $subscription->first_name }} {{ $subscription->last_name }}</div>

      <div class="lab">Email</div>
      <div class="val"><a href="mailto:{{ $subscription->email }}" style="color:var(--pink)">{{ $subscription->email }}</a></div>

      <div class="lab">Phone</div>
      <div class="val">{{ $subscription->phone ?: '—' }}</div>

      <div class="lab">Address</div>
      <div class="val">
        {{ $subscription->address ?: '—' }}@if($subscription->address)<br>@endif
        {{ trim(($subscription->city ?? '') . ', ' . ($subscription->state ?? '') . ' ' . ($subscription->zip ?? ''), ', ') }}
      </div>

      <div class="lab">Referral code</div>
      <div class="val">{{ $subscription->referral_code ?: '— none' }}</div>

      <div class="lab">First seen</div>
      <div class="val">{{ $subscription->created_at->format('M j, Y · g:ia') }} ({{ $subscription->created_at->diffForHumans() }})</div>
    </div>
  </div>

  <!-- ════════ PLAN + GATEWAY IDS ════════ -->
  <div class="adm-card">
    <div class="adm-card-head"><h2>Plan &amp; gateway IDs</h2></div>
    <div class="detail-grid">
      <div class="lab">Plan</div>
      <div class="val">{{ $subscription->plan_label }} <span class="sub" style="color:var(--ink-3)">({{ $subscription->plan_key }})</span></div>

      <div class="lab">Status</div>
      <div class="val">
        <form class="status-form" method="POST" action="{{ route('admin.subscriptions.status', $subscription) }}">
          @csrf @method('PATCH')
          <select name="status" onchange="this.form.submit()">
            @foreach (['active','past_due','terminated'] as $st)
              <option value="{{ $st }}" @selected($subscription->status===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
            @endforeach
          </select>
        </form>
      </div>

      <div class="lab">Invoice #</div>
      <div class="val mono">{{ $subscription->invoice_number }}</div>

      <div class="lab">Initial transaction ID</div>
      <div class="val mono">{{ $subscription->transaction_id ?: '—' }}</div>

      <div class="lab">Auth code</div>
      <div class="val mono">{{ $subscription->auth_code ?: '—' }}</div>

      <div class="lab">ARB subscription ID</div>
      <div class="val mono">{{ $subscription->arb_subscription_id ?: '— (one-time plan)' }}</div>

      <div class="lab">Customer profile ID</div>
      <div class="val mono">{{ $subscription->customer_profile_id ?: '—' }}</div>

      <div class="lab">Customer payment profile ID</div>
      <div class="val mono">{{ $subscription->customer_payment_profile_id ?: '—' }}</div>

      <div class="lab">Next billing date</div>
      <div class="val">{{ $subscription->next_billing_date?->format('M j, Y') ?? '—' }}</div>

      @if($subscription->status === 'past_due')
        <div class="lab">Failed payments</div>
        <div class="val" style="color:#991b1b">{{ $subscription->failed_payment_count }}× · first failed {{ $subscription->first_failed_at?->format('M j, Y · g:ia') }}</div>

        <div class="lab">Grace period ends</div>
        <div class="val">{{ $subscription->grace_period_ends_at?->format('M j, Y · g:ia') ?: '—' }}</div>
      @endif

      @if($subscription->terminated_at)
        <div class="lab">Terminated at</div>
        <div class="val">{{ $subscription->terminated_at->format('M j, Y · g:ia') }}</div>
      @endif
    </div>
  </div>
</div>

<!-- ════════ PAYMENT HISTORY ════════ -->
<div class="adm-card" style="margin-top:16px">
  <div class="adm-card-head">
    <h2>Payment history · {{ $subscription->payments->count() }} record(s)</h2>
  </div>
  @if ($subscription->payments->isEmpty())
    <div class="empty"><strong>No payments recorded yet.</strong>Initial charge is created on checkout; recurring charges arrive via webhook.</div>
  @else
    <div class="adm-table-wrap"><table class="adm-table">
      <thead>
        <tr>
          <th>Charged</th>
          <th>Type</th>
          <th>Status</th>
          <th>Amount</th>
          <th>Transaction ID</th>
          <th>Invoice</th>
          <th>Source event</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($subscription->payments as $p)
          <tr>
            <td>{{ $p->charged_at?->format('M j, Y · g:ia') ?? $p->created_at->format('M j, Y · g:ia') }}</td>
            <td><span class="badge {{ in_array($p->type,['initial','recurring'])?'active':'failed' }}">{{ $p->type }}</span></td>
            <td><span class="badge {{ $p->status === 'captured' ? 'active' : 'failed' }}">{{ $p->status }}</span></td>
            <td><strong>{{ in_array($p->type, ['refund','void']) ? '−' : '' }}${{ number_format((float) $p->amount, 2) }}</strong></td>
            <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $p->transaction_id ?: '—' }}</span></td>
            <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $p->invoice_number ?: '—' }}</span></td>
            <td><span class="sub">{{ $p->event_type_raw ?: '—' }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @endif
</div>

<!-- ════════ EVENT TIMELINE ════════ -->
<div class="adm-card" style="margin-top:16px">
  <div class="adm-card-head"><h2>Subscription events</h2></div>
  @if ($subscription->events->isEmpty())
    <div class="empty"><strong>No events yet.</strong>Lifecycle events (failures, recoveries, cancellations) get logged here as the webhook fires.</div>
  @else
    <div class="adm-table-wrap"><table class="adm-table">
      <thead><tr><th>When</th><th>Event</th><th>Note</th></tr></thead>
      <tbody>
        @foreach ($subscription->events as $e)
          <tr>
            <td>{{ $e->created_at->format('M j, Y · g:ia') }}</td>
            <td><span class="badge {{ $e->event_type === 'payment_failed' ? 'failed' : ($e->event_type === 'terminated' ? 'archived' : 'active') }}">{{ str_replace('_',' ', $e->event_type) }}</span></td>
            <td>{{ $e->note }}</td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @endif
</div>

<!-- ════════ RELATED WEBHOOK ROWS ════════ -->
<div class="adm-card" style="margin-top:16px">
  <div class="adm-card-head">
    <h2>Webhook events linked to this customer</h2>
    <a href="{{ route('admin.webhooks', ['q' => $subscription->invoice_number]) }}">View in webhook log →</a>
  </div>
  @if ($relatedWebhooks->isEmpty())
    <div class="empty"><strong>No webhooks captured yet for this subscription.</strong></div>
  @else
    <div class="adm-table-wrap"><table class="adm-table">
      <thead><tr><th>Received</th><th>Event</th><th>Entity</th><th>Amount</th><th>Sig</th><th></th></tr></thead>
      <tbody>
        @foreach ($relatedWebhooks as $w)
          <tr>
            <td>{{ $w->received_at?->format('M j · g:ia:s') ?? $w->created_at->format('M j · g:ia') }}</td>
            <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $w->event_type }}</span></td>
            <td><span class="sub mono">{{ $w->entity_id ?: '—' }}</span></td>
            <td>{{ $w->amount !== null ? '$' . number_format((float) $w->amount, 2) : '—' }}</td>
            <td>
              @if($w->signature_valid === true)<span class="badge active">valid</span>
              @elseif($w->signature_valid === false)<span class="badge failed">invalid</span>
              @else<span class="badge archived">unverified</span>@endif
            </td>
            <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.webhooks.show', $w) }}">View</a></td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @endif
</div>
@endsection
