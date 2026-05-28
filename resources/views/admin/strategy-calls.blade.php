@extends('admin.layout')
@section('title', 'Strategy Calls')

@section('content')
@php
  use App\Support\Mask;
  $rm = (bool) session('review_mode');
@endphp

<div class="admin-header">
  <div>
    <h1>Strategy call requests</h1>
    <div class="sub">{{ $rows->total() }} total · qualified before they reach the Calendly booking page</div>
  </div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.strategy-calls') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone, goal" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">All statuses</option>
      @foreach (['new','booked','showed','no_show','converted','archived'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No strategy-call requests yet.</strong>They will show up here the moment someone fills the gate form.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>Lead</th><th>Goal</th><th>Monitoring</th><th>Investment</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
      @foreach ($rows as $r)
        <tr>
          <td>
            @if ($rm)
              <span class="nm">{{ Mask::name($r->name) }}</span>
              <span class="sub">{{ Mask::email($r->email) }} · {{ Mask::phone($r->phone) }}</span>
            @else
              <a href="{{ route('admin.strategy-calls.show', $r) }}" class="nm">{{ $r->name ?: $r->email }}</a>
              <span class="sub">{{ $r->email }} · {{ $r->phone }}</span>
            @endif
          </td>
          <td>{{ \Illuminate\Support\Str::limit($r->goal, 70) ?: '—' }}</td>
          <td>
            {{ $r->monitoring_service ?: '—' }}
            @if (!$rm && $r->monitoring_username)
              <br><span class="sub">{{ $r->monitoring_username }}</span>
            @endif
          </td>
          <td>{{ $r->investment_range ?: '—' }}</td>
          <td>
            @if ($rm)
              <span class="badge {{ $r->status }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span>
            @else
              <form class="status-form" method="POST" action="{{ route('admin.strategy-calls.status', $r) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()">
                  @foreach (['new','booked','showed','no_show','converted','archived'] as $s)
                    <option value="{{ $s }}" @selected($r->status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                  @endforeach
                </select>
              </form>
            @endif
          </td>
          <td>{{ $r->created_at->format('M j · g:ia') }}</td>
          <td class="actions">
            @unless ($rm)
              <a class="adm-btn ghost" href="{{ route('admin.strategy-calls.show', $r) }}">View</a>
            @endunless
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
