@extends('admin.layout')
@section('title', 'Burgundy Expenses')

@section('content')
@include('admin.burgundy._styles')

<div class="admin-header">
  <div>
    <h1>Burgundy expenses</h1>
    <div class="sub">Other shared expenses · only <strong>approved</strong> expenses reduce net profit</div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.burgundy.index', ['period' => $period]) }}">← Back to Burgundy clients</a>
</div>

@if ($errors->any())
  <div class="flash error">{{ $errors->first() }}</div>
@endif

<div class="bg-label">
  <span>{{ $periods[$period] ?? '' }}</span>
  <form method="GET" action="{{ route('admin.burgundy.expenses') }}">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <select class="adm-select" name="period" onchange="this.form.submit()" aria-label="Period">
      @foreach ($periods as $val => $lab)
        <option value="{{ $val }}" @selected($period === $val)>{{ $lab }}</option>
      @endforeach
    </select>
  </form>
</div>
<div class="bg-stats">
  <div class="adm-stat">
    <div class="lab">Approved</div>
    <div class="val" style="color:#157a3d">${{ number_format((float) ($totals['approved'] ?? 0), 2) }}</div>
    <div class="delta">Counted in net profit</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Pending approval</div>
    <div class="val" style="color:#92400e">${{ number_format((float) ($totals['pending'] ?? 0), 2) }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Rejected</div>
    <div class="val" style="color:var(--ink-3)">${{ number_format((float) ($totals['rejected'] ?? 0), 2) }}</div>
  </div>
</div>

<div class="adm-card" style="margin-bottom:18px">
  <div class="adm-card-head"><h2>Add an expense</h2></div>
  <form method="POST" action="{{ route('admin.burgundy.expenses.store') }}">
    @csrf
    <div class="bg-form cols-4">
      <div class="plm-fld" style="grid-column: span 2">
        <label>Description <span class="req">*</span></label>
        <input class="plm-input" type="text" name="description" maxlength="150" required placeholder="e.g. Mailing / certified letters" value="{{ old('description') }}">
      </div>
      <div class="plm-fld">
        <label>Amount <span class="req">*</span></label>
        <input class="plm-input" type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" value="{{ old('amount') }}">
      </div>
      <div class="plm-fld">
        <label>Date <span class="req">*</span></label>
        <input class="plm-input" type="date" name="expense_date" required value="{{ old('expense_date', now()->format('Y-m-d')) }}">
      </div>
      <div class="plm-fld">
        <label>Status</label>
        <select class="plm-input" name="status">
          @foreach (\App\Models\BurgundyExpense::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', 'pending') === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="plm-fld" style="grid-column: span 3">
        <label>Notes</label>
        <input class="plm-input" type="text" name="notes" maxlength="255" placeholder="Optional" value="{{ old('notes') }}">
      </div>
    </div>
    <div class="bg-form-foot"><button class="adm-btn pink" type="submit">Add expense</button></div>
  </form>
</div>

<nav class="bg-tabs" aria-label="Filter by status">
  <a href="{{ route('admin.burgundy.expenses', ['period' => $period]) }}" class="{{ request('status') ? '' : 'on' }}">All</a>
  @foreach (\App\Models\BurgundyExpense::STATUSES as $s)
    <a href="{{ route('admin.burgundy.expenses', ['period' => $period, 'status' => $s]) }}" class="{{ request('status') === $s ? 'on' : '' }}">{{ ucfirst($s) }}</a>
  @endforeach
</nav>

@if ($rows->isEmpty())
  <div class="empty"><strong>No expenses for this period.</strong>Add one above — it only affects profit once approved.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Status</th><th>Notes</th><th></th></tr></thead>
    <tbody>
      @foreach ($rows as $e)
        <tr>
          <td style="white-space:nowrap">{{ $e->expense_date->format('M j, Y') }}</td>
          <td><span class="nm">{{ $e->description }}</span></td>
          <td><strong>${{ number_format((float) $e->amount, 2) }}</strong></td>
          <td>
            <form class="status-form" method="POST" action="{{ route('admin.burgundy.expenses.status', $e) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()" aria-label="Expense status">
                @foreach (\App\Models\BurgundyExpense::STATUSES as $s)
                  <option value="{{ $s }}" @selected($e->status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td class="wrap">{{ $e->notes ?: '—' }}</td>
          <td class="actions">
            <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.expenses.destroy', $e) }}" data-confirm="Delete this expense?">
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
@endsection
