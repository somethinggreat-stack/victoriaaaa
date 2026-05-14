@extends('admin.layout')
@section('title', 'Payments')

@section('content')
<div class="admin-header">
  <div>
    <h1>Payments</h1>
    <div class="sub">{{ $rows->total() }} transaction{{ $rows->total() === 1 ? '' : 's' }} on file · captures, refunds, and voids</div>
  </div>
</div>

<div class="adm-stats" style="grid-template-columns: repeat(6, 1fr);">
  <div class="adm-stat">
    <div class="lab">Gross lifetime</div>
    <div class="val" style="color:#157a3d">${{ number_format($kpis['gross_lifetime'], 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Refunded / voided</div>
    <div class="val" style="color:#991b1b">${{ number_format($kpis['refunded_lifetime'], 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Today</div>
    <div class="val">${{ number_format($kpis['gross_today'], 2) }}</div>
    <div class="delta">{{ $kpis['count_today'] }} transaction(s)</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Month to date</div>
    <div class="val">${{ number_format($kpis['gross_mtd'], 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Total count</div>
    <div class="val">{{ number_format($kpis['count_total']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Net lifetime</div>
    <div class="val" style="color:var(--pink)">${{ number_format($kpis['gross_lifetime'] - $kpis['refunded_lifetime'], 2) }}</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.payments') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search txn id, invoice, or customer email/name" value="{{ request('q') }}">
    <select class="adm-select" name="type">
      <option value="">All types</option>
      @foreach (['initial','recurring','refund','void'] as $t)
        <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst($t) }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['captured','refunded','voided','failed'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No payments match.</strong>Adjust filters or wait for the next charge.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Charged</th>
        <th>Customer</th>
        <th>Plan</th>
        <th>Type</th>
        <th>Status</th>
        <th>Amount</th>
        <th>Transaction ID</th>
        <th>Invoice</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $p)
        <tr>
          <td>{{ $p->charged_at?->format('M j, Y · g:ia') ?? $p->created_at->format('M j · g:ia') }}</td>
          <td>
            @if($p->subscription)
              <a href="{{ route('admin.subscriptions.show', $p->subscription) }}" class="nm">{{ $p->subscription->first_name }} {{ $p->subscription->last_name }}</a>
              <span class="sub">{{ $p->subscription->email }}</span>
            @else
              <span class="sub">Unlinked</span>
            @endif
          </td>
          <td>{{ $p->subscription?->plan_label ?? '—' }}</td>
          <td><span class="badge {{ in_array($p->type,['initial','recurring'])?'active':'failed' }}">{{ $p->type }}</span></td>
          <td><span class="badge {{ $p->status === 'captured' ? 'active' : 'failed' }}">{{ $p->status }}</span></td>
          <td><strong>{{ in_array($p->type, ['refund','void']) ? '−' : '' }}${{ number_format((float) $p->amount, 2) }}</strong></td>
          <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $p->transaction_id ?: '—' }}</span></td>
          <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $p->invoice_number ?: '—' }}</span></td>
          <td class="actions">
            @if($p->subscription)
              <a class="adm-btn ghost" href="{{ route('admin.subscriptions.show', $p->subscription) }}">View customer</a>
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
