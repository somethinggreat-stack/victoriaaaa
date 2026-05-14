@extends('admin.layout')
@section('title', 'Webhook events')

@section('content')
<div class="admin-header">
  <div>
    <h1>Webhook events</h1>
    <div class="sub">{{ $rows->total() }} event{{ $rows->total() === 1 ? '' : 's' }} received from Authorize.Net · every event is logged, even informational ones</div>
  </div>
</div>

<div class="adm-stats" style="grid-template-columns: repeat(5, 1fr);">
  <div class="adm-stat">
    <div class="lab">Total received</div>
    <div class="val">{{ number_format($kpis['total']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Today</div>
    <div class="val">{{ number_format($kpis['today']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Unique event types</div>
    <div class="val">{{ number_format($kpis['unique_types']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Invalid signature</div>
    <div class="val" style="color:#991b1b">{{ number_format($kpis['invalid_sig']) }}</div>
    <div class="delta">Should be 0 in steady-state</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Unverified</div>
    <div class="val" style="color:#92400e">{{ number_format($kpis['unverified']) }}</div>
    <div class="delta">No signature key configured yet, or header missing</div>
  </div>
</div>

@if ($topTypes->isNotEmpty())
  <div class="adm-toolbar" style="margin-bottom:8px">
    <span class="sub" style="color:var(--ink-3);text-transform:uppercase;letter-spacing:0.12em;font-size:11px;font-weight:700;align-self:center">Filter by type:</span>
    <a class="adm-btn ghost" href="{{ route('admin.webhooks') }}">All</a>
    @foreach ($topTypes as $t)
      @php $short = str_replace(['net.authorize.', 'customer.', 'payment.'], '', $t->event_type); @endphp
      <a class="adm-btn @if(request('event_type')===$t->event_type)@else ghost @endif" href="{{ route('admin.webhooks', ['event_type' => $t->event_type]) }}">{{ $short }} · {{ $t->c }}</a>
    @endforeach
  </div>
@endif

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.webhooks') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search event type, entity ID, invoice, customer name or email" value="{{ request('q') }}">
    @if(request('event_type'))<input type="hidden" name="event_type" value="{{ request('event_type') }}">@endif
    <select class="adm-select" name="sig">
      <option value="">All signatures</option>
      <option value="valid"      @selected(request('sig')==='valid')>Signature valid</option>
      <option value="invalid"    @selected(request('sig')==='invalid')>Signature invalid</option>
      <option value="unverified" @selected(request('sig')==='unverified')>Unverified (no key)</option>
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty">
    <strong>No webhooks captured yet.</strong>
    Once Authorize.Net is configured to POST to <code style="font-family:'SF Mono',Menlo,monospace;background:var(--bg-2);padding:2px 8px;border-radius:6px">/authorize-net/webhook</code>, every event lands here in real time.
  </div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Received</th>
        <th>Event</th>
        <th>Customer</th>
        <th>Entity</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Sig</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $w)
        <tr>
          <td>{{ $w->received_at?->format('M j · g:ia:s') ?? $w->created_at->format('M j · g:ia') }}</td>
          <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace;font-size:12px">{{ $w->event_type }}</span></td>
          <td>
            @if($w->customer_first_name || $w->customer_last_name)
              <span class="nm">{{ trim($w->customer_first_name . ' ' . $w->customer_last_name) }}</span>
              <span class="sub">{{ $w->customer_email }}</span>
            @else
              <span class="sub">—</span>
            @endif
          </td>
          <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $w->entity_id ?: '—' }}</span></td>
          <td>{{ $w->amount !== null ? '$' . number_format((float) $w->amount, 2) : '—' }}</td>
          <td><span style="font-size:12.5px">{{ $w->description ?: '—' }}</span></td>
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

  <div class="pager">
    <div>Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</div>
    <div class="links">{!! $rows->links('vendor.pagination.admin') !!}</div>
  </div>
@endif
@endsection
