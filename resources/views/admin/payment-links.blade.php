@extends('admin.layout')
@section('title', 'Payment Links')

@section('content')
<div class="admin-header">
  <div>
    <h1>Payment Links</h1>
    <div class="sub">Generate a one-time payment link for a client and send it to them directly.</div>
  </div>
  @unless ($needsSetup)
    <button type="button" class="adm-btn plm-open-btn" id="plmOpen">+ Create Payment Link</button>
  @endunless
</div>

<style>
  .plm-open-btn { background: var(--pink); white-space: nowrap; }
  .plm-open-btn:hover { background: var(--ink); }

  .badge.unpaid { background:#fff4e5; color:#9a5b00; border:1px solid #ffd8a8; text-transform:capitalize; }
  .badge.paid   { background:#e6f6ec; color:#157a3d; border:1px solid #c9eccd; text-transform:capitalize; }
  .badge.void   { background:#f1f1f1; color:#777;    border:1px solid #e2e2e2; text-transform:capitalize; }

  /* Freshly generated link banner */
  .pl-generated { background:linear-gradient(180deg,#f0fdf4,#ffffff); border:1px solid #c9eccd; border-radius:16px; padding:18px 20px; margin-bottom:22px; }
  .pl-generated .h { font-size:13.5px; font-weight:700; color:#157a3d; margin-bottom:11px; display:flex; align-items:center; gap:8px; }
  .pl-linkrow { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .pl-linkrow input { flex:1; min-width:260px; padding:12px 14px; border:1.5px solid #bfe6c8; border-radius:10px; font-family:ui-monospace,monospace; font-size:13.5px; background:#fff; color:var(--ink); }
  .pl-copy { background:var(--pink); color:#fff; border:none; padding:12px 18px; border-radius:10px; font-weight:700; font-size:13.5px; cursor:pointer; white-space:nowrap; }
  .pl-copy.copied { background:#157a3d; }
  .pl-copy-sm { background:transparent; border:1px solid var(--line-2); color:var(--ink); padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; }
  .pl-copy-sm.copied { border-color:#157a3d; color:#157a3d; }

  .pl-setup-note { background:#fff8e6; border:1px solid #ffe4a3; border-radius:12px; padding:18px 20px; margin-bottom:22px; font-size:14px; color:#7a5b00; line-height:1.6; }
  .pl-setup-note code { background:#fff; border:1px solid #ffe4a3; padding:2px 6px; border-radius:5px; }
  .pl-err { background:#fdecec; border:1px solid #f1b5b5; color:#8a1f1f; border-radius:12px; padding:14px 18px; margin-bottom:20px; font-size:14px; }

  /* ── Create-link modal ── */
  .plm-overlay { position:fixed; inset:0; z-index:2000; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(20,16,14,.55); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px); }
  .plm-overlay.open { display:flex; }
  .plm-card { width:100%; max-width:470px; background:#fff; border-radius:20px; box-shadow:0 50px 100px -30px rgba(0,0,0,.45); overflow:hidden; transform:translateY(14px) scale(.98); transition:transform .25s cubic-bezier(.2,1,.3,1); }
  .plm-overlay.open .plm-card { transform:none; }
  .plm-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:22px 24px 16px; }
  .plm-head h3 { margin:0 0 4px; font-size:19px; }
  .plm-head .sub { font-size:13px; color:var(--ink-3); }
  .plm-close { flex:0 0 auto; width:32px; height:32px; border-radius:50%; border:none; background:rgba(20,16,14,.06); color:var(--ink); font-size:19px; line-height:1; cursor:pointer; transition:background .2s, transform .2s; }
  .plm-close:hover { background:rgba(20,16,14,.12); transform:rotate(90deg); }
  .plm-body { padding:4px 24px 8px; display:flex; flex-direction:column; gap:15px; }
  .plm-fld { display:flex; flex-direction:column; gap:6px; }
  .plm-fld label { font-size:11.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
  .plm-fld label .req { color:var(--pink); }
  .plm-input { width:100%; padding:12px 14px; border:1.5px solid var(--line-2); border-radius:11px; font-family:inherit; font-size:15px; color:var(--ink); background:#fff; transition:border-color .2s, box-shadow .2s; }
  .plm-input:focus { outline:none; border-color:var(--pink); box-shadow:0 0 0 4px rgba(230,49,121,.10); }
  .plm-amount { position:relative; }
  .plm-amount .cur { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#999; font-weight:600; }
  .plm-amount .plm-input { padding-left:27px; }
  .plm-foot { display:flex; gap:10px; justify-content:flex-end; padding:18px 24px 24px; }
  .plm-btn-primary { background:var(--pink); color:#fff; border:none; padding:12px 24px; border-radius:100px; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 12px 26px -12px rgba(230,49,121,.55); transition:background .2s, transform .2s; }
  .plm-btn-primary:hover { background:var(--ink); transform:translateY(-1px); }
  .plm-btn-ghost { background:transparent; border:1px solid var(--line-2); color:var(--ink); padding:12px 20px; border-radius:100px; font-weight:600; font-size:14px; cursor:pointer; }
  .plm-fld-err { color:#c0392b; font-size:12.5px; }
  @media (max-width:560px){ .plm-foot{ flex-direction:column-reverse; } .plm-btn-primary,.plm-btn-ghost{ width:100%; } }
</style>

@if (session('error'))
  <div class="pl-err">{{ session('error') }}</div>
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
    <div class="empty"><strong>No payment links yet.</strong>Click <em>Create Payment Link</em> above to make one and send it to your client.</div>
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
            <td><span class="badge {{ $r->status }}">{{ $r->status }}</span></td>
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

  {{-- ── Create Payment Link modal ── --}}
  <div class="plm-overlay {{ $errors->any() ? 'open' : '' }}" id="plmOverlay" role="dialog" aria-modal="true" aria-labelledby="plmTitle">
    <div class="plm-card">
      <div class="plm-head">
        <div>
          <h3 id="plmTitle">Create a payment link</h3>
          <div class="sub">Enter the client and amount — you'll get a link to send.</div>
        </div>
        <button type="button" class="plm-close" id="plmClose" aria-label="Close">×</button>
      </div>

      <form method="POST" action="{{ route('admin.payment-links.store') }}">
        @csrf
        <div class="plm-body">
          <div class="plm-fld">
            <label>Client name <span class="req">*</span></label>
            <input class="plm-input" type="text" name="client_name" maxlength="150" required autofocus placeholder="Jane Smith" value="{{ old('client_name') }}">
            @error('client_name')<div class="plm-fld-err">{{ $message }}</div>@enderror
          </div>

          <div class="plm-fld">
            <label>Amount — one-time <span class="req">*</span></label>
            <div class="plm-amount">
              <span class="cur">$</span>
              <input class="plm-input" type="number" name="amount" step="0.01" min="1" max="100000" required placeholder="497.00" value="{{ old('amount') }}">
            </div>
            @error('amount')<div class="plm-fld-err">{{ $message }}</div>@enderror
          </div>

          <div class="plm-fld">
            <label>Client email <span style="text-transform:none;font-weight:500;color:var(--ink-3)">(optional)</span></label>
            <input class="plm-input" type="email" name="email" maxlength="150" placeholder="client@email.com" value="{{ old('email') }}">
            @error('email')<div class="plm-fld-err">{{ $message }}</div>@enderror
          </div>

          <div class="plm-fld">
            <label>Note on payment page <span style="text-transform:none;font-weight:500;color:var(--ink-3)">(optional)</span></label>
            <input class="plm-input" type="text" name="note" maxlength="255" placeholder="e.g. Credit Repair — final payment" value="{{ old('note') }}">
            @error('note')<div class="plm-fld-err">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="plm-foot">
          <button type="button" class="plm-btn-ghost" id="plmCancel">Cancel</button>
          <button type="submit" class="plm-btn-primary">Generate link</button>
        </div>
      </form>
    </div>
  </div>
@endunless

<script>
(function () {
  /* Copy buttons */
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
      const t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select();
      try { document.execCommand('copy'); flash(btn); } catch (_) {}
      document.body.removeChild(t);
    });
  });

  /* Modal open/close */
  const overlay = document.getElementById('plmOverlay');
  const openBtn = document.getElementById('plmOpen');
  if (!overlay) return;
  const open = () => { overlay.classList.add('open'); const f = overlay.querySelector('input[name="client_name"]'); if (f) setTimeout(() => f.focus(), 50); };
  const close = () => overlay.classList.remove('open');
  if (openBtn) openBtn.addEventListener('click', open);
  const closeBtn = document.getElementById('plmClose');
  const cancelBtn = document.getElementById('plmCancel');
  if (closeBtn) closeBtn.addEventListener('click', close);
  if (cancelBtn) cancelBtn.addEventListener('click', close);
  overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && overlay.classList.contains('open')) close(); });
})();
</script>
@endsection
