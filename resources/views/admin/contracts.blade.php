@extends('admin.layout')
@section('title', 'Contracts')

@section('content')
<div class="admin-header">
  <div>
    <h1>Contracts</h1>
    <div class="sub">{{ $rows->total() }} total · service agreements signed after payment</div>
  </div>
</div>

@if ($pendingCount > 0 && request('status') !== 'pending')
  <div class="adm-card" style="margin-bottom:14px; border-color:#ffd8a8; background:#fffaf2;">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <div style="flex:1; min-width:260px;">
        <strong style="color:#9a5b00;">{{ $pendingCount }} client{{ $pendingCount === 1 ? '' : 's' }} paid but never signed.</strong>
        <div style="font-size:13px; color:var(--ink-2); margin-top:3px;">
          Their card was charged and the agreement was opened, but they closed the page before signing.
          Open each one and send them the link again.
        </div>
      </div>
      <a class="adm-btn" href="{{ route('admin.contracts', ['status' => 'pending']) }}">Show them</a>
    </div>
  </div>
@endif

<div class="adm-kpis" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
  <div class="adm-card" style="padding:16px 18px;"><div class="sub">Signed</div><div style="font-size:24px;font-weight:700;color:#157a3d;">{{ $signedCount }}</div></div>
  <div class="adm-card" style="padding:16px 18px;"><div class="sub">Awaiting signature</div><div style="font-size:24px;font-weight:700;color:{{ $pendingCount > 0 ? '#9a5b00' : 'inherit' }};">{{ $pendingCount }}</div></div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.contracts') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, invoice or plan" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">Any status</option>
      <option value="pending" @selected(request('status')==='pending')>Awaiting signature</option>
      <option value="signed"  @selected(request('status')==='signed')>Signed</option>
    </select>
    <select class="adm-select" name="partner">
      <option value="">Both</option>
      <option value="victoria" @selected(request('partner')==='victoria')>Victoria</option>
      <option value="burgundy" @selected(request('partner')==='burgundy')>Burgundy</option>
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No contracts match.</strong>Agreements appear here as clients sign them.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Client</th><th>Service</th><th class="nw">Price</th><th>Status</th>
        <th class="nw">Signed</th><th>Source</th><th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $c)
        <tr>
          <td>
            <a href="{{ route('admin.contracts.show', $c) }}" class="nm">{{ $c->signerName() }}</a>
            <span class="sub">{{ $c->email ?: '—' }}</span>
          </td>
          <td>
            {{ $c->plan_label }}
            @if ($c->partner === 'burgundy')<span class="badge" style="background:#fbeef2;color:#7b1e3c;margin-left:6px;">Burgundy</span>@endif
          </td>
          <td class="nw">{{ $c->priceSummary() }}</td>
          <td>
            @if ($c->isSigned())
              <span class="badge sent">signed</span>
            @else
              <span class="badge pending">awaiting</span>
            @endif
          </td>
          <td class="nw">{{ optional($c->signed_at)->format('M j, Y') ?: '—' }}</td>
          <td class="sub">{{ $c->source === 'payment_link' ? 'Payment link' : 'Checkout' }}</td>
          <td class="actions">
            <a class="adm-btn ghost" href="{{ route('admin.contracts.show', $c) }}">View</a>
            @if ($c->isSigned())
              <a class="adm-btn" href="{{ route('admin.contracts.pdf', $c) }}">PDF</a>
            @endif
          </td>
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
