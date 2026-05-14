@extends('admin.layout')
@section('title', 'eBook Sales')

@section('content')
<div class="admin-header">
  <div>
    <h1>eBook Sales</h1>
    <div class="sub">{{ $rows->total() }} order{{ $rows->total() === 1 ? '' : 's' }} on file · all paid downloads across the digital library.</div>
  </div>
  <a href="{{ route('admin.ebooks') }}" class="adm-btn ghost">← Manage catalog</a>
</div>

<div class="adm-stats" style="grid-template-columns: repeat(5, 1fr);">
  <div class="adm-stat">
    <div class="lab">Lifetime revenue</div>
    <div class="val" style="color:#157a3d">${{ number_format($kpis['paid_lifetime'], 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Today</div>
    <div class="val">${{ number_format($kpis['today_amount'], 2) }}</div>
    <div class="delta">{{ $kpis['today_count'] }} order(s)</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Month to date</div>
    <div class="val">${{ number_format($kpis['mtd_amount'], 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Orders total</div>
    <div class="val">{{ number_format($kpis['total_orders']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Avg. order</div>
    <div class="val" style="color:var(--pink)">${{ $kpis['total_orders'] > 0 ? number_format($kpis['paid_lifetime'] / $kpis['total_orders'], 2) : '0.00' }}</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.ebook-orders') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search invoice, transaction id, or customer email/name"
           value="{{ request('q') }}">
    <select class="adm-select" name="ebook">
      <option value="">All eBooks</option>
      @foreach($ebooks as $eb)
        <option value="{{ $eb->slug }}" @selected(request('ebook')===$eb->slug)>{{ $eb->title }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['paid','refunded','failed'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No ebook sales yet.</strong>Once a buyer completes checkout, their order will appear here.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Charged</th>
        <th>Customer</th>
        <th>eBook</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Downloaded</th>
        <th>Invoice</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $o)
        <tr>
          <td>{{ $o->charged_at?->format('M j, Y · g:ia') ?? $o->created_at->format('M j · g:ia') }}</td>
          <td>
            <a href="{{ route('admin.ebook-orders.show', $o) }}" class="nm">{{ $o->fullName() }}</a>
            <span class="sub">{{ $o->email }}</span>
          </td>
          <td>{{ $o->ebook_title }}</td>
          <td><strong>${{ number_format((float) $o->amount, 2) }}</strong></td>
          <td><span class="badge {{ $o->status === 'paid' ? 'active' : 'failed' }}">{{ $o->status }}</span></td>
          <td>
            @if($o->downloaded_at)
              <span style="color:#157a3d;font-weight:600">✓ {{ $o->downloaded_at->diffForHumans() }}</span>
              @if($o->download_count > 1)
                <span class="sub">{{ $o->download_count }}× total</span>
              @endif
            @else
              <span class="sub">— not yet</span>
            @endif
          </td>
          <td><span style="font-family:'SF Mono',Menlo,monospace;font-size:11.5px">{{ $o->invoice_number }}</span></td>
          <td class="actions">
            <a href="{{ route('admin.ebook-orders.show', $o) }}" class="adm-btn ghost">View</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table></div>

  <div class="pager">
    <div>Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</div>
    <div class="links">{!! $rows->links() !!}</div>
  </div>
@endif

@endsection
