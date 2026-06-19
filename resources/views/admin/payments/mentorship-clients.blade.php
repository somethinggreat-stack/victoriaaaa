@extends('admin.layout')
@section('title', 'Paid Mentorship Clients')

@section('content')
<div class="admin-header">
  <div>
    <h1>Paid Mentorship Clients</h1>
    <div class="sub">{{ $rows->total() }} client{{ $rows->total() === 1 ? '' : 's' }} who purchased a 1:1 mentorship plan · contact details on file (card data is never stored)</div>
  </div>
</div>

<div class="adm-stats" style="grid-template-columns: repeat(5, 1fr);">
  <div class="adm-stat">
    <div class="lab">Total clients</div>
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
    <div class="lab">Deposits collected</div>
    <div class="val" style="color:var(--pink)">${{ number_format($kpis['deposits'], 2) }}</div>
    <div class="delta">Initial charges across all plans</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Monthly installments</div>
    <div class="val">${{ number_format($kpis['plan_installments'], 2) }}</div>
    <div class="delta">Sum of recurring amounts</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.mentorship-clients') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, address, invoice" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['active','past_due','terminated'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="plan">
      <option value="">All mentorship plans</option>
      @foreach ([
        'mentorship-2pay'=>'Deposit + 2 Payments',
        'mentorship-3pay'=>'Deposit + 3 Payments',
        'mentorship-5pay'=>'Deposit + 5 Payments',
        'mentorship-full'=>'Pay in Full',
      ] as $k => $lbl)
        <option value="{{ $k }}" @selected(request('plan')===$k)>{{ $lbl }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty">
    <strong>No mentorship clients yet.</strong>
    As soon as someone checks out on a mentorship payment plan, their details will appear here.
  </div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Address</th>
        <th>Plan</th>
        <th>Deposit</th>
        <th>Installments</th>
        <th>Status</th>
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
            @if($s->address)
              <span class="sub">{{ $s->address }}</span>
              <span class="sub">{{ trim($s->city . ', ' . $s->state . ' ' . $s->zip, ', ') }}</span>
            @else
              <span class="sub">—</span>
            @endif
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
              <span class="sub">— paid in full</span>
            @endif
          </td>
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
