@extends('admin.layout')
@section('title', 'Webhook · ' . $webhook->event_type)

@section('content')
<div class="admin-header">
  <div>
    <h1>Webhook event
      @if($webhook->signature_valid === true)<span class="badge active" style="vertical-align:middle;margin-left:8px">signature valid</span>
      @elseif($webhook->signature_valid === false)<span class="badge failed" style="vertical-align:middle;margin-left:8px">signature INVALID</span>
      @else<span class="badge archived" style="vertical-align:middle;margin-left:8px">unverified</span>@endif
    </h1>
    <div class="sub">{{ $webhook->event_type }} · received {{ $webhook->received_at?->format('M j, Y · g:ia:s') ?? $webhook->created_at->format('M j, Y · g:ia') }}</div>
  </div>
  <div>
    <a class="adm-btn ghost" href="{{ route('admin.webhooks') }}">← All webhooks</a>
  </div>
</div>

<div class="adm-grid-2">
  <!-- ════════ EVENT DETAILS ════════ -->
  <div class="adm-card">
    <div class="adm-card-head"><h2>Event</h2></div>
    <div class="detail-grid">
      <div class="lab">Event type</div>
      <div class="val mono">{{ $webhook->event_type }}</div>

      <div class="lab">Notification ID</div>
      <div class="val mono">{{ $webhook->notification_id ?: '— (forged or test traffic)' }}</div>

      <div class="lab">Entity ID</div>
      <div class="val mono">{{ $webhook->entity_id ?: '—' }}</div>

      <div class="lab">Invoice number</div>
      <div class="val mono">{{ $webhook->invoice_number ?: '—' }}</div>

      <div class="lab">Amount</div>
      <div class="val">{{ $webhook->amount !== null ? '$' . number_format((float) $webhook->amount, 2) : '—' }}</div>

      <div class="lab">ARB status (if sub)</div>
      <div class="val">{{ $webhook->arb_status ?: '—' }}</div>

      <div class="lab">Response code</div>
      <div class="val">{{ $webhook->response_code ?: '—' }}</div>

      <div class="lab">Description</div>
      <div class="val">{{ $webhook->description ?: '—' }}</div>

      <div class="lab">Source IP</div>
      <div class="val mono">{{ $webhook->source_ip ?: '—' }}</div>

      <div class="lab">Received at</div>
      <div class="val">{{ $webhook->received_at?->format('M j, Y · g:ia:s T') ?? $webhook->created_at->format('M j, Y · g:ia:s') }} ({{ ($webhook->received_at ?? $webhook->created_at)->diffForHumans() }})</div>

      <div class="lab">Signature</div>
      <div class="val">
        @if($webhook->signature_valid === true)
          <span class="badge active">VALID</span> — HMAC-SHA512 matched the signing key
        @elseif($webhook->signature_valid === false)
          <span class="badge failed">INVALID</span> — header didn't match HMAC. Likely wrong key in <code>.env</code>.
        @else
          <span class="badge archived">UNVERIFIED</span> — either no signing key in <code>.env</code> or header missing
        @endif
      </div>
    </div>
  </div>

  <!-- ════════ LINKED CUSTOMER ════════ -->
  <div class="adm-card">
    <div class="adm-card-head">
      <h2>Linked customer</h2>
      @if($subscription)<a href="{{ route('admin.subscriptions.show', $subscription) }}">Open subscription →</a>@endif
    </div>
    @if($subscription)
      <div class="detail-grid">
        <div class="lab">Name</div>
        <div class="val">{{ $subscription->first_name }} {{ $subscription->last_name }}</div>

        <div class="lab">Email</div>
        <div class="val"><a href="mailto:{{ $subscription->email }}" style="color:var(--pink)">{{ $subscription->email }}</a></div>

        <div class="lab">Phone</div>
        <div class="val">{{ $subscription->phone ?: '—' }}</div>

        <div class="lab">Plan</div>
        <div class="val">{{ $subscription->plan_label }}</div>

        <div class="lab">Status</div>
        <div class="val"><span class="badge {{ $subscription->status === 'active' ? 'active' : ($subscription->status === 'past_due' ? 'in_progress' : 'archived') }}">{{ str_replace('_',' ', $subscription->status) }}</span></div>

        <div class="lab">Invoice #</div>
        <div class="val mono">{{ $subscription->invoice_number }}</div>

        <div class="lab">ARB subscription ID</div>
        <div class="val mono">{{ $subscription->arb_subscription_id ?: '—' }}</div>
      </div>
    @else
      <div class="empty" style="margin:0">
        <strong>No matching subscription.</strong>
        @if($webhook->entity_id || $webhook->invoice_number)
          We couldn't match this event to a local subscription by entity ID
          (<span style="font-family:'SF Mono',Menlo,monospace">{{ $webhook->entity_id ?: '—' }}</span>)
          or invoice (<span style="font-family:'SF Mono',Menlo,monospace">{{ $webhook->invoice_number ?: '—' }}</span>).
          This is normal for informational events like <code>customer.created</code> or for legacy subs predating this system.
        @else
          This is an informational event with no entity reference (e.g. customer profile updated).
        @endif
      </div>
    @endif
  </div>
</div>

<!-- ════════ RAW PAYLOAD ════════ -->
<div class="adm-card" style="margin-top:16px">
  <div class="adm-card-head">
    <h2>Raw payload</h2>
    <span class="sub" style="color:var(--ink-3);font-size:12px">Exact JSON received from Authorize.Net</span>
  </div>
  <pre style="background:#0d0a0d;color:#fdeaf2;padding:18px 20px;border-radius:12px;overflow:auto;font-family:'SF Mono',Menlo,Consolas,monospace;font-size:12.5px;line-height:1.55;max-height:600px">{{ json_encode($webhook->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
</div>
@endsection
