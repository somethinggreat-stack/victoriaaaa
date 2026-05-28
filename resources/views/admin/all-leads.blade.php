@extends('admin.layout')
@section('title', 'All Leads')

@section('content')
@php
  use App\Support\Mask;
  $rm = (bool) session('review_mode');

  $statusOptions = [
    ''           => 'All statuses',
    'new'        => 'New',
    'contacted'  => 'Contacted',
    'replied'    => 'Replied',
    'qualified'  => 'Qualified',
    'converted'  => 'Converted',
    'funded'     => 'Funded',
    'enrolled'   => 'Enrolled',
    'archived'   => 'Archived',
  ];

  $typeOptions = [
    ''           => 'All sources',
    'popup'      => 'Popup submissions',
    'contact'    => 'Contact form',
    'funding'    => 'Funding leads',
    'mentorship' => 'Mentorship leads',
  ];

  $typeBadge = [
    'popup'      => 'new',
    'contact'    => 'replied',
    'funding'    => 'pending',
    'mentorship' => 'converted',
  ];
@endphp

<div class="admin-header">
  <div>
    <h1>All leads</h1>
    <div class="sub">{{ number_format($counts['total']) }} total across popup, contact, funding, and mentorship submissions</div>
  </div>
</div>

<div class="adm-stats">
  <a class="adm-stat link" href="{{ route('admin.leads') }}">
    <div class="lab">Popup submissions</div>
    <div class="val">{{ number_format($counts['popup']) }}</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.contacts') }}">
    <div class="lab">Contact form</div>
    <div class="val">{{ number_format($counts['contact']) }}</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.funding') }}">
    <div class="lab">Funding leads</div>
    <div class="val">{{ number_format($counts['funding']) }}</div>
  </a>
  <a class="adm-stat link" href="{{ route('admin.mentorship') }}">
    <div class="lab">Mentorship leads</div>
    <div class="val">{{ number_format($counts['mentorship']) }}</div>
  </a>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.all-leads') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone" value="{{ request('q') }}">
    <select class="adm-select" name="type">
      @foreach ($typeOptions as $val => $lab)
        <option value="{{ $val }}" @selected(request('type')===$val)>{{ $lab }}</option>
      @endforeach
    </select>
    <select class="adm-select" name="status">
      @foreach ($statusOptions as $val => $lab)
        <option value="{{ $val }}" @selected(request('status')===$val)>{{ $lab }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No leads match.</strong>Adjust your filters or wait for the next one.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Source</th>
        <th>Lead</th>
        <th>Summary</th>
        <th>Status</th>
        <th>Captured</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $r)
        <tr>
          <td><span class="badge {{ $typeBadge[$r['type']] ?? 'new' }}">{{ $r['type_label'] }}</span></td>
          <td>
            @if ($rm)
              <span class="nm">{{ Mask::name($r['name']) }}</span>
              <span class="sub">{{ Mask::email($r['email']) }}@if(!empty($r['phone'])) · {{ Mask::phone($r['phone']) }}@endif</span>
            @else
              @if (!empty($r['view_url']))
                <a href="{{ $r['view_url'] }}" class="nm">{{ $r['name'] ?: $r['email'] }}</a>
              @else
                <span class="nm">{{ $r['name'] ?: $r['email'] }}</span>
              @endif
              <span class="sub">{{ $r['email'] }}@if(!empty($r['phone'])) · {{ $r['phone'] }}@endif</span>
            @endif
          </td>
          <td>{{ $r['summary'] ?: '—' }}</td>
          <td><span class="badge {{ $r['status'] }}">{{ ucfirst(str_replace('_',' ',$r['status'])) }}</span></td>
          <td>{{ optional($r['created_at'])->format('M j · g:ia') ?: '—' }}</td>
          <td class="actions">
            @if (!$rm && !empty($r['view_url']))
              <a class="adm-btn ghost" href="{{ $r['view_url'] }}">View</a>
            @endif
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
