@extends('admin.layout')
@section('title', 'Burgundy client · ' . $client->full_name)

@section('content')
@include('admin.burgundy._styles')

@php
  $today = now()->format('Y-m-d');
  $fmt = fn ($n) => ($n < 0 ? '−$' : '$') . number_format(abs($n), 2);
@endphp

<div class="admin-header">
  <div>
    <h1>{{ $client->full_name }}</h1>
    <div class="sub">
      <span class="badge st-{{ $client->status }}">{{ $client->status_label }}</span>
      &nbsp;{{ \App\Models\BurgundyClient::SOURCES[$client->source] ?? ucfirst($client->source) }} client · added {{ $client->created_at->format('M j, Y') }}
    </div>
  </div>
  <a class="adm-btn ghost" href="{{ route('admin.burgundy.index') }}">← Back to Burgundy clients</a>
</div>

@if ($errors->any())
  <div class="flash error">{{ $errors->first() }}</div>
@endif

@if (session('generated_link'))
  <div class="bg-generated">
    <div class="h">✓ Payment link ready for {{ $client->full_name }}</div>
    <div class="bg-linkrow">
      <input type="text" value="{{ session('generated_link') }}" readonly onclick="this.select()">
      <button type="button" class="adm-btn pink" data-copy-text="{{ session('generated_link') }}">Copy link</button>
      <a class="adm-btn ghost" href="{{ session('generated_link') }}" target="_blank" rel="noopener">Open</a>
    </div>
  </div>
@endif

<div class="bg-stats">
  <div class="adm-stat">
    <div class="lab">Amount Paid</div>
    <div class="val" style="color:var(--pink)">{{ $fmt($amountPaid) }}</div>
    <div class="delta">All time</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Current Round</div>
    <div class="val">{{ $client->current_round ?: '—' }}</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Backend Cost</div>
    <div class="val">{{ $fmt($backendCost) }}</div>
    <div class="delta">{{ $client->rounds->count() }} round{{ $client->rounds->count() === 1 ? '' : 's' }} processed here</div>
  </div>
  <div class="adm-stat">
    <div class="lab">Last Activity</div>
    <div class="val" style="font-size:18px">{{ $client->last_activity_at?->diffForHumans() ?? '—' }}</div>
    <div class="delta">Next: {{ $client->next_action_label }}</div>
  </div>
</div>

{{-- ── Details ── --}}
<div class="adm-card" style="margin-bottom:16px">
  <div class="adm-card-head"><h2>Client details</h2></div>
  <form method="POST" action="{{ route('admin.burgundy.update', $client) }}">
    @csrf @method('PATCH')
    <div class="bg-form cols-4">
      <div class="plm-fld">
        <label>First name <span class="req">*</span></label>
        <input class="plm-input" type="text" name="first_name" maxlength="100" required value="{{ old('first_name', $client->first_name) }}">
      </div>
      <div class="plm-fld">
        <label>Last name</label>
        <input class="plm-input" type="text" name="last_name" maxlength="100" value="{{ old('last_name', $client->last_name) }}">
      </div>
      <div class="plm-fld">
        <label>Email</label>
        <input class="plm-input" type="email" name="email" maxlength="150" value="{{ old('email', $client->email) }}">
        @error('email')<div class="plm-fld-err">{{ $message }}</div>@enderror
      </div>
      <div class="plm-fld">
        <label>Phone</label>
        <input class="plm-input" type="tel" name="phone" maxlength="30" value="{{ old('phone', $client->phone) }}">
      </div>

      <div class="plm-fld">
        <label>Client status</label>
        <select class="plm-input" name="status">
          @foreach (\App\Models\BurgundyClient::STATUSES as $key => $label)
            <option value="{{ $key }}" @selected(old('status', $client->status) === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="plm-fld">
        <label>Client type</label>
        <select class="plm-input" name="source">
          @foreach (\App\Models\BurgundyClient::SOURCES as $key => $label)
            <option value="{{ $key }}" @selected(old('source', $client->source) === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="plm-fld">
        <label>Signup date</label>
        <input class="plm-input" type="date" name="signed_up_at" value="{{ old('signed_up_at', $client->signed_up_at?->format('Y-m-d')) }}">
      </div>
      <div class="plm-fld">
        <label>Monthly fee</label>
        <input class="plm-input" type="number" name="monthly_fee" step="0.01" min="0" value="{{ old('monthly_fee', $client->monthly_fee) }}">
      </div>

      <div class="plm-fld">
        <label>Current round</label>
        <input class="plm-input" type="number" name="current_round" min="0" max="99" value="{{ old('current_round', $client->current_round) }}">
      </div>
      <div class="plm-fld" style="grid-column: span 3">
        <label>Linked checkout subscription</label>
        <select class="plm-input" name="subscription_id">
          <option value="">— Not linked —</option>
          @foreach ($subscriptionOptions as $s)
            <option value="{{ $s->id }}" @selected((string) old('subscription_id', $client->subscription_id) === (string) $s->id)>
              #{{ $s->id }} · {{ $s->plan_label }} · {{ str_replace('_', ' ', $s->status) }} · {{ $s->created_at->format('M j, Y') }}
            </option>
          @endforeach
        </select>
        <span class="hint">
          @if ($subscriptionOptions->isEmpty())
            No checkout subscriptions found for this email — record payments manually or send a payment link below.
          @else
            Payments captured on the linked subscription count toward this client's revenue.
          @endif
        </span>
      </div>

      <div class="plm-fld full">
        <label>Next action</label>
        <input class="plm-input" type="text" name="next_action" maxlength="255" placeholder="{{ $client->suggestedNextAction() }}" value="{{ old('next_action', $client->next_action) }}">
      </div>
      <div class="plm-fld full">
        <label>Notes</label>
        <textarea class="plm-input" name="notes" rows="3" maxlength="5000">{{ old('notes', $client->notes) }}</textarea>
      </div>
    </div>
    <div class="bg-form-foot">
      <button class="adm-btn pink" type="submit">Save changes</button>
    </div>
  </form>
</div>

{{-- ── Rounds ── --}}
<div class="adm-card" style="margin-bottom:16px">
  <div class="adm-card-head">
    <h2>Rounds processed</h2>
    <span class="sub">${{ number_format($roundCost, 2) }} backend cost per round</span>
  </div>

  <form method="POST" action="{{ route('admin.burgundy.rounds.store', $client) }}">
    @csrf
    <div class="bg-form cols-4">
      <div class="plm-fld">
        <label>Round #</label>
        <input class="plm-input" type="number" name="round_number" min="1" max="99" value="{{ $client->current_round + 1 }}">
      </div>
      <div class="plm-fld">
        <label>Processed on</label>
        <input class="plm-input" type="date" name="processed_at" value="{{ $today }}">
      </div>
      <div class="plm-fld">
        <label>Cost</label>
        <input class="plm-input" type="number" name="cost" step="0.01" min="0" value="{{ number_format($roundCost, 2, '.', '') }}">
      </div>
      <div class="plm-fld">
        <label>Note</label>
        <input class="plm-input" type="text" name="note" maxlength="255" placeholder="Optional">
      </div>
    </div>
    <div class="bg-form-foot"><button class="adm-btn" type="submit">Log round</button></div>
  </form>

  @if ($client->rounds->isNotEmpty())
    <div class="adm-table-wrap" style="margin-top:16px"><table class="adm-table">
      <thead><tr><th>Round</th><th>Processed</th><th>Cost</th><th>Note</th><th></th></tr></thead>
      <tbody>
        @foreach ($client->rounds as $round)
          <tr>
            <td><span class="nm">Round {{ $round->round_number }}</span></td>
            <td>{{ $round->processed_at->format('M j, Y') }}</td>
            <td>${{ number_format((float) $round->cost, 2) }}</td>
            <td>{{ $round->note ?: '—' }}</td>
            <td class="actions">
              <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.rounds.destroy', [$client, $round]) }}" data-confirm="Remove round {{ $round->round_number }}?">
                @csrf @method('DELETE')
                <button class="adm-btn danger sm" type="submit">Remove</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @endif
</div>

{{-- ── Payments ── --}}
<div class="adm-card" style="margin-bottom:16px">
  <div class="adm-card-head"><h2>Payments</h2></div>

  <div class="adm-grid-2" style="margin-bottom:6px">
    <form method="POST" action="{{ route('admin.burgundy.payments.store', $client) }}" style="background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:14px 16px">
      @csrf
      <div style="font-weight:700;font-size:13px;margin-bottom:10px">Record a payment</div>
      <div class="bg-form">
        <div class="plm-fld">
          <label>Amount <span class="req">*</span></label>
          <input class="plm-input" type="number" name="amount" step="0.01" min="0.01" required placeholder="{{ $client->monthly_fee ?: '0.00' }}">
        </div>
        <div class="plm-fld">
          <label>Paid on</label>
          <input class="plm-input" type="date" name="paid_at" value="{{ $today }}">
        </div>
        <div class="plm-fld">
          <label>Method</label>
          <input class="plm-input" type="text" name="method" maxlength="40" placeholder="Zelle, Cash App…">
        </div>
        <div class="plm-fld">
          <label>Note</label>
          <input class="plm-input" type="text" name="note" maxlength="255" placeholder="Optional">
        </div>
        <label class="plm-check full"><input type="checkbox" name="prior" value="1"> Paid before the transition (show in Amount Paid, leave out of the profit split)</label>
      </div>
      <div class="bg-form-foot"><button class="adm-btn" type="submit">Record payment</button></div>
    </form>

    <form method="POST" action="{{ route('admin.burgundy.payments.link', $client) }}" style="background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:14px 16px">
      @csrf
      <div style="font-weight:700;font-size:13px;margin-bottom:10px">Send a payment link</div>
      @if ($linksAvailable)
        <div class="bg-form">
          <div class="plm-fld">
            <label>Amount <span class="req">*</span></label>
            <input class="plm-input" type="number" name="amount" step="0.01" min="1" required value="{{ $client->monthly_fee }}">
          </div>
          <div class="plm-fld">
            <label>Note on payment page</label>
            <input class="plm-input" type="text" name="note" maxlength="255" placeholder="Credit Repair — monthly service">
          </div>
        </div>
        <p class="sub" style="font-size:12px;color:var(--ink-3);margin:10px 0 0">Creates a one-time link (also listed under Payment Links). When the client pays, it's counted here automatically.</p>
        <div class="bg-form-foot"><button class="adm-btn pink" type="submit">Generate link</button></div>
      @else
        <p class="sub">Payment links need the <code>payment_links</code> table — run the setup SQL first.</p>
      @endif
    </form>
  </div>

  @if ($client->payments->isNotEmpty() || $gatewayPayments->isNotEmpty())
    <div class="adm-table-wrap" style="margin-top:16px"><table class="adm-table">
      <thead><tr><th>Date</th><th>Amount</th><th>Status</th><th>Source</th><th>Note</th><th></th></tr></thead>
      <tbody>
        @foreach ($client->payments as $p)
          <tr>
            <td style="white-space:nowrap">{{ ($p->paid_at ?? $p->created_at)->format('M j, Y') }}</td>
            <td><span class="nm">${{ number_format((float) $p->amount, 2) }}</span></td>
            <td><span class="badge pay-{{ $p->status }}">{{ $p->status }}</span></td>
            <td>
              <span class="nm" style="font-weight:500">{{ $p->method ?: 'Manual' }}</span>
              @unless ($p->counts_toward_profit)<span class="sub">Before transition · not in profit split</span>@endunless
            </td>
            <td class="wrap">{{ $p->note ?: '—' }}</td>
            <td class="actions">
              @if ($p->status === 'pending' && $p->paymentLink)
                <button type="button" class="adm-btn ghost sm" data-copy-text="{{ $p->paymentLink->url }}">Copy link</button>
              @endif
              <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.payments.destroy', [$client, $p]) }}"
                    data-confirm="{{ $p->status === 'pending' && $p->payment_link_id ? 'Remove this payment record and void its unpaid link?' : 'Remove this payment record?' }}">
                @csrf @method('DELETE')
                <button class="adm-btn danger sm" type="submit">Remove</button>
              </form>
            </td>
          </tr>
        @endforeach
        @foreach ($gatewayPayments as $gp)
          <tr>
            <td style="white-space:nowrap">{{ $gp->charged_at?->format('M j, Y') ?? '—' }}</td>
            <td><span class="nm">{{ $gp->signedAmount() < 0 ? '−' : '' }}${{ number_format((float) $gp->amount, 2) }}</span></td>
            <td><span class="badge {{ $gp->status === 'captured' ? 'pay-paid' : 'pay-unpaid' }}">{{ $gp->status }}</span></td>
            <td>
              <span class="nm" style="font-weight:500">Checkout · {{ $gp->type }}</span>
              <span class="sub">Txn {{ $gp->transaction_id ?: '—' }}</span>
            </td>
            <td>—</td>
            <td class="actions">
              <a class="adm-btn ghost sm" href="{{ route('admin.subscriptions.show', $client->subscription_id) }}">Subscription</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table></div>
  @else
    <p class="sub" style="margin-top:14px;color:var(--ink-3)">No payments yet.</p>
  @endif
</div>

<div style="margin-top:20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
  @if ($client->email)
    <a class="adm-btn" href="mailto:{{ $client->email }}">Email client →</a>
  @endif
  <form class="adm-inline-form" method="POST" action="{{ route('admin.burgundy.destroy', $client) }}"
        data-confirm="Delete {{ $client->full_name }}? This removes the client, their rounds and payment records. It cannot be undone.">
    @csrf @method('DELETE')
    <button class="adm-btn danger" type="submit">Delete client</button>
  </form>
</div>
@endsection
