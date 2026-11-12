@extends('admin.layout')
@section('title', 'Affiliates')

@section('content')
<div class="admin-header">
  <div>
    <h1>Affiliates</h1>
    <div class="sub">Each affiliate gets their own link. When a client they send pays, they are owed their fee straight away.</div>
  </div>
</div>

@if ($needsSetup)
  <div class="adm-card" style="border-color:#ffe4a3;background:#fff8e6;">
    <strong>One-time setup needed.</strong> The affiliate tables don't exist yet — run the affiliate SQL in
    <code>database/setup.sql</code> once via phpMyAdmin, then this page is live.
  </div>
@else

@if (session('new_affiliate_link'))
  <div class="adm-card" style="margin-bottom:18px;border-color:#c9eccd;background:#f0fdf4;">
    <strong style="color:#157a3d;">Their link — copy and send it:</strong>
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px;">
      <input class="adm-input" id="newAffLink" type="text" readonly value="{{ session('new_affiliate_link') }}" onclick="this.select()" style="flex:1;min-width:260px;font-family:ui-monospace,monospace;">
      <button type="button" class="adm-btn" data-copy="{{ session('new_affiliate_link') }}">Copy link</button>
    </div>
  </div>
@endif

<div class="adm-stats" style="grid-template-columns:repeat(3,1fr);">
  <div class="adm-stat"><div class="lab">Referred clients</div><div class="val">{{ $totals['count'] }}</div></div>
  <div class="adm-stat"><div class="lab">Owed to affiliates</div><div class="val">${{ number_format($totals['owed'], 2) }}</div></div>
  <div class="adm-stat"><div class="lab">Already paid out</div><div class="val">${{ number_format($totals['paid'], 2) }}</div></div>
</div>

<div class="adm-card" style="margin-bottom:22px;">
  <h3 style="margin:0 0 12px;">Add an affiliate</h3>
  <form method="POST" action="{{ route('admin.affiliates.store') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;align-items:end;">
    @csrf
    <div><label class="sub">Name *</label><input class="adm-input" name="name" required maxlength="150" value="{{ old('name') }}" placeholder="Jane at the car lot"></div>
    <div><label class="sub">Email</label><input class="adm-input" type="email" name="email" maxlength="150" value="{{ old('email') }}"></div>
    <div><label class="sub">Phone</label><input class="adm-input" name="phone" maxlength="30" value="{{ old('phone') }}"></div>
    <div><label class="sub">Fee per client ($) *</label><input class="adm-input" type="number" step="0.01" min="1" name="payout_amount" required value="{{ old('payout_amount', '100') }}"></div>
    <div><button class="adm-btn" type="submit">Create affiliate link</button></div>
  </form>
  @if ($errors->any())<div style="color:#c0392b;margin-top:10px;">{{ $errors->first() }}</div>@endif
</div>

<div class="adm-card" style="margin-bottom:22px;">
  <h3 style="margin:0 0 12px;">Affiliates</h3>
  @if ($affiliates->isEmpty())
    <div class="empty"><strong>No affiliates yet.</strong>Add one above to get their link.</div>
  @else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>Affiliate</th><th>Link</th><th>Clients</th><th>Owed</th><th>Paid</th><th></th></tr></thead>
    <tbody>
    @foreach ($affiliates as $a)
      <tr>
        <td><span class="nm">{{ $a->name }}</span><span class="sub">{{ $a->email ?: ($a->phone ?: '—') }} · ${{ number_format((float) $a->payout_amount, 0) }}/client</span>
          @unless($a->active)<span class="badge void">off</span>@endunless</td>
        <td><button type="button" class="pl-copy-sm" data-copy="{{ $a->link }}" style="background:transparent;border:1px solid var(--line-2);padding:6px 12px;border-radius:8px;cursor:pointer;">Copy link</button>
          <span class="sub">{{ $a->code }}</span></td>
        <td>{{ $a->referrals_count }}</td>
        <td><strong>${{ number_format((float) $a->owed_sum, 2) }}</strong></td>
        <td>${{ number_format((float) $a->paid_sum, 2) }}</td>
        <td class="actions" style="white-space:nowrap;">
          @if ((float) $a->owed_sum > 0)
            <form method="POST" action="{{ route('admin.affiliates.pay-all', $a) }}" style="display:inline" onsubmit="return confirm('Mark everything owed to {{ addslashes($a->name) }} as paid?');">
              @csrf <button class="adm-btn" type="submit">Mark all paid</button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.affiliates.toggle', $a) }}" style="display:inline">
            @csrf @method('PATCH') <button class="adm-btn ghost" type="submit">{{ $a->active ? 'Turn off' : 'Turn on' }}</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table></div>
  @endif
</div>

<div class="adm-card">
  <h3 style="margin:0 0 12px;">Referred clients</h3>
  @if ($referrals->isEmpty())
    <div class="empty"><strong>No referrals yet.</strong>They appear here the moment a referred client pays.</div>
  @else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead><tr><th>Client</th><th>Plan</th><th>Referred by</th><th>Fee</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
    @foreach ($referrals as $r)
      <tr>
        <td><span class="nm">{{ $r->client_name ?: '—' }}</span><span class="sub">{{ $r->client_email }}</span></td>
        <td>{{ $r->plan_label ?: '—' }}</td>
        <td>{{ $r->affiliate?->name ?: '—' }}</td>
        <td><strong>${{ number_format((float) $r->amount, 2) }}</strong></td>
        <td><span class="badge {{ $r->status === 'paid' ? 'paid' : 'unpaid' }}" style="text-transform:capitalize">{{ $r->status }}</span></td>
        <td>{{ $r->created_at->format('M j · g:ia') }}</td>
        <td class="actions">
          @if ($r->status === 'owed')
            <form method="POST" action="{{ route('admin.affiliates.referral-paid', $r) }}">@csrf <button class="adm-btn ghost" type="submit">Mark paid</button></form>
          @endif
        </td>
      </tr>
    @endforeach
    </tbody>
  </table></div>
  @endif
</div>

<style>
  .badge.unpaid { background:#fff4e5; color:#9a5b00; border:1px solid #ffd8a8; }
  .badge.paid   { background:#e6f6ec; color:#157a3d; border:1px solid #c9eccd; }
  .badge.void   { background:#f1f1f1; color:#777; border:1px solid #e2e2e2; }
</style>
<script>
document.addEventListener('click', function (e) {
  var btn = e.target.closest('[data-copy]'); if (!btn) return;
  var text = btn.getAttribute('data-copy'), old = btn.textContent;
  function done() { btn.textContent = '✓ Copied'; setTimeout(function () { btn.textContent = old; }, 1800); }
  if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); return; }
  var t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select();
  try { document.execCommand('copy'); done(); } catch (_) {} document.body.removeChild(t);
});
</script>
@endif
@endsection
