@extends('partnership.layout')
@section('title', 'Payments')
@section('subtitle', 'Every charge, refund and failure on the partnership plan.')

@section('content')

<div class="tiles">
  <div class="tile green">
    <div class="k">Collected</div>
    <div class="v">${{ number_format($m['payments_collected'], 0) }}</div>
    <div class="n">All time, gross</div>
  </div>
  <div class="tile">
    <div class="k">Net of refunds</div>
    <div class="v">${{ number_format($m['net_collected'], 0) }}</div>
    <div class="n">After ${{ number_format($m['refunded'], 0) }} refunded</div>
  </div>
  <div class="tile green">
    <div class="k">Monthly Revenue</div>
    <div class="v">${{ number_format($m['monthly_revenue'], 0) }}</div>
    <div class="n">{{ $m['active'] }} active subscription{{ $m['active'] === 1 ? '' : 's' }}</div>
  </div>
  <div class="tile red">
    <div class="k">Failed / Past Due</div>
    <div class="v">{{ $m['failed_payments'] }}</div>
    <div class="n">In the grace window</div>
  </div>
</div>

<div class="card">
  <div class="filters">
    <a href="{{ route('partnership.payments') }}" class="{{ !$type ? 'on' : '' }}">All</a>
    <a href="{{ route('partnership.payments', ['type' => 'initial']) }}" class="{{ $type === 'initial' ? 'on' : '' }}">Enrollment</a>
    <a href="{{ route('partnership.payments', ['type' => 'recurring']) }}" class="{{ $type === 'recurring' ? 'on' : '' }}">Monthly</a>
    <a href="{{ route('partnership.payments', ['type' => 'failed']) }}" class="{{ $type === 'failed' ? 'on' : '' }}">Failed</a>
    <a href="{{ route('partnership.payments', ['type' => 'refunded']) }}" class="{{ $type === 'refunded' ? 'on' : '' }}">Refunds</a>
  </div>

  @if($payments->isEmpty())
    <div class="empty">
      <strong>No payments yet</strong>
      Charges appear here automatically as clients pay.
    </div>
  @else
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Client</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Result</th>
            <th>Type</th>
            <th>Subscription</th>
            <th>Next Billing</th>
            <th>Transaction</th>
          </tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
          @php
            $client = $clients[$p->subscription_id] ?? null;
            $sub    = $p->subscription;
            $isNeg  = in_array($p->type, ['refund', 'void']);
          @endphp
          <tr>
            <td>
              @if($client)
                <a href="{{ route('partnership.clients.show', $client) }}" class="nm">{{ $client->full_name }}</a>
              @else
                <span class="nm">{{ $p->payerName() ?: 'Unlinked' }}</span>
              @endif
            </td>
            <td style="font-weight:700;{{ $isNeg ? 'color:var(--red)' : '' }}">
              {{ $isNeg ? '−' : '' }}${{ number_format((float) $p->amount, 2) }}
            </td>
            <td>{{ $p->charged_at?->format('M j, Y') ?: '—' }}</td>
            <td>
              @php $tone = match($p->status) { 'captured' => 'green', 'failed' => 'red', 'refunded','voided' => 'amber', default => 'grey' }; @endphp
              <span class="pill {{ $tone }}">{{ $p->status === 'captured' ? 'Successful' : ucfirst($p->status) }}</span>
            </td>
            <td>{{ $p->type === 'initial' ? 'Enrollment' : ucfirst($p->type) }}</td>
            <td>
              @if($sub)
                @php $st = match($sub->status) { 'active' => 'green', 'past_due' => 'red', 'terminated' => 'grey', default => 'grey' }; @endphp
                <span class="pill {{ $st }}">{{ ucfirst(str_replace('_', ' ', $sub->status)) }}</span>
              @else — @endif
            </td>
            <td class="mut">{{ $sub?->next_billing_date?->format('M j, Y') ?: '—' }}</td>
            <td class="mono mut">{{ $p->transaction_id ?: '—' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="pager">
      <div>Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }}</div>
      <div class="links">{!! $payments->links('vendor.pagination.admin') !!}</div>
    </div>
  @endif
</div>

@endsection
