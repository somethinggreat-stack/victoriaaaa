@extends('admin.layout')
@section('title', 'Contact · ' . $contact->name)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $contact->name }}</h1>
    <div class="sub">#{{ str_pad($contact->id, 4, '0', STR_PAD_LEFT) }} · submitted {{ $contact->created_at->format('M j, Y \a\t g:ia') }} ({{ $contact->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.contacts') }}">← Back to list</a>
</div>

<!-- CONTACT INFO -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Submitter</h2>
    <form class="status-form" method="POST" action="{{ route('admin.contacts.status', $contact) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','replied','archived'] as $s)
          <option value="{{ $s }}" @selected($contact->status===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="detail-grid">
    <div class="lab">Full name</div> <div class="val">{{ $contact->name }}</div>
    <div class="lab">Email</div>     <div class="val"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></div>
    <div class="lab">Phone</div>     <div class="val">@if($contact->phone)<a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>@else — @endif</div>
  </div>
</div>

<!-- FORM DETAILS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Form selections</h2></div>
  <div class="detail-grid">
    <div class="lab">What's this about?</div>      <div class="val">{{ $contact->topic    ?: '—' }}</div>
    <div class="lab">Current credit score</div>    <div class="val">{{ $contact->score    ?: '—' }}</div>
    <div class="lab">Timeline to start</div>       <div class="val">{{ $contact->timeline ?: '—' }}</div>
    <div class="lab">How they heard about VL</div> <div class="val">{{ $contact->source   ?: '—' }}</div>
  </div>
</div>

<!-- MESSAGE -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Message</h2></div>
  <div style="white-space: pre-wrap; line-height: 1.65; font-size: 14.5px; color: var(--ink); background: var(--bg-2); padding: 16px 18px; border-radius: 12px;">{{ $contact->message }}</div>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>    <div class="val mono">{{ $contact->id }}</div>
    <div class="lab">Pipeline status</div><div class="val"><span class="badge {{ $contact->status }}">{{ $contact->status }}</span></div>
    <div class="lab">Submitted at</div>   <div class="val">{{ $contact->created_at->format('Y-m-d H:i:s') }} ({{ $contact->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>   <div class="val">{{ $contact->updated_at->format('Y-m-d H:i:s') }} ({{ $contact->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>     <div class="val mono">{{ $contact->ip ?: '—' }}</div>
    <div class="lab">User agent</div>     <div class="val" style="font-size: 12px; word-break: break-all;">{{ $contact->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px;">
  <a class="adm-btn" href="mailto:{{ $contact->email }}?subject=Re:%20your%20message%20to%20Victoria%20Love">Reply by email →</a>
</div>
@endsection
