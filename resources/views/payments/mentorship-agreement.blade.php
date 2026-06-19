@extends('layouts.app')

@section('title', 'Payment Agreement — ' . $terms['plan_label'] . ' | Victoria Love')
@section('description', 'Review and sign your mentorship payment agreement before checkout.')
@section('bodyClass', 'page-checkout')

@php
  $depositFmt = '$' . number_format($terms['deposit_amount'], 0);
  $instFmt    = '$' . number_format($terms['installment_amount'], 0);
  $count      = $terms['installment_count'];
  $totalFmt   = '$' . number_format($terms['total_amount'], 0);
  $today      = now()->format('F j, Y');
@endphp

@section('content')

<style>
.agr-hero { padding: 130px 0 20px; text-align: center; }
.agr-hero .eyebrow { margin-bottom: 14px; }
.agr-hero h1 { font-size: clamp(2rem, 3.6vw, 2.7rem); margin-bottom: 10px; }
.agr-hero p { font-size: 16px; max-width: 560px; margin: 0 auto; color: var(--ink-2); }

.agr-section { padding: 24px 0 100px; }
.agr-section .container { max-width: 820px; }

.agr-card {
  background: var(--bg-3, #fff);
  border: 1px solid var(--line);
  border-radius: var(--r-lg, 20px);
  padding: 40px 44px;
  box-shadow: 0 30px 60px -30px rgba(20,16,14,0.10);
}

/* Plan terms strip */
.agr-terms {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 30px;
}
.agr-term {
  background: var(--pink-tint, #fff5f9);
  border: 1px solid rgba(230,49,121,0.16);
  border-radius: var(--r-md, 14px);
  padding: 16px 18px;
  text-align: center;
}
.agr-term .v { font-size: 24px; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; }
.agr-term .l { font-size: 11.5px; font-weight: 600; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px; }

/* Contract body */
.agr-doc {
  border: 1px solid var(--line);
  border-radius: var(--r-md, 14px);
  background: #fff;
  padding: 28px 30px;
  max-height: 380px;
  overflow-y: auto;
  margin-bottom: 12px;
}
.agr-doc h2 { font-size: 17px; margin: 0 0 4px; }
.agr-doc .meta { font-size: 12.5px; color: var(--ink-3); margin-bottom: 18px; }
.agr-doc ol { margin: 0; padding-left: 18px; }
.agr-doc li { margin-bottom: 14px; font-size: 14px; line-height: 1.6; color: var(--ink-2); }
.agr-doc li strong { color: var(--ink); }
.agr-doc .sched { list-style: none; padding: 8px 0 0; margin: 8px 0 0; }
.agr-doc .sched li { display: flex; gap: 10px; margin-bottom: 8px; }
.agr-doc .sched li::before { content: '•'; color: var(--pink); font-weight: 800; }
.agr-scroll-note { font-size: 12px; color: var(--ink-3); margin: 0 0 26px; text-align: center; }

/* Signature block */
.agr-sign-head { font-size: 12px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 16px; display:flex; align-items:center; gap:10px; }
.agr-sign-head::before { content:''; width:18px; height:1px; background: var(--pink); }

.agr-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 22px; }
.agr-field > span { font-size: 12.5px; font-weight: 600; color: var(--ink); }
.agr-field > span em { color: var(--pink); font-style: normal; }
.agr-input {
  width: 100%; padding: 14px 15px;
  border: 1.5px solid var(--line-2); border-radius: var(--r-sm, 10px);
  background: #fff; font-family: inherit; font-size: 16px; color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
}
.agr-input:focus { outline: none; border-color: var(--pink); box-shadow: 0 0 0 4px rgba(230,49,121,0.10); }
.agr-input.is-invalid { border-color: #d93838; box-shadow: 0 0 0 4px rgba(217,56,56,0.10); }

/* Signature pad */
.agr-pad-wrap { margin-bottom: 8px; }
.agr-pad-label { display:flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.agr-pad-label span { font-size: 12.5px; font-weight: 600; color: var(--ink); }
.agr-pad-label em { color: var(--pink); font-style: normal; }
.agr-pad-clear {
  font-size: 12px; font-weight: 600; color: var(--ink-3);
  background: transparent; border: 0; cursor: pointer; text-decoration: underline; text-underline-offset: 3px;
}
.agr-pad-clear:hover { color: var(--pink); }
.agr-pad {
  position: relative;
  border: 1.5px dashed var(--line-2);
  border-radius: var(--r-sm, 10px);
  background: #fffdfa;
  touch-action: none;
}
.agr-pad.is-invalid { border-color: #d93838; }
.agr-pad canvas { display: block; width: 100%; height: 180px; border-radius: inherit; cursor: crosshair; }
.agr-pad .agr-pad-base {
  position: absolute; left: 0; right: 0; bottom: 38px;
  margin: 0 24px; border-bottom: 1px solid var(--line-2);
  pointer-events: none;
}
.agr-pad .agr-pad-x {
  position: absolute; left: 24px; bottom: 40px;
  font-size: 18px; color: var(--ink-3); pointer-events: none;
}
.agr-pad .agr-pad-hint {
  position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
  color: var(--ink-3); font-size: 14px; pointer-events: none; transition: opacity .2s;
}
.agr-pad.has-ink .agr-pad-hint { opacity: 0; }

.agr-agree {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 16px 18px; margin: 24px 0 8px;
  background: var(--bg); border: 1px solid var(--line); border-radius: var(--r-sm, 10px);
  font-size: 13.5px; line-height: 1.55; color: var(--ink-2); cursor: pointer;
}
.agr-agree input { margin-top: 3px; width: 18px; height: 18px; flex: 0 0 18px; accent-color: var(--pink); cursor: pointer; }
.agr-agree strong { color: var(--ink); }

.agr-alert {
  display:none; background:#fce8e8; border:1px solid #f1b5b5; color:#8a1f1f;
  padding:13px 16px; border-radius: var(--r-sm,10px); margin: 16px 0; font-size: 14px;
}
.agr-alert.show { display:block; }

.agr-submit {
  width: 100%; margin-top: 22px;
  background: var(--pink); color: #fff; border: none;
  padding: 18px 24px; border-radius: 100px;
  font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer;
  display:inline-flex; align-items:center; justify-content:center; gap:10px;
  box-shadow: 0 16px 36px -12px rgba(230,49,121,0.55);
  transition: background .25s, transform .25s, box-shadow .25s;
}
.agr-submit:hover:not(:disabled) { background: var(--ink); transform: translateY(-2px); }
.agr-submit:disabled { opacity: .55; cursor: not-allowed; }
.agr-fine { text-align:center; font-size: 12px; color: var(--ink-3); margin-top: 14px; }

@media (max-width: 600px) {
  .agr-card { padding: 26px 20px; }
  .agr-terms { grid-template-columns: 1fr; }
  .agr-doc { padding: 20px 18px; }
}
</style>

<section class="agr-hero">
  <div class="container">
    <span class="eyebrow">Step 1 of 2 · Payment agreement</span>
    <h1>Review &amp; <em class="serif gradient-text">sign your agreement</em></h1>
    <p>Please read your payment plan below and sign. Once signed, you'll continue to secure checkout to complete your deposit.</p>
  </div>
</section>

<section class="agr-section">
  <div class="container">
    <div class="agr-card reveal">

      <div class="agr-terms">
        <div class="agr-term"><div class="v">{{ $depositFmt }}</div><div class="l">Deposit today</div></div>
        <div class="agr-term"><div class="v">{{ $count }} × {{ $instFmt }}</div><div class="l">Monthly payments</div></div>
        <div class="agr-term"><div class="v">{{ $totalFmt }}</div><div class="l">Total investment</div></div>
      </div>

      <div class="agr-doc" id="agrDoc">
        <h2>1:1 Mentorship Program — Payment Agreement</h2>
        <div class="meta">{{ $terms['plan_label'] }} · {{ $today }}</div>
        <ol>
          <li>This Payment Agreement is entered into between <strong>Victorious Opportunities</strong> (“Company”) and the undersigned (“Client”).</li>
          <li><strong>Total investment.</strong> Client enrolls in the 1:1 Mentorship Program for a total of <strong>{{ $totalFmt }}</strong>.</li>
          <li>
            <strong>Payment schedule.</strong> Client agrees to the following schedule:
            <ul class="sched">
              <li>A deposit of <strong>{{ $depositFmt }}</strong> charged today, {{ $today }}.</li>
              <li>Followed by <strong>{{ $count }} monthly payments of {{ $instFmt }}</strong> each, beginning approximately 30 days after the deposit and recurring monthly until the total is paid in full.</li>
            </ul>
          </li>
          <li><strong>Authorization.</strong> Client authorizes the Company to automatically charge the payment method provided at checkout for each scheduled payment on its due date, processed securely through Authorize.Net.</li>
          <li><strong>Commitment.</strong> The deposit is non-refundable. Client remains responsible for the full total above regardless of program usage. Missed or failed payments may pause program access until the balance is current.</li>
          <li><strong>E-signature.</strong> By typing my full legal name and drawing my signature below, I acknowledge that I have read, understand, and agree to be legally bound by this Agreement, and that my electronic signature is the legal equivalent of my handwritten signature.</li>
        </ol>
      </div>
      <p class="agr-scroll-note">Scroll inside the box to read the full agreement.</p>

      <form id="agrForm" novalidate>
        @csrf
        <input type="hidden" name="selected_plan" value="{{ $planKey }}">
        <input type="hidden" name="signature_data" id="agrSignatureData">

        <div class="agr-sign-head">Sign below</div>

        <label class="agr-field">
          <span>Full legal name <em>*</em></span>
          <input class="agr-input" type="text" name="full_name" id="agrName" maxlength="150" autocomplete="name" placeholder="Type your full legal name">
        </label>

        <div class="agr-pad-wrap">
          <div class="agr-pad-label">
            <span>Draw your signature <em>*</em></span>
            <button type="button" class="agr-pad-clear" id="agrClear">Clear</button>
          </div>
          <div class="agr-pad" id="agrPad">
            <canvas id="agrCanvas"></canvas>
            <div class="agr-pad-base"></div>
            <div class="agr-pad-x">✕</div>
            <div class="agr-pad-hint">Sign here with your mouse or finger</div>
          </div>
        </div>

        <label class="agr-agree">
          <input type="checkbox" name="agree_terms" id="agrAgree" value="1">
          <span>I, the undersigned, have read and agree to the terms of this <strong>Payment Agreement</strong>, and authorize the scheduled charges described above. My electronic signature is legally binding.</span>
        </label>

        <div class="agr-alert" id="agrAlert"></div>

        <button type="submit" class="agr-submit" id="agrSubmit">
          Agree &amp; continue to checkout <span class="arr">→</span>
        </button>
        <p class="agr-fine">🔒 Your signature is recorded with a timestamp and securely stored. Next: enter your payment details.</p>
      </form>

    </div>
  </div>
</section>

<script>
(function () {
  const form    = document.getElementById('agrForm');
  const pad     = document.getElementById('agrPad');
  const canvas  = document.getElementById('agrCanvas');
  const clearBtn= document.getElementById('agrClear');
  const nameEl  = document.getElementById('agrName');
  const agreeEl = document.getElementById('agrAgree');
  const sigInput= document.getElementById('agrSignatureData');
  const submit  = document.getElementById('agrSubmit');
  const alertBox= document.getElementById('agrAlert');
  const ctx     = canvas.getContext('2d');

  let drawing = false, hasInk = false, lastX = 0, lastY = 0;

  /* Size the canvas crisply for the device pixel ratio */
  function sizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect  = canvas.getBoundingClientRect();
    // Preserve any existing drawing
    const prev = hasInk ? canvas.toDataURL() : null;
    canvas.width  = Math.round(rect.width  * ratio);
    canvas.height = Math.round(rect.height * ratio);
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.lineWidth = 2.4;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#15110f';
    if (prev) {
      const img = new Image();
      img.onload = () => ctx.drawImage(img, 0, 0, rect.width, rect.height);
      img.src = prev;
    }
  }
  // Defer until layout settles
  setTimeout(sizeCanvas, 50);
  window.addEventListener('resize', sizeCanvas);

  function pos(e) {
    const rect = canvas.getBoundingClientRect();
    const p = e.touches ? e.touches[0] : e;
    return { x: p.clientX - rect.left, y: p.clientY - rect.top };
  }
  function start(e) {
    e.preventDefault();
    drawing = true;
    const { x, y } = pos(e);
    lastX = x; lastY = y;
  }
  function move(e) {
    if (!drawing) return;
    e.preventDefault();
    const { x, y } = pos(e);
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(x, y);
    ctx.stroke();
    lastX = x; lastY = y;
    if (!hasInk) { hasInk = true; pad.classList.add('has-ink'); }
  }
  function end() { drawing = false; }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);

  clearBtn.addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasInk = false;
    pad.classList.remove('has-ink');
    pad.classList.remove('is-invalid');
  });

  const showAlert = (msg) => { alertBox.textContent = msg; alertBox.classList.add('show'); };
  const hideAlert = () => alertBox.classList.remove('show');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    let ok = true;
    if (nameEl.value.trim().length < 3) { nameEl.classList.add('is-invalid'); ok = false; }
    else nameEl.classList.remove('is-invalid');

    if (!hasInk) { pad.classList.add('is-invalid'); ok = false; }
    else pad.classList.remove('is-invalid');

    if (!agreeEl.checked) ok = false;

    if (!ok) {
      showAlert('Please type your full legal name, draw your signature, and check the agreement box.');
      return;
    }

    sigInput.value = canvas.toDataURL('image/png');

    submit.disabled = true;
    const original = submit.innerHTML;
    submit.innerHTML = 'Saving your signature…';

    try {
      const res = await fetch('{{ route('mentorship-agreement.sign') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: new FormData(form),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.success && data.redirect) {
        window.location.href = data.redirect;
        return;
      }
      showAlert(data.message || 'Something went wrong saving your signature. Please try again.');
      submit.disabled = false;
      submit.innerHTML = original;
    } catch (err) {
      showAlert('A network error stopped your submission. Please try again.');
      submit.disabled = false;
      submit.innerHTML = original;
    }
  });
})();
</script>

@endsection
