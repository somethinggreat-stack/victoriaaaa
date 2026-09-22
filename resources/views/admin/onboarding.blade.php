@extends('admin.layout')
@section('title', 'Onboarding')

@section('content')
<div class="admin-header">
  <div>
    <h1>Paid credit repair clients</h1>
    <div class="sub">{{ $rows->total() }} total · clients who completed the post-payment onboarding form</div>
  </div>
</div>

@if ($apexFailed > 0 && request('apex') !== 'failed')
  <div class="adm-card" style="margin-bottom:14px; border-color:#fecaca; background:#fef4f4;">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <div style="flex:1; min-width:260px;">
        <strong style="color:#991b1b;">{{ $apexFailed }} client{{ $apexFailed === 1 ? '' : 's' }} never reached Apex.</strong>
        <div style="font-size:13px; color:var(--ink-2); margin-top:3px;">
          They paid and completed onboarding, but the handoff failed — so nobody is working their file.
          Open each one and send the short "finish your file" link.
        </div>
      </div>
      <a class="adm-btn" href="{{ route('admin.onboarding', ['apex' => 'failed']) }}">Show them</a>
    </div>
  </div>
@endif

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.onboarding') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, last 4 of SSN" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','in_progress','active','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="apex">
      <option value="">Any Apex state</option>
      <option value="failed"  @selected(request('apex')==='failed')>Apex: failed</option>
      <option value="sent"    @selected(request('apex')==='sent')>Apex: sent</option>
      <option value="pending" @selected(request('apex')==='pending')>Apex: pending</option>
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No submissions match.</strong>Adjust your filters or wait for the next client.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Client</th><th>Contact</th><th>Location</th><th class="nw">SSN</th><th class="nw">DOB</th><th>Status</th><th>Apex</th><th>Submitted</th><th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $r)
        <tr>
          <td>
            <a href="{{ route('admin.onboarding.show', $r) }}" class="nm">{{ $r->firstname }} {{ $r->lastname }}</a>
            <span class="sub">#{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</span>
          </td>
          <td>
            <span class="nm">{{ $r->email }}</span>
            <span class="sub">{{ $r->phone }}</span>
          </td>
          <td>{{ $r->city ?: '—' }}@if($r->state), {{ $r->state }}@endif</td>
          <td class="nw"><code>{{ $r->formatted_ssn }}</code></td>
          <td class="nw">{{ optional($r->birth_date)->format('M j, Y') }}</td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.onboarding.status', $r) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['new','in_progress','active','archived'] as $s)
                  <option value="{{ $s }}" @selected($r->status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>
            @php $ax = $r->crc_status ?: 'pending'; @endphp
            @if ($ax === 'failed')
              <a href="{{ route('admin.onboarding.show', $r) }}" class="badge failed"
                 title="Never reached Apex — open to send the finish-your-file link">failed</a>
            @elseif ($ax === 'sent')
              <span class="badge sent">sent</span>
            @else
              <span class="badge pending">pending</span>
            @endif
          </td>
          <td class="nw">{{ $r->created_at->format('M j · g:ia') }}</td>
          <td class="actions">
            <a class="adm-btn ghost" href="{{ route('admin.onboarding.show', $r) }}">View</a>
            <form class="adm-inline-form" method="POST" action="{{ route('admin.onboarding.destroy', $r) }}" onsubmit="return confirm('Delete {{ $r->firstname }} {{ $r->lastname }}? This permanently removes the client and cannot be undone.');">
              @csrf @method('DELETE')
              <button class="adm-btn danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table></div>

  <div class="pager">
    <div>Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</div>
    <div class="links">{!! $rows->links('vendor.pagination.admin') !!}</div>
  </div>
@endif
@endsection
