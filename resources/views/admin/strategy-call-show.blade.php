@extends('admin.layout')
@section('title', 'Strategy call · ' . $row->name)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $row->name }}</h1>
    <div class="sub">#{{ str_pad($row->id, 4, '0', STR_PAD_LEFT) }} · submitted {{ $row->created_at->format('M j, Y \a\t g:ia') }} ({{ $row->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.strategy-calls') }}">← Back to list</a>
</div>

<!-- CONTACT -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Contact details</h2>
    <form class="status-form" method="POST" action="{{ route('admin.strategy-calls.status', $row) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','booked','showed','no_show','converted','archived'] as $s)
          <option value="{{ $s }}" @selected($row->status===$s)>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="detail-grid">
    <div class="lab">Name</div>              <div class="val">{{ $row->name ?: '—' }}</div>
    <div class="lab">Email</div>             <div class="val"><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></div>
    <div class="lab">Phone</div>             <div class="val"><a href="tel:{{ $row->phone }}">{{ $row->phone }}</a></div>
    <div class="lab">Best time to reach</div><div class="val">{{ $row->best_time ?: '—' }}</div>
  </div>
</div>

<!-- QUALIFYING ANSWERS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Qualifying answers</h2></div>
  <div class="detail-grid">
    <div class="lab">Current situation</div>    <div class="val">{{ $row->situation ?: '—' }}</div>
    <div class="lab">Credit score band</div>    <div class="val">{{ $row->score ?: '—' }}</div>
    <div class="lab">Start timeline</div>       <div class="val">{{ $row->timeline ?: '—' }}</div>
    <div class="lab">Investment range</div>     <div class="val">{{ $row->investment_range ?: '—' }}</div>
    <div class="lab">Prior credit repair</div>  <div class="val">{{ $row->prior_repair ? 'Yes' : 'No' }}</div>
    <div class="lab">Prior repair notes</div>   <div class="val">{{ $row->prior_repair_notes ?: '—' }}</div>
    <div class="lab">90-day goal</div>          <div class="val">{{ $row->goal ?: '—' }}</div>
  </div>
</div>

<!-- MONITORING ACCESS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Monitoring access (bring to the call)</h2></div>
  <div class="detail-grid">
    <div class="lab">Service</div>                <div class="val">{{ $row->monitoring_service ?: '—' }}</div>
    <div class="lab">Username / email</div>       <div class="val">{{ $row->monitoring_username ?: '—' }}</div>
    <div class="lab">Confirmed bring login</div>  <div class="val">{{ $row->will_bring_login ? '✓ Yes' : '—' }}</div>
    <div class="lab">Confirmed show-up</div>      <div class="val">{{ $row->showup_confirmed ? '✓ Yes' : '—' }}</div>
  </div>
  <p style="margin: 14px 0 0; font-size: 12px; color: var(--ink-3);">
    Password is <strong>never</strong> stored in the database — the client brings it live to the Zoom call and logs in for you.
  </p>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>     <div class="val mono">{{ $row->id }}</div>
    <div class="lab">Pipeline status</div> <div class="val"><span class="badge {{ $row->status }}">{{ ucfirst(str_replace('_',' ',$row->status)) }}</span></div>
    <div class="lab">Submitted at</div>    <div class="val">{{ $row->created_at->format('Y-m-d H:i:s') }} ({{ $row->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>    <div class="val">{{ $row->updated_at->format('Y-m-d H:i:s') }} ({{ $row->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>      <div class="val mono">{{ $row->ip ?: '—' }}</div>
    <div class="lab">User agent</div>      <div class="val" style="font-size: 12px; word-break: break-all;">{{ $row->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
  <a class="adm-btn" href="mailto:{{ $row->email }}?subject=Your%20strategy%20call%20with%20Victoria%20Love">Reply by email →</a>
  <a class="adm-btn ghost" href="tel:{{ $row->phone }}">Call {{ $row->phone }}</a>
</div>
@endsection
