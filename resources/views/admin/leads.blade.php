@extends('admin.layout')
@section('title', 'Leads')

@section('content')
<div class="admin-header">
  <div>
    <h1>Popup submissions</h1>
    <div class="sub">{{ $leads->total() }} total · from the 4-step qualifier popup on the homepage</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.leads') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','contacted','converted','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($leads->isEmpty())
  <div class="empty"><strong>No leads match.</strong>Adjust your filters or wait for the next one.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>Lead</th><th>Score</th><th>Issue</th><th>Goal</th><th>Source</th><th>Status</th><th>Captured</th><th></th></tr></thead>
    <tbody>
      @foreach ($leads as $l)
        <tr>
          <td>
            <a href="{{ route('admin.leads.show', $l) }}" class="nm">{{ $l->name ?: $l->email }}</a>
            <span class="sub">{{ $l->email }}@if($l->phone) · {{ $l->phone }}@endif</span>
          </td>
          <td>{{ $l->score ?: '—' }}</td>
          <td>{{ $l->issue ?: '—' }}</td>
          <td>{{ $l->goal ?: '—' }}</td>
          <td><span class="badge new">{{ $l->source }}</span></td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.leads.status', $l) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['new','contacted','converted','archived'] as $s)
                  <option value="{{ $s }}" @selected($l->status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>{{ $l->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.leads.show', $l) }}">View</a></td>
        </tr>
      @endforeach
    </tbody>
  </table></div>

  <div class="pager">
    <div>Showing {{ $leads->firstItem() }}–{{ $leads->lastItem() }} of {{ $leads->total() }}</div>
    <div class="links">{!! $leads->links('vendor.pagination.admin') !!}</div>
  </div>
@endif
@endsection
