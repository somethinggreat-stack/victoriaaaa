<style>
  /* ── Burgundy Clients — page-scoped additions on top of the admin layout ── */
  .bg-stats { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:14px; margin-bottom:18px; }
  .bg-stats .adm-stat .val { font-size:26px; }
  .bg-stats .adm-stat .delta a { color:var(--pink); font-weight:600; }
  .bg-label { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin:4px 0 10px; font-size:11px; letter-spacing:.14em; text-transform:uppercase; font-weight:700; color:var(--ink-3); }
  .bg-label form { display:flex; gap:8px; align-items:center; }
  .bg-label .adm-select { padding:6px 10px; font-size:12.5px; text-transform:none; letter-spacing:0; font-weight:600; color:var(--ink); }
  .money-neg { color:#c0392b !important; }
  .money-pos { color:#157a3d !important; }

  .bg-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
  .adm-btn.pink { background:var(--pink); }
  .adm-btn.pink:hover { background:var(--ink); }
  .adm-btn.sm { padding:6px 12px; font-size:12px; }
  table.adm-table .actions { display:flex; gap:6px; align-items:center; }
  table.adm-table td.wrap { white-space:normal; min-width:150px; }

  /* Status filter pills */
  .bg-tabs { display:flex; gap:6px; flex-wrap:wrap; margin:10px 0 14px; }
  .bg-tabs a { padding:7px 14px; border-radius:100px; border:1px solid var(--line-2); background:#fff; font-size:12.5px; font-weight:600; color:var(--ink-2); white-space:nowrap; transition:border-color .2s, color .2s, background .2s; }
  .bg-tabs a .n { margin-left:5px; color:var(--ink-3); font-weight:700; }
  .bg-tabs a:hover { border-color:var(--pink); color:var(--pink); }
  .bg-tabs a.on { background:var(--ink); border-color:var(--ink); color:#fff; }
  .bg-tabs a.on .n { color:rgba(255,255,255,.6); }

  /* Client status + payment status badges */
  .badge.st-invited         { background:var(--pink-soft); color:var(--pink); }
  .badge.st-signed_up       { background:#e0f2fe; color:#075985; }
  .badge.st-payment_pending { background:#fef3c7; color:#92400e; }
  .badge.st-paid            { background:#ecfdf5; color:#047857; }
  .badge.st-active          { background:#f0fdf4; color:#157a3d; }
  .badge.st-paused          { background:#f3e8ff; color:#6b21a8; }
  .badge.st-cancelled       { background:var(--bg-2); color:var(--ink-3); }
  .badge.pay-paid     { background:#e6f6ec; color:#157a3d; }
  .badge.pay-pending  { background:#fff4e5; color:#9a5b00; }
  .badge.pay-past_due { background:#fee2e2; color:#991b1b; }
  .badge.pay-unpaid,
  .badge.pay-void     { background:var(--bg-2); color:var(--ink-3); }
  .badge.exp-approved { background:#e6f6ec; color:#157a3d; }
  .badge.exp-pending  { background:#fff4e5; color:#9a5b00; }
  .badge.exp-rejected { background:var(--bg-2); color:var(--ink-3); }

  .bg-setup { background:#fff8e6; border:1px solid #ffe4a3; border-radius:12px; padding:18px 20px; margin-bottom:22px; font-size:14px; color:#7a5b00; line-height:1.6; }
  .bg-setup code { background:#fff; border:1px solid #ffe4a3; padding:2px 6px; border-radius:5px; }

  .bg-generated { background:linear-gradient(180deg,#f0fdf4,#ffffff); border:1px solid #c9eccd; border-radius:16px; padding:16px 18px; margin-bottom:18px; }
  .bg-generated .h { font-size:13.5px; font-weight:700; color:#157a3d; margin-bottom:10px; }
  .bg-linkrow { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .bg-linkrow input { flex:1; min-width:220px; padding:10px 12px; border:1.5px solid #bfe6c8; border-radius:10px; font-family:ui-monospace,monospace; font-size:13px; background:#fff; color:var(--ink); }

  /* Forms (detail page + modals) */
  .bg-form { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px 16px; }
  .bg-form.cols-4 { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  .bg-form .full { grid-column:1 / -1; }
  .plm-fld { display:flex; flex-direction:column; gap:6px; min-width:0; }
  .plm-fld label { font-size:11.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
  .plm-fld label .req { color:var(--pink); }
  .plm-fld .hint { font-size:11.5px; color:var(--ink-3); }
  .plm-input { width:100%; padding:10px 12px; border:1.5px solid var(--line-2); border-radius:11px; font-family:inherit; font-size:14px; color:var(--ink); background:#fff; transition:border-color .2s, box-shadow .2s; }
  .plm-input:focus { outline:none; border-color:var(--pink); box-shadow:0 0 0 4px rgba(230,49,121,.10); }
  textarea.plm-input { resize:vertical; min-height:90px; }
  .plm-check { display:flex; gap:8px; align-items:flex-start; font-size:13px; color:var(--ink-2); cursor:pointer; }
  .plm-check input { margin-top:3px; accent-color:var(--pink); }
  .plm-fld-err { color:#c0392b; font-size:12.5px; }
  .bg-form-foot { display:flex; gap:10px; justify-content:flex-end; margin-top:16px; flex-wrap:wrap; }

  /* Modal — same look as the Payment Links modal */
  .plm-overlay { position:fixed; inset:0; z-index:2000; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(20,16,14,.55); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px); }
  .plm-overlay.open { display:flex; }
  .plm-card { width:100%; max-width:560px; max-height:calc(100vh - 40px); overflow-y:auto; background:#fff; border-radius:20px; box-shadow:0 50px 100px -30px rgba(0,0,0,.45); transform:translateY(14px) scale(.98); transition:transform .25s cubic-bezier(.2,1,.3,1); }
  .plm-overlay.open .plm-card { transform:none; }
  .plm-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:22px 24px 16px; }
  .plm-head h3 { margin:0 0 4px; font-size:19px; }
  .plm-head .sub { font-size:13px; color:var(--ink-3); }
  .plm-close { flex:0 0 auto; width:32px; height:32px; border-radius:50%; border:none; background:rgba(20,16,14,.06); color:var(--ink); font-size:19px; line-height:1; cursor:pointer; transition:background .2s, transform .2s; }
  .plm-close:hover { background:rgba(20,16,14,.12); transform:rotate(90deg); }
  .plm-body { padding:4px 24px 8px; }
  .plm-foot { display:flex; gap:10px; justify-content:flex-end; padding:18px 24px 24px; }
  .plm-btn-primary { background:var(--pink); color:#fff; border:none; padding:12px 24px; border-radius:100px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; box-shadow:0 12px 26px -12px rgba(230,49,121,.55); transition:background .2s, transform .2s; }
  .plm-btn-primary:hover { background:var(--ink); transform:translateY(-1px); }
  .plm-btn-ghost { background:transparent; border:1px solid var(--line-2); color:var(--ink); padding:12px 20px; border-radius:100px; font-weight:600; font-size:14px; cursor:pointer; font-family:inherit; }
  .bg-csv-help { font-size:12.5px; color:var(--ink-2); background:var(--bg-2); border-radius:10px; padding:10px 12px; margin-bottom:12px; line-height:1.55; }
  .bg-csv-help code { font-size:11.5px; word-break:break-word; }

  @media (max-width: 900px) {
    .bg-form.cols-4 { grid-template-columns:repeat(2, minmax(0, 1fr)); }
    .bg-actions { width:100%; }
  }
  @media (max-width: 560px) {
    .bg-form, .bg-form.cols-4 { grid-template-columns:1fr; }
    .bg-stats { grid-template-columns:1fr 1fr; gap:10px; }
    .bg-stats .adm-stat .val { font-size:19px; }
    .plm-foot { flex-direction:column-reverse; }
    .plm-btn-primary, .plm-btn-ghost { width:100%; }
  }
  @media (max-width: 360px) {
    .bg-stats { grid-template-columns:1fr; }
  }
</style>

<script>
(function () {
  if (window.__bgAdminInit) return; window.__bgAdminInit = true;

  // Modals: [data-modal-open="id"] opens, [data-modal-close] / backdrop / Esc closes.
  document.addEventListener('click', function (e) {
    const opener = e.target.closest('[data-modal-open]');
    if (opener) {
      const m = document.getElementById(opener.getAttribute('data-modal-open'));
      if (m) { m.classList.add('open'); const f = m.querySelector('input:not([type=hidden]),textarea'); if (f) setTimeout(() => f.focus(), 50); }
      return;
    }
    if (e.target.closest('[data-modal-close]') || e.target.classList.contains('plm-overlay')) {
      const m = e.target.closest('.plm-overlay'); if (m) m.classList.remove('open');
    }

    const copy = e.target.closest('[data-copy-text]');
    if (copy) {
      const text = copy.getAttribute('data-copy-text'); const old = copy.textContent;
      const done = () => { copy.textContent = '✓ Copied'; setTimeout(() => copy.textContent = old, 1600); };
      (navigator.clipboard ? navigator.clipboard.writeText(text) : Promise.reject()).then(done).catch(() => {
        const t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select();
        try { document.execCommand('copy'); done(); } catch (_) {} document.body.removeChild(t);
      });
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.querySelectorAll('.plm-overlay.open').forEach(m => m.classList.remove('open'));
  });

  // Confirmations read from data-confirm so names with quotes can't break the JS.
  document.addEventListener('submit', function (e) {
    const msg = e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  }, true);
})();
</script>
