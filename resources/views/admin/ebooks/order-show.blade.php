@extends('admin.layout')
@section('title', 'Order ' . $order->invoice_number)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $order->fullName() }}</h1>
    <div class="sub">Order <span style="font-family:'SF Mono',Menlo,monospace">{{ $order->invoice_number }}</span> · {{ $order->ebook_title }}</div>
  </div>
  <a href="{{ route('admin.ebook-orders') }}" class="adm-btn ghost">← Back to sales</a>
</div>

<div class="adm-grid-2">
  <div class="adm-card">
    <div class="adm-card-head">
      <h2>Order details</h2>
      <span class="badge {{ $order->status === 'paid' ? 'active' : 'failed' }}">{{ $order->status }}</span>
    </div>
    <div class="detail-grid">
      <div class="lab">eBook</div>
      <div class="val">{{ $order->ebook_title }}</div>

      <div class="lab">Amount paid</div>
      <div class="val"><strong style="color:#157a3d">${{ number_format((float) $order->amount, 2) }}</strong></div>

      <div class="lab">Charged at</div>
      <div class="val">{{ $order->charged_at?->format('M j, Y · g:ia') ?? '—' }}</div>

      <div class="lab">Invoice</div>
      <div class="val mono">{{ $order->invoice_number }}</div>

      <div class="lab">Transaction ID</div>
      <div class="val mono">{{ $order->transaction_id ?? '—' }}</div>

      <div class="lab">Auth code</div>
      <div class="val mono">{{ $order->auth_code ?? '—' }}</div>

      <div class="lab">Downloaded</div>
      <div class="val">
        @if($order->downloaded_at)
          <strong style="color:#157a3d">✓ {{ $order->downloaded_at->format('M j, Y · g:ia') }}</strong>
          @if($order->download_count > 1)
            <span style="color:var(--ink-3);font-size:12.5px"> · {{ $order->download_count }} visits</span>
          @endif
        @else
          <span style="color:var(--ink-3)">Not yet</span>
        @endif
      </div>

      <div class="lab">Drive link active?</div>
      <div class="val">
        @if($order->ebook && $order->ebook->drive_link)
          <a href="{{ $order->ebook->drive_link }}" target="_blank" style="color:var(--pink);font-weight:600">Open Drive link ↗</a>
        @else
          <span style="color:#92400e">⚠ Missing — buyer cannot download yet</span>
        @endif
      </div>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-head">
      <h2>Customer</h2>
      <a href="mailto:{{ $order->email }}">Email →</a>
    </div>
    <div class="detail-grid">
      <div class="lab">Name</div>
      <div class="val">{{ $order->fullName() }}</div>

      <div class="lab">Email</div>
      <div class="val"><a href="mailto:{{ $order->email }}" style="color:var(--pink);font-weight:600">{{ $order->email }}</a></div>

      <div class="lab">Phone</div>
      <div class="val">{{ $order->phone ?? '—' }}</div>

      <div class="lab">Address</div>
      <div class="val">
        {{ $order->address ?? '—' }}
        @if($order->city || $order->state || $order->zip)
          <br>{{ $order->city }}@if($order->state), {{ $order->state }}@endif {{ $order->zip }}
        @endif
      </div>

      <div class="lab">Marketing opt-in</div>
      <div class="val">{{ $order->marketing_opt_in ? 'Yes' : 'No' }}</div>

      <div class="lab">IP / Device</div>
      <div class="val mono" style="font-size:11.5px">{{ $order->ip_address ?? '—' }}<br>{{ $order->user_agent ?? '' }}</div>
    </div>
  </div>
</div>

@if($order->raw_payload)
  <div class="adm-card" style="margin-top:16px">
    <div class="adm-card-head">
      <h2>Gateway response</h2>
      <span style="font-size:11.5px;color:var(--ink-3)">Authorize.Net raw</span>
    </div>
    <pre style="background:var(--bg-2);padding:14px 16px;border-radius:10px;font-size:11.5px;line-height:1.5;overflow:auto;max-height:360px;font-family:'SF Mono',Menlo,monospace">{{ json_encode($order->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
  </div>
@endif

@endsection
