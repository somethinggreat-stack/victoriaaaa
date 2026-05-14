@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="adm-hero">
  <div class="adm-hero-portrait">
    <img src="{{ asset('images/founderimage1.jpeg') }}" alt="Victoria Love" />
    <span class="adm-hero-online"></span>
  </div>
  <div class="adm-hero-text">
    <span class="adm-hero-eye">Welcome back · {{ now()->format('l, M j, Y') }}</span>
    <h1>{{ auth()->user()->name }} <em class="serif">Love.</em></h1>
    <p>Texas Realtor &amp; Credit Coach · Founder, Victorious Opportunities.</p>
    <p class="sub">Everything that came through your four forms — paid clients, funding leads, contact us, popup — lives below.</p>
  </div>
  <div class="adm-hero-summary">
    <div class="adm-hero-stat">
      <strong>{{ number_format($counts['onboarding'] + $counts['funding'] + $counts['contacts'] + $counts['leads']) }}</strong>
      <span>Total submissions</span>
    </div>
    <div class="adm-hero-stat alt">
      <strong>{{ number_format($counts['onboarding_today'] + $counts['funding_today'] + $counts['contacts_today'] + $counts['leads_today']) }}</strong>
      <span>Today</span>
    </div>
  </div>
</div>

<!-- ════════ REVENUE ROW ════════ -->
<div class="adm-stats" style="grid-template-columns: repeat(6, 1fr);">
  <a class="adm-stat link" href="{{ route('admin.payments') }}">
    <div class="lab">Gross lifetime</div>
    <div class="val" style="color:#157a3d">${{ number_format($payments['gross_lifetime'], 2) }}</div>
    <div class="delta">Captured · before refunds</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.payments', ['type'=>'refund']) }}">
    <div class="lab">Refunded / voided</div>
    <div class="val" style="color:#991b1b">${{ number_format($payments['refunded_lifetime'], 2) }}</div>
    <div class="delta">Lifetime</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.payments') }}">
    <div class="lab">Today</div>
    <div class="val">${{ number_format($payments['gross_today'], 2) }}</div>
    <div class="delta">Captured today</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.payments') }}">
    <div class="lab">Month to date</div>
    <div class="val">${{ number_format($payments['gross_mtd'], 2) }}</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.subscriptions', ['status'=>'active']) }}">
    <div class="lab">MRR (active recurring)</div>
    <div class="val" style="color:var(--pink)">${{ number_format($payments['mrr'], 2) }}</div>
    <div class="delta"><strong>{{ $payments['subs_active'] }}</strong> active · {{ $payments['subs_past_due'] }} past due</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.webhooks') }}">
    <div class="lab">Webhooks captured</div>
    <div class="val">{{ number_format($payments['webhooks_total']) }}</div>
    <div class="delta"><strong>+{{ $payments['webhooks_today'] }}</strong> today @if($payments['webhooks_invalid'])· <span style="color:#991b1b">{{ $payments['webhooks_invalid'] }} invalid sig</span>@endif</div>
  </a>
</div>

<!-- Top stat row — one card per submission source -->
<div class="adm-stats">
  <a class="adm-stat link" href="{{ route('admin.onboarding') }}">
    <div class="lab">Paid Credit Repair Clients</div>
    <div class="val">{{ number_format($counts['onboarding']) }}</div>
    <div class="delta"><strong>+{{ $counts['onboarding_today'] }}</strong> today · {{ $counts['new_onboarding'] }} new</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.funding') }}">
    <div class="lab">Funding Leads</div>
    <div class="val">{{ number_format($counts['funding']) }}</div>
    <div class="delta"><strong>+{{ $counts['funding_today'] }}</strong> today · {{ $counts['new_funding'] }} new</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.mentorship') }}">
    <div class="lab">Mentorship Leads</div>
    <div class="val">{{ number_format($counts['mentorship']) }}</div>
    <div class="delta"><strong>+{{ $counts['mentorship_today'] }}</strong> today · {{ $counts['new_mentorship'] }} new</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.contacts') }}">
    <div class="lab">Contact Us Submissions</div>
    <div class="val">{{ number_format($counts['contacts']) }}</div>
    <div class="delta"><strong>+{{ $counts['contacts_today'] }}</strong> today · {{ $counts['new_contacts'] }} new</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.leads') }}">
    <div class="lab">Popup Submissions</div>
    <div class="val">{{ number_format($counts['leads']) }}</div>
    <div class="delta"><strong>+{{ $counts['leads_today'] }}</strong> today · {{ $counts['new_leads'] }} new</div>
  </a>
</div>

<!-- ════════ LATEST PAYMENT ACTIVITY ════════ -->
<div class="adm-grid-2" style="margin-bottom:16px">

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest paying customers</h2><a href="{{ route('admin.subscriptions') }}">View all →</a></div>
    @if ($recentSubscriptions->isEmpty())
      <div class="empty"><strong>No customers yet</strong>The first paid checkout will land here in real time.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentSubscriptions as $s)
          <div class="row">
            <div>
              <a href="{{ route('admin.subscriptions.show', $s) }}" class="nm">{{ $s->first_name }} {{ $s->last_name }}</a>
              <div class="em">{{ $s->plan_label }} · ${{ number_format((float) $s->amount, 2) }} @if($s->recurring_amount) + ${{ number_format((float) $s->recurring_amount, 2) }}/mo @endif</div>
            </div>
            <time>{{ ($s->subscribed_at ?? $s->created_at)->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest webhook events</h2><a href="{{ route('admin.webhooks') }}">View all →</a></div>
    @if ($recentWebhooks->isEmpty())
      <div class="empty"><strong>No webhooks yet</strong>Configure Authorize.Net to POST events to /authorize-net/webhook.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentWebhooks as $w)
          <div class="row">
            <div>
              <a href="{{ route('admin.webhooks.show', $w) }}" class="nm">{{ $w->description ?: $w->event_type }}</a>
              <div class="em" style="font-family:'SF Mono',Menlo,monospace;font-size:11.5px">{{ $w->event_type }}@if($w->amount !== null) · ${{ number_format((float) $w->amount, 2) }}@endif</div>
            </div>
            <time>{{ ($w->received_at ?? $w->created_at)->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

<!-- Recent activity per source -->
<div class="adm-grid-2">

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest paid credit repair clients</h2><a href="{{ route('admin.onboarding') }}">View all →</a></div>
    @if ($recentOnboarding->isEmpty())
      <div class="empty"><strong>Nothing yet</strong>No onboarding submissions yet.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentOnboarding as $r)
          <div class="row">
            <div>
              <a href="{{ route('admin.onboarding.show', $r) }}" class="nm">{{ $r->firstname }} {{ $r->lastname }}</a>
              <div class="em">{{ $r->email }} · {{ $r->phone }}</div>
            </div>
            <time>{{ $r->created_at->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest funding leads</h2><a href="{{ route('admin.funding') }}">View all →</a></div>
    @if ($recentFunding->isEmpty())
      <div class="empty"><strong>Nothing yet</strong>No funding applications yet.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentFunding as $f)
          <div class="row">
            <div>
              <a href="{{ route('admin.funding.show', $f) }}" class="nm">{{ $f->first_name }} {{ $f->last_name }}</a>
              <div class="em">{{ $f->email }} · {{ $f->amount ?: '—' }} · FICO {{ $f->fico ?: '—' }}</div>
            </div>
            <time>{{ $f->created_at->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest mentorship leads</h2><a href="{{ route('admin.mentorship') }}">View all →</a></div>
    @if ($recentMentorship->isEmpty())
      <div class="empty"><strong>Nothing yet</strong>No mentorship leads yet.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentMentorship as $m)
          <div class="row">
            <div>
              <a href="{{ route('admin.mentorship.show', $m) }}" class="nm">{{ $m->first_name }} {{ $m->last_name }}</a>
              <div class="em">{{ $m->email }} · {{ $m->situation ?: '—' }}</div>
            </div>
            <time>{{ $m->created_at->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest Contact Us submissions</h2><a href="{{ route('admin.contacts') }}">View all →</a></div>
    @if ($recentContacts->isEmpty())
      <div class="empty"><strong>Nothing yet</strong>No contact messages yet.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentContacts as $c)
          <div class="row">
            <div>
              <a href="{{ route('admin.contacts.show', $c) }}" class="nm">{{ $c->name }}</a>
              <div class="em">{{ $c->email }} · {{ Str::limit($c->topic ?? '—', 30) }}</div>
            </div>
            <time>{{ $c->created_at->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="adm-card">
    <div class="adm-card-head"><h2>Latest popup submissions</h2><a href="{{ route('admin.leads') }}">View all →</a></div>
    @if ($recentLeads->isEmpty())
      <div class="empty"><strong>Nothing yet</strong>No popup leads yet.</div>
    @else
      <div class="adm-mini-list">
        @foreach ($recentLeads as $l)
          <div class="row">
            <div>
              <a href="{{ route('admin.leads.show', $l) }}" class="nm">{{ $l->name ?: $l->email }}</a>
              <div class="em">{{ $l->email }} · {{ $l->score ?: '—' }} · {{ $l->goal ?: '—' }}</div>
            </div>
            <time>{{ $l->created_at->diffForHumans() }}</time>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>
@endsection
