@extends('admin.layout')
@section('title', 'Contacts')

@section('content')
<div class="admin-header">
  <div>
    <h1>Contact Us page submissions</h1>
    <div class="sub">{{ $contacts->total() }} total · from /contact</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.contacts') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, message" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','replied','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($contacts->isEmpty())
  <div class="empty"><strong>No messages match.</strong>Adjust your filters or wait for a new submission.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>From</th><th>Topic</th><th>Score / Timeline</th><th>Message preview</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
      @foreach ($contacts as $c)
        <tr>
          <td>
            <a href="{{ route('admin.contacts.show', $c) }}" class="nm">{{ $c->name }}</a>
            <span class="sub">{{ $c->email }}</span>
          </td>
          <td>{{ $c->topic ?: '—' }}</td>
          <td>
            <span class="nm">{{ $c->score ?: '—' }}</span>
            <span class="sub">{{ $c->timeline ?: '' }}</span>
          </td>
          <td style="max-width: 320px;">{{ Str::limit($c->message, 80) }}</td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.contacts.status', $c) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['new','replied','archived'] as $s)
                  <option value="{{ $s }}" @selected($c->status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>{{ $c->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.contacts.show', $c) }}">Open</a></td>
        </tr>
      @endforeach
    </tbody>
  </table></div>

  <div class="pager">
    <div>Showing {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} of {{ $contacts->total() }}</div>
    <div class="links">{!! $contacts->links('vendor.pagination.admin') !!}</div>
  </div>
@endif
@endsection
