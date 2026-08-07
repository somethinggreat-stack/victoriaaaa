@extends('admin.layout')
@section('title', 'Apex Retries')

@section('content')
<div class="admin-header">
  <div>
    <h1>Apex Retries</h1>
    <div class="sub">Onboarding submissions whose forward to Apex failed — retry them here to push them into New Clients.</div>
  </div>
  @unless ($needsSetup)
    @if(($kpis['pending'] ?? 0) > 0)
      <form method="POST" action="{{ route('admin.apex-retries.retry-all') }}" onsubmit="return confirm('Retry all pending submissions to Apex now?');">
        @csrf
        <button type="submit" class="adm-btn" style="background:var(--pink);">↻ Retry all pending</button>
      </form>
    @endif
  @endunless
</div>

<style>
  .badge.pending   { background:#fff4e5; color:#9a5b00; border:1px solid #ffd8a8; text-transform:capitalize; }
  .badge.succeeded { background:#e6f6ec; color:#157a3d; border:1px solid #c9eccd; text-transform:capitalize; }
  .ar-err { font-size:12px; color:#8a1f1f; max-width:420px; white-space:pre-wrap; word-break:break-word; }
  .ar-setup-note { background:#fff8e6; border:1px solid #ffe4a3; border-radius:12px; padding:18px 20px; margin-bottom:22px; font-size:14px; color:#7a5b00; line-height:1.6; }
  .ar-setup-note code { background:#fff; border:1px solid #ffe4a3; padding:2px 6px; border-radius:5px; }
</style>

@if (session('success'))<div class="flash success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="flash error">{{ session('error') }}</div>@endif

@if ($needsSetup)
  <div class="ar-setup-note">
    <strong>One-time setup needed.</strong> The <code>apex_retry_jobs</code> table doesn't exist yet.
    Run the setup SQL (in <code>database/setup.sql</code>) once via phpMyAdmin, then failed forwards will be captured here for retry.
  </div>
@else
  <div class="adm-kpis" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Pending retry</div><div style="font-size:24px;font-weight:700;">{{ $kpis['pending'] }}</div></div>
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Sent after retry</div><div style="font-size:24px;font-weight:700;">{{ $kpis['succeeded'] }}</div></div>
  </div>

  @if ($pending->isEmpty())
    <div class="empty"><strong>Nothing pending. 🎉</strong>Every onboarding submission has reached Apex. Failed forwards will show up here automatically.</div>
  @else
    <div class="adm-table-wrap"><table class="adm-table">
      <thead>
        <tr><th>Client</th><th>Attempts</th><th>Last error</th><th>Failed at</th><th></th></tr>
      </thead>
      <tbody>
        @foreach ($pending as $j)
          <tr>
            <td>
              <span class="nm">{{ $j->client_name ?: '—' }}</span>
              <span class="sub">{{ $j->email ?: '—' }}</span>
            </td>
            <td>{{ $j->attempts }}</td>
            <td><span class="ar-err">{{ $j->last_error ?: '—' }}</span></td>
            <td>{{ $j->created_at->format('M j · g:ia') }}</td>
            <td class="actions">
              <form method="POST" action="{{ route('admin.apex-retries.retry', $j) }}">
                @csrf
                <button class="adm-btn" type="submit">↻ Retry</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
    <div class="pager">
      <div>Showing {{ $pending->firstItem() }}–{{ $pending->lastItem() }} of {{ $pending->total() }}</div>
      <div class="links">{!! $pending->appends(request()->except('pending'))->links('vendor.pagination.admin') !!}</div>
    </div>
  @endif

  @if ($done && $done->isNotEmpty())
    <h2 style="margin:34px 0 14px;font-size:18px;">Recently sent after retry</h2>
    <div class="adm-table-wrap"><table class="adm-table">
      <thead><tr><th>Client</th><th>Apex ID</th><th>Attempts</th><th>Sent at</th></tr></thead>
      <tbody>
        @foreach ($done as $j)
          <tr>
            <td><span class="nm">{{ $j->client_name ?: '—' }}</span><span class="sub">{{ $j->email ?: '—' }}</span></td>
            <td class="mono">{{ $j->apex_id ?: '—' }}</td>
            <td>{{ $j->attempts }}</td>
            <td>{{ optional($j->succeeded_at)->format('M j · g:ia') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @endif
@endif
@endsection
