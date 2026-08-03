@extends('admin.layout')
@section('title', 'Payment Links')

@section('content')
<div class="admin-header">
  <div>
    <h1>Payment Links</h1>
    <div class="sub">Generate a one-time payment link for a client and send it to them directly.</div>
  </div>
</div>

<style>
  .pl-badge { text-transform: capitalize; }
  .badge.unpaid { background:#fff4e5; color:#9a5b00; border:1px solid #ffd8a8; }
  .badge.paid   { background:#e6f6ec; color:#157a3d; border:1px solid #c9eccd; }
  .badge.void   { background:#f1f1f1; color:#777;    border:1px solid #e2e2e2; }

  .pl-create { display:grid; grid-template-columns: 1.4fr 0.8fr 1.2fr 1.6fr auto; gap:12px; align-items:end; }
  .pl-fld { display:flex; flex-direction:column; gap:5px; }
  .pl-fld label { font-size:11.5px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:var(--adm-ink-3, #7a7a7a); }
  .pl-amount-wrap { position:relative; }
  .pl-amount-wrap .cur { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#999; font-weight:600; }
  .pl-amount-wrap input { padding-left:26px !important; }
  @media (max-width: 1000px){ .pl-create { grid-template-columns:1fr 1fr; } }

  .pl-generated {
    background:linear-gradient(180deg,#f0fdf4,#ffffff); border:1px solid #c9eccd;
    border-radius:14px; padding:18px 20px; margin-bottom:22px;
  }
  .pl-generated .h { font-size:13px; font-weight:700; color:#157a3d; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
  .pl-linkrow { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .pl-linkrow input {
    flex:1; min-width:260px; padding:12px 14px; border:1.5px solid #bfe6c8; border-radius:10px;
    font-family:ui-monospace,monospace; font-size:13.5px; background:#fff; color:var(--adm-ink,#222);
  }
  .pl-copy { background:var(--adm-accent,#e63179); color:#fff; border:none; padding:12px 18px; border-radius:10px; font-weight:700; font-size:13.5px; cursor:pointer; white-space:nowrap; }
  .pl-copy.copied { background:#157a3d; }
  .pl-copy-sm { background:transparent; border:1px solid var(--adm-line,#e2e2e2); color:var(--adm-ink,#333); padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; }
  .pl-copy-sm.copied { border-color:#157a3d; color:#157a3d; }
  .pl-setup-note { background:#fff8e6; border:1px solid #ffe4a3; border-radius:12px; padding:18px 20px; margin-bottom:22px; font-size:14px; color:#7a5b00; line-height:1.6; }
  .pl-setup-note code { background:#fff; border:1px solid #ffe4a3; padding:2px 6px; border-radius:5px; }
</style>

@if (session('error'))
  <div class="pl-setup-note" style="background:#fdecec;border-color:#f1b5b5;color:#8a1f1f;">{{ session('error') }}</div>
@endif

@if ($needsSetup)
  <div class="pl-setup-note">
    <strong>One-time setup needed.</strong> The <code>payment_links</code> table doesn't exist yet.
    Run the setup SQL (in <code>database/setup.sql</code>) once via phpMyAdmin, then this page is fully live.
  </div>
@endif

{{-- Freshly generated link — copy + send --}}
@if (session('generated_link'))
  <div class="pl-generated">
    <div class="h">✓ Link ready for {{ session('generated_name') }} · {{ session('generated_amount') }}</div>
    <div class="pl-linkrow">
      <input type="text" id="freshLink" value="{{ session('generated_link') }}" readonly onclick="this.select()">
      <button type="button" class="pl-copy" data-copy="#freshLink">Copy link</button>
      <a class="pl-copy-sm" href="{{ session('generated_link') }}" target="_blank" rel="noopener">Open</a>
    </div>
  </div>
@endif

{{-- Create a new link --}}
@unless ($needsSetup)
<div class="adm-card" style="margin-bottom:22px; padding:22px;">
  <form method="POST" action="{{ route('admin.payment-links.store') }}">
    @csrf
    <div class="pl-create">
      <div class="pl-fld">
        <label>Client name <span style="color:var(--adm-accent,#e63179)">*</span></label>
        <input class="adm-input" type="text" name="client_name" maxlength="150" required placeholder="Jane Smith" value="{{ old('client_name') }}">
      </div>
      <div class="pl-fld">
        <label>Amount (one-time) <span style="color:var(--adm-accent,#e63179)">*</span></label>
        <div class="pl-amount-wrap">
          <span class="cur">$</span>
          <input class="adm-input" type="number" name="amount" step="0.01" min="1" max="100000" required placeholder="497.00" value="{{ old('amount') }}">
        </div>
      </div>
      <div class="pl-fld">
        <label>Client email (optional)</label>
        <input class="adm-input" type="email" name="email" maxlength="150" placeholder="client@email.com" value="{{ old('email') }}">
      </div>
      <div class="pl-fld">
        <label>Note on payment page (optional)</label>
        <input class="adm-input" type="text" name="note" maxlength="255" placeholder="e.g. Credit Repair — final payment" value="{{ old('note') }}">
      </div>
      <div class="pl-fld">
        <label>&nbsp;</label>
        <button class="adm-btn" type="submit">Generate link</button>
      </div>
    </div>
    @error('client_name')<div style="color:#c0392b;font-size:12.5px;margin-top:8px;">{{ $message }}</div>@enderror
    @error('amount')<div style="color:#c0392b;font-size:12.5px;margin-top:8px;">{{ $message }}</div>@enderror
    @error('email')<div style="color:#c0392b;font-size:12.5px;margin-top:8px;">{{ $message }}</div>@enderror
  </form>
</div>
@endunless

@unless ($needsSetup)
  {{-- KPIs --}}
  <div class="adm-kpis" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Total links</div><div style="font-size:24px;font-weight:700;">{{ $kpis['total'] }}</div></div>
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Unpaid</div><div style="font-size:24px;font-weight:700;">{{ $kpis['unpaid'] }}</div></div>
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Paid</div><div style="font-size:24px;font-weight:700;">{{ $kpis['paid'] }}</div></div>
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Collected</div><div style="font-size:24px;font-weight:700;">${{ number_format($kpis['collected'], 2) }}</div></div>
    <div class="adm-card" style="padding:16px 18px;"><div class="sub">Outstanding</div><div style="font-size:24px;font-weight:700;">${{ number_format($kpis['outstanding'], 2) }}</div></div>
  </div>

  <div class="adm-toolbar">
    <form method="GET" action="{{ route('admin.payment-links') }}">
      <input class="adm-input" type="search" name="q" placeholder="Search client, email, invoice, transaction" value="{{ request('q') }}">
      <select class="adm-select" name="status">
        <option value="">All statuses</option>
        @foreach (['unpaid','paid','void'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
      <button class="adm-btn" type="submit">Search</button>
    </form>
  </div>

  @if ($rows->isEmpty())
    <div class="empty"><strong>No payment links yet.</strong>Create one above and send it to your client.</div>
  @else
    <div class="adm-table-wrap"><table class="adm-table">
      <thead>
        <tr><th>Client</th><th>Amount</th><th>Status</th><th>Link</th><th>Paid details</th><th>Created</th><th></th></tr>
      </thead>
      <tbody>
        @foreach ($rows as $r)
          <tr>
            <td>
              <span class="nm">{{ $r->client_name }}</span>
              <span class="sub">{{ $r->email ?: '—' }}</span>
              @if($r->note)<span class="sub" style="opacity:.8">{{ $r->note }}</span>@endif
            </td>
            <td><strong>${{ number_format((float) $r->amount, 2) }}</strong></td>
            <td><span class="badge pl-badge {{ $r->status }}">{{ $r->status }}</span></td>
            <td>
              @if($r->status === 'unpaid')
                <button type="button" class="pl-copy-sm" data-copy-text="{{ $r->url }}">Copy link</button>
                <a class="pl-copy-sm" href="{{ $r->url }}" target="_blank" rel="noopener">Open</a>
              @else
                <span class="sub">—</span>
              @endif
            </td>
            <td>
              @if($r->status === 'paid')
                <span class="sub">Txn {{ $r->transaction_id ?: '—' }}</span>
                <span class="sub">{{ optional($r->paid_at)->format('M j · g:ia') }}</span>
              @else
                <span class="sub">—</span>
              @endif
            </td>
            <td>{{ $r->created_at->format('M j · g:ia') }}</td>
            <td class="actions">
              @if($r->status === 'unpaid')
                <form method="POST" action="{{ route('admin.payment-links.void', $r) }}" onsubmit="return confirm('Void this link so it can no longer be paid?');">
                  @csrf @method('PATCH')
                  <button class="adm-btn ghost" type="submit">Void</button>
                </form>
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
@endunless

<script>
(function () {
  function flash(btn) {
    const old = btn.textContent;
    btn.classList.add('copied'); btn.textContent = '✓ Copied';
    setTimeout(() => { btn.classList.remove('copied'); btn.textContent = old; }, 1800);
  }
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-copy], [data-copy-text]');
    if (!btn) return;
    let text = btn.getAttribute('data-copy-text');
    if (!text) { const sel = btn.getAttribute('data-copy'); const el = sel && document.querySelector(sel); text = el ? el.value : ''; }
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => flash(btn)).catch(() => {
      // Fallback for older browsers
      const t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select();
      try { document.execCommand('copy'); flash(btn); } catch (_) {}
      document.body.removeChild(t);
    });
  });
})();
</script>
@endsection
