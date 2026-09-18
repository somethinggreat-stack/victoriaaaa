@extends('partnership.layout')
@section('title', 'Clients')
@section('subtitle', $clients->total() . ' client' . ($clients->total() === 1 ? '' : 's') . ' in the pipeline')

@section('actions')
  <button type="button" class="btn" onclick="document.getElementById('addBox').style.display='block';window.scrollTo(0,0)">+ Add client</button>
@endsection

@section('content')

<div class="card" id="addBox" style="display:none">
  <h2>Add a client</h2>
  <p class="sub">For someone Burgundy brings in outside the legacy list.</p>
  <form method="POST" action="{{ route('partnership.clients.store') }}">
    @csrf
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px">
      <div class="field"><label class="fl">First name</label><input type="text" name="first_name" required></div>
      <div class="field"><label class="fl">Last name</label><input type="text" name="last_name" required></div>
      <div class="field"><label class="fl">Email</label><input type="email" name="email"></div>
      <div class="field"><label class="fl">Phone</label><input type="text" name="phone"></div>
    </div>
    <div class="field"><label class="fl">Notes</label><textarea name="notes" rows="2"></textarea></div>
    <button type="submit" class="btn">Add client</button>
    <button type="button" class="btn ghost" onclick="document.getElementById('addBox').style.display='none'">Cancel</button>
  </form>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:9px;flex-wrap:wrap;margin-bottom:16px">
    @if($stage)<input type="hidden" name="stage" value="{{ $stage }}">@endif
    <input type="search" name="q" value="{{ $search }}" placeholder="Search name, email or phone…" style="flex:1;min-width:230px">
    <button type="submit" class="btn">Search</button>
    @if($search || $stage)<a href="{{ route('partnership.clients') }}" class="btn ghost">Clear</a>@endif
  </form>

  @php $base = array_filter(['q' => $search]); @endphp
  <div class="filters">
    <a href="{{ route('partnership.clients', $base) }}" class="{{ !$stage ? 'on' : '' }}">All</a>
    @foreach($statuses as $key => $label)
      <a href="{{ route('partnership.clients', $base + ['stage' => $key]) }}" class="{{ $stage === $key ? 'on' : '' }}">{{ $label }}</a>
    @endforeach
    <a href="{{ route('partnership.clients', $base + ['stage' => 'paid']) }}" class="{{ $stage === 'paid' ? 'on' : '' }}">Paid</a>
    <a href="{{ route('partnership.clients', $base + ['stage' => 'onboarding_pending']) }}" class="{{ $stage === 'onboarding_pending' ? 'on' : '' }}">Onboarding Pending</a>
    <a href="{{ route('partnership.clients', $base + ['stage' => 'active']) }}" class="{{ $stage === 'active' ? 'on' : '' }}">Active</a>
    <a href="{{ route('partnership.clients', $base + ['stage' => 'cancelled']) }}" class="{{ $stage === 'cancelled' ? 'on' : '' }}">Cancelled</a>
    <a href="{{ route('partnership.clients', $base + ['stage' => 'needs_review']) }}" class="{{ $stage === 'needs_review' ? 'on' : '' }}">Needs Review</a>
  </div>

  @if($clients->isEmpty())
    <div class="empty">
      <strong>No clients match</strong>
      Try a different search or filter.
    </div>
  @else
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Contact Status</th>
            <th>Payment</th>
            <th>Amount</th>
            <th>Subscription</th>
            <th>Next Payment</th>
            <th>Onboarding</th>
            <th>Client Status</th>
          </tr>
        </thead>
        <tbody>
        @foreach($clients as $c)
          @php $sub = $c->subscription; @endphp
          <tr>
            <td>
              <a href="{{ route('partnership.clients.show', $c) }}" class="nm">{{ $c->full_name }}</a>
              @if($c->needsReview())<span class="pill amber" style="margin-left:6px">Review</span>@endif
              @if($c->possible_duplicate_of)<span class="pill amber" style="margin-left:6px">Dup?</span>@endif
              @if($c->source === 'new')<span class="pill blue" style="margin-left:6px">New</span>@endif
            </td>
            <td class="mono">{{ $c->phone ?: '—' }}</td>
            <td class="mono" style="max-width:190px;overflow:hidden;text-overflow:ellipsis">{{ $c->email ?: '—' }}</td>
            <td>
              <form method="POST" action="{{ route('partnership.clients.update', $c) }}">
                @csrf @method('PATCH')
                <select name="contact_status" onchange="this.form.submit()" style="font-size:12px;padding:5px 8px">
                  @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected($c->contact_status === $key)>{{ $label }}</option>
                  @endforeach
                </select>
              </form>
            </td>
            <td>@include('partnership.partials.payment-pill', ['client' => $c])</td>
            <td>{{ $sub ? '$' . number_format((float) $sub->amount, 0) : '—' }}</td>
            <td>
              @if($sub)
                @php $tone = match($sub->status) { 'active' => 'green', 'past_due' => 'red', 'terminated' => 'grey', default => 'grey' }; @endphp
                <span class="pill {{ $tone }}">{{ ucfirst(str_replace('_', ' ', $sub->status)) }}</span>
              @else — @endif
            </td>
            <td class="mut">{{ $c->nextBillingDate()?->format('M j, Y') ?: '—' }}</td>
            <td>
              @if($c->onboarding_status === 'complete')
                <span class="pill green">Complete</span>
              @elseif($c->hasPaid())
                <span class="pill amber">Pending</span>
              @else — @endif
            </td>
            <td>
              @php $tone = match($c->client_status) { 'active' => 'green', 'cancelled' => 'red', default => 'grey' }; @endphp
              <span class="pill {{ $tone }}">{{ ucfirst($c->client_status) }}</span>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="pager">
      <div>Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }}</div>
      <div class="links">{!! $clients->links('vendor.pagination.admin') !!}</div>
    </div>
  @endif
</div>

@endsection
