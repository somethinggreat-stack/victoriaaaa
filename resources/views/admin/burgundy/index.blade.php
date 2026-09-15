@extends('admin.layout')
@section('title', 'Burgundy Clients')

@section('content')
@include('admin.burgundy._styles')

@if ($needsSetup)
  <div class="admin-header">
    <div>
      <h1>Burgundy clients</h1>
      <div class="sub">Clients serviced with Burgundy · 50/50 net-profit split</div>
    </div>
  </div>

  <div class="bg-setup">
    <strong>One-time setup needed.</strong> The Burgundy tables don't exist yet. Click the button below to create them,
    or run the Burgundy section of <code>database/setup.sql</code> in phpMyAdmin.
    <form method="POST" action="{{ route('admin.burgundy.setup') }}" style="margin-top:12px">
      @csrf
      <button class="adm-btn pink" type="submit">Set up Burgundy Clients</button>
    </form>
  </div>
@else
@php
  $fmt = fn ($n) => ($n < 0 ? '−$' : '$') . number_format(abs($n), 2);
  $periodLabel = $periods[$period] ?? '';
  $isAll = $period === 'all';
  $share = \App\Services\BurgundyLedger::burgundyShare();
  $bPct = rtrim(rtrim(number_format($share * 100, 1), '0'), '.');
  $vPct = rtrim(rtrim(number_format((1 - $share) * 100, 1), '0'), '.');
  $filterUrl = fn ($s) => route('admin.burgundy.index', array_filter(['status' => $s, 'period' => $period, 'q' => request('q')]));
  $addErrors = $errors->any() && old('_form') === 'add';
@endphp

<div class="admin-header">
  <div>
    <h1>Burgundy clients</h1>
    <div class="sub">{{ number_format($total) }} client{{ $total === 1 ? '' : 's' }} · transitioned and new clients serviced with Burgundy</div>
  </div>
  <div class="bg-actions">
    <a class="adm-btn ghost" href="{{ route('admin.burgundy.expenses', ['period' => $period]) }}">Expenses</a>
    <button type="button" class="adm-btn ghost" data-modal-open="bgImport">Import existing</button>
    <button type="button" class="adm-btn pink" data-modal-open="bgAdd">+ Add client</button>
  </div>
</div>

@if ($errors->any() && ! $addErrors)
  <div class="flash error">{{ $errors->first() }}</div>
@endif

{{-- ── Client pipeline ── --}}
<div class="bg-label"><span>Clients</span></div>
<div class="bg-stats">
  <a class="adm-stat link" href="{{ $filterUrl('') }}">
    <div class="lab">Total Clients</div>
    <div class="val">{{ number_format($total) }}</div>
  </a>
  <a class="adm-stat link" href="{{ $filterUrl('signed_up') }}">
    <div class="lab">Signed Up</div>
    <div class="val">{{ number_format($counts['signed_up'] ?? 0) }}</div>
  </a>
  <a class="adm-stat link" href="{{ $filterUrl('payment_pending') }}">
    <div class="lab">Payment Pending</div>
    <div class="val" style="color:#92400e">{{ number_format($counts['payment_pending'] ?? 0) }}</div>
  </a>
  <a class="adm-stat link" href="{{ $filterUrl('paid') }}">
    <div class="lab">Paid</div>
    <div class="val" style="color:#047857">{{ number_format($counts['paid'] ?? 0) }}</div>
  </a>
  <a class="adm-stat link" href="{{ $filterUrl('active') }}">
    <div class="lab">Active</div>
    <div class="val" style="color:#157a3d">{{ number_format($counts['active'] ?? 0) }}</div>
  </a>
</div>

{{-- ── Money ── --}}
<div class="bg-label">
  <span>Financials · {{ $periodLabel }}</span>
  <form method="GET" action="{{ route('admin.burgundy.index') }}">
    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
    <select class="adm-select" name="period" onchange="this.form.submit()" aria-label="Financial period">
      @foreach ($periods as $val => $lab)
        <option value="{{ $val }}" @selected($period === $val)>{{ $lab }}</option>
      @endforeach
    </select>
  </form>
</div>
<div class="bg-stats">
  <div class="adm-stat">
    <div class="lab">{{ $isAll ? 'Total Revenue' : 'Monthly Revenue' }}</div>
    <div class="val" style="color:var(--pink)">{{ $fmt($money['revenue']) }}</div>
    <div class="delta">Collected {{ $isAll ? 'all time' : 'in ' . $periodLabel }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Backend Cost</div>
    <div class="val">{{ $fmt($money['backend']) }}</div>
    <div class="delta">{{ $money['rounds'] }} round{{ $money['rounds'] === 1 ? '' : 's' }} × ${{ number_format($roundCost, 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Customer Support Cost</div>
    <div class="val">{{ $fmt($money['support']) }}</div>
    <div class="delta">{{ rtrim(rtrim(number_format($money['support_weeks'], 1), '0'), '.') }} wks × ${{ number_format(\App\Services\BurgundyLedger::supportWeekly(), 0) }}/wk</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Net Profit</div>
    <div class="val {{ $money['net'] < 0 ? 'money-neg' : '' }}">{{ $fmt($money['net']) }}</div>
    <div class="delta">After <a href="{{ route('admin.burgundy.expenses', ['period' => $period]) }}">{{ $fmt($money['expenses']) }} approved expenses</a></div>
  </div>
  <div class="adm-stat">
    <div class="lab">Burgundy {{ $bPct }}% Share</div>
    <div class="val {{ $money['burgundy'] < 0 ? 'money-neg' : '' }}">{{ $fmt($money['burgundy']) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Victoria {{ $vPct }}% Share</div>
    <div class="val {{ $money['victoria'] < 0 ? 'money-neg' : 'money-pos' }}">{{ $fmt($money['victoria']) }}</div>
  </div>
</div>

{{-- ── Status filters ── --}}
<nav class="bg-tabs" aria-label="Filter by status">
  <a href="{{ $filterUrl('') }}" class="{{ $status === '' ? 'on' : '' }}">All<span class="n">{{ $total }}</span></a>
  @foreach (\App\Models\BurgundyClient::STATUSES as $key => $label)
    <a href="{{ $filterUrl($key) }}" class="{{ $status === $key ? 'on' : '' }}">{{ $label }}<span class="n">{{ $counts[$key] ?? 0 }}</span></a>
  @endforeach
</nav>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.burgundy.index') }}">
    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <input type="hidden" name="period" value="{{ $period }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, phone" value="{{ request('q') }}">
    <button class="adm-btn" type="submit">Search</button>
    @if (request('q'))<a class="adm-btn ghost" href="{{ $filterUrl($status) }}">Clear</a>@endif
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty">
    @if ($total === 0)
      <strong>No Burgundy clients yet.</strong>Use <em>Import existing</em> to bring over current Burgundy clients, or <em>+ Add client</em> for a new one.
    @else
      <strong>No clients match.</strong>Try another status or search.
    @endif
  </div>
@else
  <div class="adm-table-wrap"><table class="adm-table" style="min-width:1280px">
    <thead>
      <tr>
        <th>Name</th><th>Phone</th><th>Email</th><th>Signup Date</th><th>Payment Status</th><th>Amount Paid</th>
        <th>Client Status</th><th>Current Round</th><th>Last Activity</th><th>Next Action</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $r)
        @php
          $paid = $amountPaid[$r->id] ?? 0;
          if ($r->subscription && $r->subscription->status === 'past_due') { $ps = 'past_due'; }
          elseif ($paid > 0)                                                { $ps = 'paid'; }
          elseif (isset($pendingIds[$r->id]) || $r->status === 'payment_pending') { $ps = 'pending'; }
          else                                                              { $ps = 'unpaid'; }
        @endphp
        <tr>
          <td>
            <a href="{{ route('admin.burgundy.show', $r) }}" class="nm">{{ $r->full_name }}</a>
            <span class="sub">{{ \App\Models\BurgundyClient::SOURCES[$r->source] ?? ucfirst($r->source) }}</span>
          </td>
          <td>@if($r->phone)<a href="tel:{{ $r->phone }}">{{ $r->phone }}</a>@else<span class="sub">—</span>@endif</td>
          <td>@if($r->email)<a href="mailto:{{ $r->email }}">{{ $r->email }}</a>@else<span class="sub">—</span>@endif</td>
          <td style="white-space:nowrap">{{ $r->signup_date?->format('M j, Y') ?? '—' }}</td>
          <td><span class="badge pay-{{ $ps }}">{{ str_replace('_', ' ', $ps) }}</span></td>
          <td style="white-space:nowrap">
            <span class="nm">${{ number_format($paid, 2) }}</span>
            @if($r->monthly_fee)<span class="sub">${{ number_format((float) $r->monthly_fee, 2) }}/mo</span>@endif
          </td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.burgundy.status', $r) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()" aria-label="Client status">
                @foreach (\App\Models\BurgundyClient::STATUSES as $key => $label)
                  <option value="{{ $key }}" @selected($r->status === $key)>{{ $label }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td style="white-space:nowrap">{{ $r->current_round > 0 ? 'Round ' . $r->current_round : '—' }}</td>
          <td style="white-space:nowrap" title="{{ $r->last_activity_at?->format('M j, Y g:ia') }}">{{ $r->last_activity_at?->diffForHumans() ?? '—' }}</td>
          <td class="wrap">
            @if ($r->next_action)
              <span class="nm" style="font-weight:500">{{ $r->next_action }}</span>
            @else
              <span class="sub">{{ $r->suggestedNextAction() }}</span>
            @endif
          </td>
          <td class="actions">
            <a class="adm-btn ghost sm" href="{{ route('admin.burgundy.show', $r) }}">View</a>
            @unless (in_array($r->status, ['cancelled', 'paused'], true))
              <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.rounds.store', $r) }}"
                    data-confirm="Log round {{ $r->current_round + 1 }} for {{ $r->full_name }}? This adds ${{ number_format($roundCost, 2) }} backend cost.">
                @csrf
                <button class="adm-btn sm" type="submit">+ Round</button>
              </form>
            @endunless
            <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.destroy', $r) }}"
                  data-confirm="Delete {{ $r->full_name }}? This removes the client, their rounds and payment records. It cannot be undone.">
              @csrf @method('DELETE')
              <button class="adm-btn danger sm" type="submit">Delete</button>
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

{{-- ── Add client modal ── --}}
<div class="plm-overlay {{ $addErrors ? 'open' : '' }}" id="bgAdd" role="dialog" aria-modal="true" aria-labelledby="bgAddTitle">
  <div class="plm-card">
    <div class="plm-head">
      <div>
        <h3 id="bgAddTitle">Add a Burgundy client</h3>
        <div class="sub">If a checkout subscription exists for this email, its payments are linked automatically.</div>
      </div>
      <button type="button" class="plm-close" data-modal-close aria-label="Close">×</button>
    </div>
    <form method="POST" action="{{ route('admin.burgundy.store') }}">
      @csrf
      <input type="hidden" name="_form" value="add">
      <div class="plm-body">
        <div class="bg-form">
          <div class="plm-fld">
            <label>First name <span class="req">*</span></label>
            <input class="plm-input" type="text" name="first_name" maxlength="100" required value="{{ old('first_name') }}">
            @if($addErrors) @error('first_name')<div class="plm-fld-err">{{ $message }}</div>@enderror @endif
          </div>
          <div class="plm-fld">
            <label>Last name</label>
            <input class="plm-input" type="text" name="last_name" maxlength="100" value="{{ old('last_name') }}">
          </div>
          <div class="plm-fld">
            <label>Email</label>
            <input class="plm-input" type="email" name="email" maxlength="150" value="{{ old('email') }}">
            @if($addErrors) @error('email')<div class="plm-fld-err">{{ $message }}</div>@enderror @endif
          </div>
          <div class="plm-fld">
            <label>Phone</label>
            <input class="plm-input" type="tel" name="phone" maxlength="30" value="{{ old('phone') }}">
          </div>
          <div class="plm-fld">
            <label>Status <span class="req">*</span></label>
            <select class="plm-input" name="status">
              @foreach (\App\Models\BurgundyClient::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(old('status', 'invited') === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="plm-fld">
            <label>Client type</label>
            <select class="plm-input" name="source">
              @foreach (\App\Models\BurgundyClient::SOURCES as $key => $label)
                <option value="{{ $key }}" @selected(old('source', 'new') === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="plm-fld">
            <label>Signup date</label>
            <input class="plm-input" type="date" name="signed_up_at" value="{{ old('signed_up_at') }}">
          </div>
          <div class="plm-fld">
            <label>Monthly fee</label>
            <input class="plm-input" type="number" name="monthly_fee" step="0.01" min="0" placeholder="0.00" value="{{ old('monthly_fee') }}">
          </div>
          <div class="plm-fld">
            <label>Current round</label>
            <input class="plm-input" type="number" name="current_round" min="0" max="99" placeholder="0" value="{{ old('current_round') }}">
            <span class="hint">For transitioned clients already mid-program.</span>
          </div>
          <div class="plm-fld">
            <label>Next action</label>
            <input class="plm-input" type="text" name="next_action" maxlength="255" placeholder="Auto-suggested if blank" value="{{ old('next_action') }}">
          </div>
        </div>
      </div>
      <div class="plm-foot">
        <button type="button" class="plm-btn-ghost" data-modal-close>Cancel</button>
        <button type="submit" class="plm-btn-primary">Add client</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Import modal ── --}}
<div class="plm-overlay" id="bgImport" role="dialog" aria-modal="true" aria-labelledby="bgImportTitle">
  <div class="plm-card">
    <div class="plm-head">
      <div>
        <h3 id="bgImportTitle">Import existing Burgundy clients</h3>
        <div class="sub">Paste rows from a spreadsheet as CSV. Imported clients are marked <strong>Transitioned</strong>.</div>
      </div>
      <button type="button" class="plm-close" data-modal-close aria-label="Close">×</button>
    </div>
    <form method="POST" action="{{ route('admin.burgundy.import') }}">
      @csrf
      <div class="plm-body">
        <div class="bg-csv-help">
          First row must be the header. Columns (any order, only a name is required):<br>
          <code>name, email, phone, status, current_round, amount_paid, signup_date, next_action</code><br>
          Status defaults to <em>Active</em>. <em>amount_paid</em> is recorded as paid before the transition — it shows in Amount Paid but isn't part of the profit split. Emails already in the list are skipped.
        </div>
        <div class="plm-fld">
          <label>CSV <span class="req">*</span></label>
          <textarea class="plm-input" name="csv" rows="8" required style="font-family:ui-monospace,monospace;font-size:12.5px" placeholder="name,email,phone,status,current_round,amount_paid,signup_date&#10;Jane Smith,jane@email.com,555-123-4567,Active,3,297,2026-06-02"></textarea>
        </div>
      </div>
      <div class="plm-foot">
        <button type="button" class="plm-btn-ghost" data-modal-close>Cancel</button>
        <button type="submit" class="plm-btn-primary">Import clients</button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection
