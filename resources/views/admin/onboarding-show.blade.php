@extends('admin.layout')
@section('title', 'Paid client · ' . $onboarding->firstname . ' ' . $onboarding->lastname)

@php
  // Format the full 9-digit SSN as XXX-XX-XXXX for readability
  $fullSsn = $onboarding->ssn;
  $fullSsnFormatted = ($fullSsn && strlen($fullSsn) === 9)
    ? substr($fullSsn, 0, 3) . '-' . substr($fullSsn, 3, 2) . '-' . substr($fullSsn, 5, 4)
    : ($fullSsn ?: '—');
@endphp

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $onboarding->firstname }} {{ $onboarding->lastname }}</h1>
    <div class="sub">#{{ str_pad($onboarding->id, 4, '0', STR_PAD_LEFT) }} · submitted {{ $onboarding->created_at->format('M j, Y \a\t g:ia') }} ({{ $onboarding->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.onboarding') }}">← Back to list</a>
</div>

<!-- IDENTITY -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Identity</h2>
    <form class="status-form" method="POST" action="{{ route('admin.onboarding.status', $onboarding) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','in_progress','active','archived'] as $s)
          <option value="{{ $s }}" @selected($onboarding->status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
        @endforeach
      </select>
    </form>
  </div>

  <div class="detail-grid">
    <div class="lab">First name</div>   <div class="val">{{ $onboarding->firstname ?: '—' }}</div>
    <div class="lab">Middle name</div>  <div class="val">{{ $onboarding->middlename ?: '—' }}</div>
    <div class="lab">Last name</div>    <div class="val">{{ $onboarding->lastname ?: '—' }}</div>
    <div class="lab">Suffix</div>       <div class="val">{{ $onboarding->suffix ?: '—' }}</div>
  </div>
</div>

<!-- CONTACT -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Contact</h2></div>
  <div class="detail-grid">
    <div class="lab">Email</div>  <div class="val"><a href="mailto:{{ $onboarding->email }}">{{ $onboarding->email }}</a></div>
    <div class="lab">Phone</div>  <div class="val"><a href="tel:{{ $onboarding->phone }}">{{ $onboarding->phone }}</a></div>
  </div>
</div>

<!-- ADDRESS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Address</h2></div>
  <div class="detail-grid">
    <div class="lab">Street address</div> <div class="val">{{ $onboarding->street_address ?: '—' }}</div>
    <div class="lab">City</div>           <div class="val">{{ $onboarding->city ?: '—' }}</div>
    <div class="lab">State</div>          <div class="val">{{ $onboarding->state ?: '—' }}</div>
    <div class="lab">Zip code</div>       <div class="val">{{ $onboarding->zip ?: '—' }}</div>
  </div>
</div>

<!-- SECURE / VERIFICATION -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Verification</h2></div>
  <div class="detail-grid">
    <div class="lab">Date of birth</div>     <div class="val">{{ optional($onboarding->birth_date)->format('F j, Y') ?: '—' }}</div>
    <div class="lab">SSN (full)</div>        <div class="val mono">{{ $fullSsnFormatted }}</div>
    <div class="lab">SSN (last 4)</div>      <div class="val mono">{{ $onboarding->ssn_last4 ?: '—' }}</div>
    <div class="lab">SSN (masked)</div>      <div class="val mono">{{ $onboarding->masked_ssn }}</div>
  </div>
</div>

<!-- CRC SYNC -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Credit Repair Cloud sync</h2></div>
  <div class="detail-grid">
    <div class="lab">CRC status</div>    <div class="val"><span class="badge {{ $onboarding->crc_status }}">{{ $onboarding->crc_status }}</span></div>
    <div class="lab">CRC record ID</div> <div class="val mono">{{ $onboarding->crc_id ?: '—' }}</div>
    <div class="lab">Raw response</div>
    <div class="val mono" style="white-space: pre-wrap; max-height: 240px; overflow:auto; background: var(--bg-2); padding: 12px; border-radius: 8px; font-size: 12px;">{{ $onboarding->crc_response ?: '—' }}</div>
  </div>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>    <div class="val mono">{{ $onboarding->id }}</div>
    <div class="lab">Pipeline status</div><div class="val"><span class="badge {{ $onboarding->status }}">{{ str_replace('_',' ', $onboarding->status) }}</span></div>
    <div class="lab">Submitted at</div>   <div class="val">{{ $onboarding->created_at->format('Y-m-d H:i:s') }} ({{ $onboarding->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>   <div class="val">{{ $onboarding->updated_at->format('Y-m-d H:i:s') }} ({{ $onboarding->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>     <div class="val mono">{{ $onboarding->ip ?: '—' }}</div>
    <div class="lab">User agent</div>     <div class="val" style="font-size: 12px; word-break: break-all;">{{ $onboarding->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px;">
  <a class="adm-btn" href="mailto:{{ $onboarding->email }}?subject=Your%20onboarding%20with%20Victoria%20Love">Email this client →</a>
</div>
@endsection
