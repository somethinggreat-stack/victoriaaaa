@extends('partnership.layout')
@section('title', 'Overview')
@section('subtitle', 'Where the legacy client base stands today.')

@section('content')

@if($m['needs_review'] > 0 || $m['possible_duplicates'] > 0)
  <div class="card" style="border-color:rgba(180,83,9,.3);background:var(--amber-soft)">
    <h2 style="color:var(--amber)">Needs your attention</h2>
    <p class="sub" style="color:var(--amber)">
      @if($m['needs_review'] > 0)
        {{ $m['needs_review'] }} payment{{ $m['needs_review'] === 1 ? '' : 's' }} couldn't be matched to a client automatically.
      @endif
      @if($m['possible_duplicates'] > 0)
        {{ $m['possible_duplicates'] }} record{{ $m['possible_duplicates'] === 1 ? '' : 's' }} may be the same person listed twice.
      @endif
    </p>
    <a href="{{ route('partnership.review') }}" class="btn sm">Review now</a>
  </div>
@endif

{{-- ── The pipeline ── --}}
<div class="tiles">
  <a class="tile" href="{{ route('partnership.clients') }}">
    <div class="k">Legacy Clients</div>
    <div class="v">{{ $m['legacy_clients'] }}</div>
    <div class="n">Burgundy's existing base</div>
  </a>
  <a class="tile" href="{{ route('partnership.clients', ['stage' => 'contacted']) }}">
    <div class="k">Contacted</div>
    <div class="v">{{ $m['contacted'] }}</div>
    <div class="n">Awaiting a reply</div>
  </a>
  <a class="tile" href="{{ route('partnership.clients', ['stage' => 'interested']) }}">
    <div class="k">Interested</div>
    <div class="v">{{ $m['interested'] }}</div>
    <div class="n">Wants to continue</div>
  </a>
  <a class="tile" href="{{ route('partnership.clients', ['stage' => 'payment_link_sent']) }}">
    <div class="k">Links Sent</div>
    <div class="v">{{ $m['payment_links_sent'] }}</div>
    <div class="n">Checkout link sent</div>
  </a>
  <a class="tile wine" href="{{ route('partnership.clients', ['stage' => 'paid']) }}">
    <div class="k">Paid</div>
    <div class="v">{{ $m['paid'] }}</div>
    <div class="n">{{ $m['transition_rate'] }}% of legacy base</div>
  </a>
  <a class="tile" href="{{ route('partnership.clients', ['stage' => 'onboarding_pending']) }}">
    <div class="k">Onboarding Pending</div>
    <div class="v">{{ $m['onboarding_pending'] }}</div>
    <div class="n">Paid, form not done</div>
  </a>
  <a class="tile green" href="{{ route('partnership.clients', ['stage' => 'active']) }}">
    <div class="k">Active</div>
    <div class="v">{{ $m['active'] }}</div>
    <div class="n">Onboarded &amp; running</div>
  </a>
  <a class="tile red" href="{{ route('partnership.clients', ['stage' => 'cancelled']) }}">
    <div class="k">Cancelled</div>
    <div class="v">{{ $m['cancelled'] }}</div>
    <div class="n">Subscription ended</div>
  </a>
</div>

{{-- ── Money ── --}}
<div class="tiles">
  <div class="tile green">
    <div class="k">Monthly Revenue</div>
    <div class="v">${{ number_format($m['monthly_revenue'], 0) }}</div>
    <div class="n">Active subscriptions × $100</div>
  </div>
  <a class="tile" href="{{ route('partnership.payments') }}">
    <div class="k">Payments Collected</div>
    <div class="v">${{ number_format($m['payments_collected'], 0) }}</div>
    <div class="n">All time, gross</div>
  </a>
  <a class="tile red" href="{{ route('partnership.payments', ['type' => 'failed']) }}">
    <div class="k">Failed Payments</div>
    <div class="v">{{ $m['failed_payments'] }}</div>
    <div class="n">Past due right now</div>
  </a>
  <a class="tile" href="{{ route('partnership.payments', ['type' => 'refunded']) }}">
    <div class="k">Refunds</div>
    <div class="v">${{ number_format($m['refunded'], 0) }}</div>
    <div class="n">{{ $m['refund_count'] }} refund{{ $m['refund_count'] === 1 ? '' : 's' }} / void{{ $m['refund_count'] === 1 ? '' : 's' }}</div>
  </a>
</div>

{{-- ── The link Burgundy actually sends ── --}}
<div class="card">
  <h2>The $100 checkout link</h2>
  <p class="sub">Send this to any client who wants to continue. After paying they sign the agreement, then complete onboarding — and land in both dashboards automatically.</p>
  <div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center">
    <input type="text" readonly value="{{ $checkoutUrl }}" onclick="this.select()" style="flex:1;min-width:270px" class="mono">
    <a href="{{ $checkoutUrl }}" target="_blank" rel="noopener" class="btn ghost sm">Preview</a>
  </div>
</div>

{{-- ── Recently touched ── --}}
<div class="card">
  <h2>Recent activity</h2>
  <p class="sub">The last 10 clients whose record changed.</p>

  @if($recent->isEmpty())
    <div class="empty"><strong>Nothing yet</strong>Client activity will show up here.</div>
  @else
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>Client</th><th>Stage</th><th>Payment</th><th class="hide-sm">Updated</th></tr></thead>
        <tbody>
        @foreach($recent as $c)
          <tr onclick="location.href='{{ route('partnership.clients.show', $c) }}'" style="cursor:pointer">
            <td class="wrap">
              <span class="nm">{{ $c->full_name }}</span>
              @if($c->needsReview())<span class="pill amber" style="margin-left:6px">Review</span>@endif
            </td>
            <td>@include('partnership.partials.stage-pill', ['client' => $c])</td>
            <td>@include('partnership.partials.payment-pill', ['client' => $c])</td>
            <td class="mut hide-sm">{{ $c->updated_at?->diffForHumans() }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

@endsection
