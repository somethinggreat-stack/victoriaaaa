@extends('admin.layout')
@section('title', 'Funding lead · ' . $funding->first_name . ' ' . $funding->last_name)

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $funding->first_name }} {{ $funding->last_name }}</h1>
    <div class="sub">#{{ str_pad($funding->id, 4, '0', STR_PAD_LEFT) }} · submitted {{ $funding->created_at->format('M j, Y \a\t g:ia') }} ({{ $funding->created_at->diffForHumans() }})</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.funding') }}">← Back to list</a>
</div>

<!-- IDENTITY -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head">
    <h2>Identity</h2>
    <form class="status-form" method="POST" action="{{ route('admin.funding.status', $funding) }}">
      @csrf @method('PATCH')
      <select name="status" onchange="this.form.submit()">
        @foreach (['new','contacted','qualified','funded','archived'] as $s)
          <option value="{{ $s }}" @selected($funding->status===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="detail-grid">
    <div class="lab">First name</div> <div class="val">{{ $funding->first_name ?: '—' }}</div>
    <div class="lab">Last name</div>  <div class="val">{{ $funding->last_name  ?: '—' }}</div>
    <div class="lab">Email</div>      <div class="val"><a href="mailto:{{ $funding->email }}">{{ $funding->email }}</a></div>
    <div class="lab">Phone</div>      <div class="val"><a href="tel:{{ $funding->phone }}">{{ $funding->phone }}</a></div>
  </div>
</div>

<!-- ALL 8 QUALIFICATION ANSWERS -->
<div class="adm-card" style="margin-bottom: 16px;">
  <div class="adm-card-head"><h2>Qualification answers</h2></div>
  <div class="detail-grid">
    <div class="lab">Step 1 · Funding goal</div>          <div class="val">{{ $funding->amount    ?: '—' }}</div>
    <div class="lab">Step 2 · Truthful confirmation</div> <div class="val">{{ $funding->confirmed === 'yes' ? 'Yes — confirmed' : ($funding->confirmed ?: '—') }}</div>
    <div class="lab">Step 3 · CC credit limits</div>      <div class="val">{{ $funding->limits    ?: '—' }}</div>
    <div class="lab">Step 4 · CC usage %</div>            <div class="val">{{ $funding->usage     ?: '—' }}</div>
    <div class="lab">Step 5 · FICO score</div>            <div class="val">{{ $funding->fico      ?: '—' }}</div>
    <div class="lab">Step 6 · Business situation</div>    <div class="val">{{ $funding->situation ?: '—' }}</div>
    <div class="lab">Step 7 · Annual income</div>         <div class="val">{{ $funding->income    ?: '—' }}</div>
    <div class="lab">Step 8 · Negative marks</div>
    <div class="val">
      @if (!empty($funding->negatives))
        @foreach ($funding->negatives as $n)
          <span class="badge new" style="margin-right:4px; margin-bottom:4px;">{{ $n }}</span>
        @endforeach
      @else — @endif
    </div>
  </div>
</div>

<!-- METADATA -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Metadata</h2></div>
  <div class="detail-grid">
    <div class="lab">Internal ID</div>    <div class="val mono">{{ $funding->id }}</div>
    <div class="lab">Pipeline status</div><div class="val"><span class="badge {{ $funding->status }}">{{ $funding->status }}</span></div>
    <div class="lab">Submitted at</div>   <div class="val">{{ $funding->created_at->format('Y-m-d H:i:s') }} ({{ $funding->created_at->diffForHumans() }})</div>
    <div class="lab">Last updated</div>   <div class="val">{{ $funding->updated_at->format('Y-m-d H:i:s') }} ({{ $funding->updated_at->diffForHumans() }})</div>
    <div class="lab">IP address</div>     <div class="val mono">{{ $funding->ip ?: '—' }}</div>
    <div class="lab">User agent</div>     <div class="val" style="font-size: 12px; word-break: break-all;">{{ $funding->user_agent ?: '—' }}</div>
  </div>
</div>

<div style="margin-top: 20px;">
  <a class="adm-btn" href="mailto:{{ $funding->email }}?subject=Your%20funding%20application%20with%20Victoria%20Love">Email this applicant →</a>
</div>
@endsection
