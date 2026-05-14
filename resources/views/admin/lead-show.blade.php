@extends('admin.layout')
@section('title', 'Popup submission · ' . ($lead->name ?: $lead->email))

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $lead->name ?: $lead->email }}</h1>
    <div class="sub">#{{ str_pad($lead->id, 4, '0', STR_PAD_LEFT) }} · captured {{ $lead->created_at->format('M j, Y \a\t g:ia') }} ({{ $lead->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.leads') }}">← Back to list</a>
</div>

<!-- CONTACT -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Submitter</h2>
    <form class="status-form" method="POST" action="{{ route('admin.leads.status', $lead) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','contacted','converted','archived'] as $s)
          <option value="{{ $s }}" @selected($lead->status===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="detail-grid">
    <div class="lab">Name</div>     <div class="val">{{ $lead->name ?: '—' }}</div>
    <div class="lab">Email</div>    <div class="val"><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></div>
    <div class="lab">Phone</div>    <div class="val">@if($lead->phone)<a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>@else — @endif</div>
    <div class="lab">Source</div>   <div class="val"><span class="badge new">{{ $lead->source }}</span></div>
  </div>
</div>

<!-- POPUP QUIZ ANSWERS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Popup quiz answers</h2></div>
  <div class="detail-grid">
    <div class="lab">Step 1 · Credit score range</div> <div class="val">{{ $lead->score ?: '—' }}</div>
    <div class="lab">Step 2 · Biggest credit issue</div><div class="val">{{ $lead->issue ?: '—' }}</div>
    <div class="lab">Step 3 · 90-day goal</div>         <div class="val">{{ $lead->goal  ?: '—' }}</div>
  </div>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>    <div class="val mono">{{ $lead->id }}</div>
    <div class="lab">Pipeline status</div><div class="val"><span class="badge {{ $lead->status }}">{{ $lead->status }}</span></div>
    <div class="lab">Captured at</div>    <div class="val">{{ $lead->created_at->format('Y-m-d H:i:s') }} ({{ $lead->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>   <div class="val">{{ $lead->updated_at->format('Y-m-d H:i:s') }} ({{ $lead->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>     <div class="val mono">{{ $lead->ip ?: '—' }}</div>
    <div class="lab">User agent</div>     <div class="val" style="font-size: 12px; word-break: break-all;">{{ $lead->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px;">
  <a class="adm-btn" href="mailto:{{ $lead->email }}?subject=About%20your%20credit%20goals">Reply by email →</a>
</div>
@endsection
