@extends('admin.layout')
@section('title', 'Search')

@section('content')
<div class="admin-header">
  <div>
    <h1>Search results</h1>
    @if ($q)
      <div class="sub">
        @if ($results)
          Showing {{ $total }} {{ Str::plural('match', $total) }} for <strong>"{{ $q }}"</strong>
        @else
          Type at least 2 characters to search.
        @endif
      </div>
    @else
      <div class="sub">Search across paid clients, funding leads, contact us submissions, and popup submissions.</div>
    @endif
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.dashboard') }}">← Back to dashboard</a>
</div>

@if (!$results)
  <div class="empty"><strong>Start typing…</strong>Use the search bar at the top to find any client, lead, message, or popup answer.</div>
@elseif ($total === 0)
  <div class="empty"><strong>No matches found.</strong>Nothing matched <em>"{{ $q }}"</em> across any of the four sources.</div>
@else

  {{-- Subscriptions (paying customers) --}}
  @if (!empty($results['subscriptions']) && $results['subscriptions']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Subscriptions <span class="search-count">{{ $results['subscriptions']->count() }}</span></h2>
        <a href="{{ route('admin.subscriptions', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Customer</th><th>Plan</th><th>Amount</th><th>Invoice</th><th>Status</th><th>Subscribed</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['subscriptions'] as $s)
            <tr>
              <td><a href="{{ route('admin.subscriptions.show', $s) }}" class="nm">{{ $s->first_name }} {{ $s->last_name }}</a><span class="sub">{{ $s->email }}</span></td>
              <td>{{ $s->plan_label }}</td>
              <td>${{ number_format((float) $s->amount, 2) }}@if($s->recurring_amount) <span class="sub">+${{ number_format((float) $s->recurring_amount, 2) }}/mo</span>@endif</td>
              <td><span class="sub" style="font-family:'SF Mono',Menlo,monospace">{{ $s->invoice_number }}</span></td>
              <td><span class="badge {{ $s->status === 'active' ? 'active' : ($s->status === 'past_due' ? 'in_progress' : 'archived') }}">{{ str_replace('_',' ', $s->status) }}</span></td>
              <td>{{ ($s->subscribed_at ?? $s->created_at)->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.subscriptions.show', $s) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

  {{-- Paid clients --}}
  @if ($results['paid_clients']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Paid credit repair clients <span class="search-count">{{ $results['paid_clients']->count() }}</span></h2>
        <a href="{{ route('admin.onboarding', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Client</th><th>Contact</th><th>Location</th><th>SSN</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['paid_clients'] as $r)
            <tr>
              <td>
                <a href="{{ route('admin.onboarding.show', $r) }}" class="nm">{{ $r->firstname }} {{ $r->lastname }}</a>
                <span class="sub">#{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</span>
              </td>
              <td><span class="nm">{{ $r->email }}</span><span class="sub">{{ $r->phone }}</span></td>
              <td>{{ $r->city ?: '—' }}@if($r->state), {{ $r->state }}@endif</td>
              <td><code>{{ $r->masked_ssn }}</code></td>
              <td>{{ $r->created_at->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.onboarding.show', $r) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

  {{-- Funding leads --}}
  @if ($results['funding_leads']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Funding leads <span class="search-count">{{ $results['funding_leads']->count() }}</span></h2>
        <a href="{{ route('admin.funding', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Applicant</th><th>Contact</th><th>Goal</th><th>FICO</th><th>Income</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['funding_leads'] as $f)
            <tr>
              <td>
                <a href="{{ route('admin.funding.show', $f) }}" class="nm">{{ $f->first_name }} {{ $f->last_name }}</a>
                <span class="sub">#{{ str_pad($f->id, 4, '0', STR_PAD_LEFT) }}</span>
              </td>
              <td><span class="nm">{{ $f->email }}</span><span class="sub">{{ $f->phone }}</span></td>
              <td>{{ $f->amount ?: '—' }}</td>
              <td>{{ $f->fico ?: '—' }}</td>
              <td>{{ $f->income ?: '—' }}</td>
              <td>{{ $f->created_at->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.funding.show', $f) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

  {{-- Mentorship leads --}}
  @if ($results['mentorship_leads']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Mentorship leads <span class="search-count">{{ $results['mentorship_leads']->count() }}</span></h2>
        <a href="{{ route('admin.mentorship', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Lead</th><th>Contact</th><th>Situation</th><th>Timeline</th><th>Investment</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['mentorship_leads'] as $m)
            <tr>
              <td>
                <a href="{{ route('admin.mentorship.show', $m) }}" class="nm">{{ $m->first_name }} {{ $m->last_name }}</a>
                <span class="sub">#{{ str_pad($m->id, 4, '0', STR_PAD_LEFT) }}</span>
              </td>
              <td><span class="nm">{{ $m->email }}</span><span class="sub">{{ $m->phone }}</span></td>
              <td>{{ $m->situation ?: '—' }}</td>
              <td>{{ $m->timeline  ?: '—' }}</td>
              <td>{{ $m->investment?: '—' }}</td>
              <td>{{ $m->created_at->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.mentorship.show', $m) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

  {{-- Contact us --}}
  @if ($results['contacts']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Contact us submissions <span class="search-count">{{ $results['contacts']->count() }}</span></h2>
        <a href="{{ route('admin.contacts', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>From</th><th>Topic</th><th>Message preview</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['contacts'] as $c)
            <tr>
              <td>
                <a href="{{ route('admin.contacts.show', $c) }}" class="nm">{{ $c->name }}</a>
                <span class="sub">{{ $c->email }}</span>
              </td>
              <td>{{ $c->topic ?: '—' }}</td>
              <td style="max-width: 360px;">{{ Str::limit($c->message, 90) }}</td>
              <td>{{ $c->created_at->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.contacts.show', $c) }}">Open</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

  {{-- Popup submissions --}}
  @if ($results['leads']->isNotEmpty())
    <div class="adm-card" style="margin-bottom: 16px;">
      <div class="adm-card-head">
        <h2>Popup submissions <span class="search-count">{{ $results['leads']->count() }}</span></h2>
        <a href="{{ route('admin.leads', ['q' => $q]) }}">View in section →</a>
      </div>
      <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Lead</th><th>Score</th><th>Issue</th><th>Goal</th><th>Captured</th><th></th></tr></thead>
        <tbody>
          @foreach ($results['leads'] as $l)
            <tr>
              <td>
                <a href="{{ route('admin.leads.show', $l) }}" class="nm">{{ $l->name ?: $l->email }}</a>
                <span class="sub">{{ $l->email }}</span>
              </td>
              <td>{{ $l->score ?: '—' }}</td>
              <td>{{ $l->issue ?: '—' }}</td>
              <td>{{ $l->goal ?: '—' }}</td>
              <td>{{ $l->created_at->format('M j · g:ia') }}</td>
              <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.leads.show', $l) }}">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  @endif

@endif

<style>
  .search-count {
    display: inline-block; margin-left: 6px;
    font-size: 11px; font-weight: 700; letter-spacing: 0.04em;
    color: var(--pink); background: var(--pink-soft);
    padding: 2px 8px; border-radius: 100px;
  }
</style>
@endsection
