@extends('admin.layout')
@section('title', 'Mentorship leads')

@section('content')
<div class="admin-header">
  <div>
    <h1>Mentorship leads</h1>
    <div class="sub">{{ $rows->total() }} total · from the 5-step qualifier on /mentorship</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.mentorship') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','contacted','qualified','enrolled','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No leads match.</strong>Adjust your filters or wait for the next submission.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Lead</th>
        <th>Contact</th>
        <th>Situation</th>
        <th>Timeline</th>
        <th>Weekly hrs</th>
        <th>Investment</th>
        <th>Status</th>
        <th>Submitted</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $m)
        <tr>
          <td>
            <a href="{{ route('admin.mentorship.show', $m) }}" class="nm">{{ $m->first_name }} {{ $m->last_name }}</a>
            <span class="sub">#{{ str_pad($m->id, 4, '0', STR_PAD_LEFT) }}</span>
          </td>
          <td>
            <span class="nm">{{ $m->email }}</span>
            <span class="sub">{{ $m->phone }}</span>
          </td>
          <td>{{ $m->situation ?: '—' }}</td>
          <td>{{ $m->timeline ?: '—' }}</td>
          <td>{{ $m->hours ?: '—' }}</td>
          <td>{{ $m->investment ?: '—' }}</td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.mentorship.status', $m) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['new','contacted','qualified','enrolled','archived'] as $s)
                  <option value="{{ $s }}" @selected($m->status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>{{ $m->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.mentorship.show', $m) }}">View</a></td>
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
