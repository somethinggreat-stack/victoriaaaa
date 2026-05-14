@extends('admin.layout')
@section('title', 'Onboarding')

@section('content')
<div class="admin-header">
  <div>
    <h1>Paid credit repair clients</h1>
    <div class="sub">{{ $rows->total() }} total · clients who completed the post-payment onboarding form</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.onboarding') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, last 4 of SSN" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','in_progress','active','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
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
        <th>Client</th><th>Contact</th><th>Location</th><th>SSN</th><th>DOB</th><th>CRC</th><th>Status</th><th>Submitted</th><th></th>
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
          <td><code>{{ $r->masked_ssn }}</code></td>
          <td>{{ optional($r->birth_date)->format('M j, Y') }}</td>
          <td><span class="badge {{ $r->crc_status }}">{{ $r->crc_status }}</span></td>
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
          <td>{{ $r->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.onboarding.show', $r) }}">View</a></td>
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
