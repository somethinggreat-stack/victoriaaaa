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

{{-- ── The links Burgundy actually sends ── --}}
<div class="card">
  <h2>Checkout links</h2>
  <p class="sub">
    Two prices are live. Send the right one — the link decides what the client is charged,
    and it is stamped onto their subscription for good.
  </p>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px">
    @foreach($plans as $tier => $p)
      <div style="border:1px solid {{ $tier === 'new' ? 'var(--wine-2)' : 'var(--line-2)' }};border-radius:var(--r);padding:16px">
        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:10px;margin-bottom:3px">
          <strong style="font-size:15px">${{ number_format((float) $p['enrollment'], 0) }} + ${{ number_format((float) $p['monthly'], 0) }}/mo</strong>
          @if($tier === 'new')<span class="pill wine">New clients</span>@else<span class="pill grey">Legacy</span>@endif
        </div>
        <div class="mut" style="font-size:12.5px;margin-bottom:11px">{{ $p['audience'] }}</div>
        <input type="text" readonly value="{{ $p['url'] }}" onclick="this.select()" class="mono" style="margin-bottom:8px">
        <div style="display:flex;gap:7px">
          <button type="button" class="btn sm" onclick="copyUrl(this)" data-url="{{ $p['url'] }}">Copy</button>
          <a href="{{ $p['url'] }}" target="_blank" rel="noopener" class="btn ghost sm">Preview</a>
        </div>
      </div>
    @endforeach
  </div>

  <p class="sub" style="margin:14px 0 0">
    After paying, the client signs the service agreement and then completes onboarding — and lands in both dashboards automatically.
  </p>
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

@push('scripts')
<script>
function copyUrl(btn){
  navigator.clipboard.writeText(btn.dataset.url).then(function(){
    var t = btn.textContent; btn.textContent = 'Copied';
    setTimeout(function(){ btn.textContent = t; }, 1400);
  });
}
</script>
@endpush
