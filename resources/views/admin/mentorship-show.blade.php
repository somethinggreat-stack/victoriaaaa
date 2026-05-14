@extends('admin.layout')
@section('title', 'Mentorship · ' . $mentorship->first_name . ' ' . $mentorship->last_name)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $mentorship->full_name }}</h1>
    <div class="sub">#{{ str_pad($mentorship->id, 4, '0', STR_PAD_LEFT) }} · submitted {{ $mentorship->created_at->format('M j, Y \a\t g:ia') }} ({{ $mentorship->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.mentorship') }}">← Back to list</a>
</div>

<!-- CONTACT -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Contact details</h2>
    <form class="status-form" method="POST" action="{{ route('admin.mentorship.status', $mentorship) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','contacted','qualified','enrolled','archived'] as $s)
          <option value="{{ $s }}" @selected($mentorship->status===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="detail-grid">
    <div class="lab">First name</div>  <div class="val">{{ $mentorship->first_name ?: '—' }}</div>
    <div class="lab">Last name</div>   <div class="val">{{ $mentorship->last_name  ?: '—' }}</div>
    <div class="lab">Email</div>       <div class="val"><a href="mailto:{{ $mentorship->email }}">{{ $mentorship->email }}</a></div>
    <div class="lab">Phone</div>       <div class="val"><a href="tel:{{ $mentorship->phone }}">{{ $mentorship->phone }}</a></div>
  </div>
</div>

<!-- QUALIFYING ANSWERS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Qualifying answers</h2></div>
  <div class="detail-grid">
    <div class="lab">Step 1 · Current situation</div>     <div class="val">{{ $mentorship->situation  ?: '—' }}</div>
    <div class="lab">Step 2 · Start timeline</div>        <div class="val">{{ $mentorship->timeline   ?: '—' }}</div>
    <div class="lab">Step 3 · Weekly time commitment</div><div class="val">{{ $mentorship->hours      ?: '—' }}</div>
    <div class="lab">Step 4 · Investment readiness</div>  <div class="val">{{ $mentorship->investment ?: '—' }}</div>
  </div>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>    <div class="val mono">{{ $mentorship->id }}</div>
    <div class="lab">Pipeline status</div><div class="val"><span class="badge {{ $mentorship->status }}">{{ $mentorship->status }}</span></div>
    <div class="lab">Submitted at</div>   <div class="val">{{ $mentorship->created_at->format('Y-m-d H:i:s') }} ({{ $mentorship->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>   <div class="val">{{ $mentorship->updated_at->format('Y-m-d H:i:s') }} ({{ $mentorship->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>     <div class="val mono">{{ $mentorship->ip ?: '—' }}</div>
    <div class="lab">User agent</div>     <div class="val" style="font-size: 12px; word-break: break-all;">{{ $mentorship->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px;">
  <a class="adm-btn" href="mailto:{{ $mentorship->email }}?subject=Your%20mentorship%20application%20with%20Victoria%20Love">Reply by email →</a>
</div>
@endsection
