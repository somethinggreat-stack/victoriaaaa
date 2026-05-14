@extends('admin.layout')
@section('title', 'Funding applications')

@section('content')
<div class="admin-header">
  <div>
    <h1>Funding leads</h1>
    <div class="sub">{{ $rows->total() }} total · from the 9-step qualification form on /services/diy-business-funding</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.funding') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','contacted','qualified','funded','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No applications match.</strong>Adjust your filters or wait for the next submission.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Applicant</th>
        <th>Contact</th>
        <th>Goal</th>
        <th>FICO</th>
        <th>Income</th>
        <th>Situation</th>
        <th>Status</th>
        <th>Submitted</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $f)
        <tr>
          <td>
            <a href="{{ route('admin.funding.show', $f) }}" class="nm">{{ $f->first_name }} {{ $f->last_name }}</a>
            <span class="sub">#{{ str_pad($f->id, 4, '0', STR_PAD_LEFT) }}</span>
          </td>
          <td>
            <span class="nm">{{ $f->email }}</span>
            <span class="sub">{{ $f->phone }}</span>
          </td>
          <td>{{ $f->amount ?: '—' }}</td>
          <td>{{ $f->fico ?: '—' }}</td>
          <td>{{ $f->income ?: '—' }}</td>
          <td>{{ $f->situation ?: '—' }}</td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.funding.status', $f) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()">
                @foreach (['new','contacted','qualified','funded','archived'] as $s)
                  <option value="{{ $s }}" @selected($f->status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td>{{ $f->created_at->format('M j · g:ia') }}</td>
          <td class="actions"><a class="adm-btn ghost" href="{{ route('admin.funding.show', $f) }}">View</a></td>
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
