@extends('admin.layout')
@section('title', 'Subscriptions')

@section('content')
<div class="admin-header">
  <div>
    <h1>Subscriptions</h1>
    <div class="sub">{{ $rows->total() }} customer{{ $rows->total() === 1 ? '' : 's' }} on file · every paying client across every plan</div>
  </div>
</div>

<div class="adm-stats" style="grid-template-columns: repeat(6, 1fr);">
  <div class="adm-stat">
    <div class="lab">Total customers</div>
    <div class="val">{{ number_format($kpis['total']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Active</div>
    <div class="val" style="color:#157a3d">{{ number_format($kpis['active']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Past due</div>
    <div class="val" style="color:#92400e">{{ number_format($kpis['past_due']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Terminated</div>
    <div class="val" style="color:var(--ink-3)">{{ number_format($kpis['terminated']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">MRR</div>
    <div class="val" style="color:var(--pink)">${{ number_format($kpis['mrr'], 2) }}</div>
    <div class="delta">From {{ number_format($kpis['active']) }} active recurring</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Captured today</div>
    <div class="val">${{ number_format($kpis['gross_today'], 2) }}</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.subscriptions') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, invoice, txn id, ARB id" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['active','past_due','terminated'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="plan">
      <option value="">All plans</option>
      @foreach (['audit'=>'Audit','monthly'=>'Monthly','onetime'=>'One-Time','couple'=>'Couple','vip'=>'VIP'] as $k => $lbl)
        <option value="{{ $k }}" @selected(request('plan')===$k)>{{ $lbl }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty">
    <strong>No subscriptions match.</strong>
    Adjust your filters — or wait for the first paying customer to come in through the checkout.
  </div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Customer</th>
        <th>Plan</th>
        <th>Today's charge</th>
        <th>Recurring</th>
        <th>Invoice</th>
        <th>Status</th>
        <th>Next billing</th>
        <th>Subscribed</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $s)
        <tr>
          <td>
            <a href="{{ route('admin.subscriptions.show', $s) }}" class="nm">{{ $s->first_name }} {{ $s->last_name }}</a>
            <span class="sub">{{ $s->email }}</span>
            @if($s->phone)<span class="sub">{{ $s->phone }}</span>@endif
          </td>
          <td>
            <span class="nm">{{ $s->plan_label }}</span>
            <span class="sub">{{ $s->plan_key }}</span>
          </td>
          <td>${{ number_format((float) $s->amount, 2) }}</td>
          <td>
            @if($s->recurring_amount)
              <span class="nm">${{ number_format((float) $s->recurring_amount, 2) }}/mo</span>
              @if($s->arb_subscription_id)<span class="sub">ARB {{ $s->arb_subscription_id }}</span>@endif
            @else
              <span class="sub">— one-time</span>
            @endif
          </td>
          <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $s->invoice_number }}</span></td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.subscriptions.status', $s) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['active','past_due','terminated'] as $st)
                  <option value="{{ $st }}" @selected($s->status===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>{{ $s->next_billing_date?->format('M j, Y') ?? '—' }}</td>
          <td>{{ $s->subscribed_at?->format('M j · g:ia') ?? $s->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.subscriptions.show', $s) }}">View</a></td>
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
