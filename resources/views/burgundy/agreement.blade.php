@extends('burgundy.layout')
@section('title', 'Your Service Agreement')
@section('description', 'Review and sign your credit restoration service agreement.')

@section('body')

<section class="hero">
  <div class="wrap">
    <span class="eyebrow">Step 2 of 3 · Agreement</span>
    <h1>Your payment went through. <em class="serif">One signature to go.</em></h1>
    <p>Please read your service agreement and sign below. A copy is kept on file with your record.</p>

    <div class="steps">
      <div class="step done"><span class="n">✓</span> Payment</div>
      <div class="step on"><span class="n">2</span> Agreement</div>
      <div class="step"><span class="n">3</span> Onboarding</div>
    </div>
  </div>
</section>

<main>
  <div class="wrap narrow">

    <div id="alertBox"></div>

    <div class="card">
      <h2>Credit Restoration Service Agreement</h2>
      <p class="sub">Please read in full before signing.</p>

      <div class="contract" id="contractBox">{{ $terms }}</div>
      <p class="scrollnote" id="scrollNote">Scroll to the bottom of the agreement to continue.</p>

      <form id="signForm" method="POST" action="{{ route('burgundy.agreement.sign') }}" novalidate>
        @csrf

        <div class="field">
          <label for="full_name">Type your full legal name <span class="req">*</span></label>
          <input type="text" id="full_name" name="full_name" value="{{ $fullName }}" required autocomplete="name">
          <div class="hint">This acts as your typed signature.</div>
          <div class="err-msg"></div>
        </div>

        <div class="field">
          <label>Draw your signature <span class="req">*</span></label>
          <div class="sigwrap">
            <canvas id="sigPad"></canvas>
            <div class="sigph" id="sigPlaceholder">Sign here with your mouse or finger</div>
          </div>
          <div class="sigbar">
            <span class="hint" style="margin:0">Use your mouse, trackpad or finger.</span>
            <button type="button" id="clearSig">Clear</button>
          </div>
          <div class="err-msg" id="sigErr"></div>
        </div>

        <div class="check" style="margin-top:18px">
          <input type="checkbox" id="agree_terms" name="agree_terms" value="1" required>
          <label for="agree_terms" style="margin:0;font-weight:500">
            I have read and agree to be legally bound by this Agreement, and I understand that my
            electronic signature is the legal equivalent of my handwritten signature. <span class="req">*</span>
          </label>
        </div>

        <button type="submit" id="signBtn" class="btn wide" style="margin-top:14px" disabled>
          Sign &amp; continue to onboarding
        </button>
      </form>
    </div>
  </div>
</main>

@endsection

@push('scripts')
<script>
(function () {
  var canvas = document.getElementById('sigPad');
  var ctx    = canvas.getContext('2d');
  var ph     = document.getElementById('sigPlaceholder');
  var form   = document.getElementById('signForm');
  var btn    = document.getElementById('signBtn');
  var box    = document.getElementById('alertBox');
  var drawn  = false;
  var drawing = false;

  // Match the backing store to the CSS size so the signature isn't blurry
  // on high-DPI screens, and redraw cleanly on rotate/resize.
  function sizeCanvas() {
    var ratio = window.devicePixelRatio || 1;
    var rect  = canvas.getBoundingClientRect();
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.lineWidth = 2.2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#1a1214';
  }
  sizeCanvas();
  window.addEventListener('resize', function () {
    var data = drawn ? canvas.toDataURL() : null;
    sizeCanvas();
    if (data) {
      var img = new Image();
      img.onload = function () { ctx.drawImage(img, 0, 0, canvas.getBoundingClientRect().width, canvas.getBoundingClientRect().height); };
      img.src = data;
    }
  });

  function pos(e) {
    var r = canvas.getBoundingClientRect();
    var p = e.touches ? e.touches[0] : e;
    return { x: p.clientX - r.left, y: p.clientY - r.top };
  }

  function start(e) {
    e.preventDefault();
    drawing = true;
    if (!drawn) { drawn = true; ph.classList.add('hide'); check(); }
    var p = pos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
  }
  function move(e) {
    if (!drawing) return;
    e.preventDefault();
    var p = pos(e);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
  }
  function end() { drawing = false; }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);

  document.getElementById('clearSig').addEventListener('click', function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawn = false;
    ph.classList.remove('hide');
    check();
  });

  // Gate the button until the agreement has actually been scrolled through.
  var contract = document.getElementById('contractBox');
  var note     = document.getElementById('scrollNote');
  var read     = false;

  function checkScroll() {
    if (contract.scrollTop + contract.clientHeight >= contract.scrollHeight - 24) {
      read = true;
      note.textContent = '✓ Agreement read in full.';
      note.style.color = '#15803d';
      check();
    }
  }
  contract.addEventListener('scroll', checkScroll);
  // Short agreements may not scroll at all.
  if (contract.scrollHeight <= contract.clientHeight + 24) { checkScroll(); }

  function check() {
    var nameOk  = document.getElementById('full_name').value.trim().length >= 3;
    var agreeOk = document.getElementById('agree_terms').checked;
    btn.disabled = !(read && drawn && nameOk && agreeOk);
  }

  document.getElementById('full_name').addEventListener('input', check);
  document.getElementById('agree_terms').addEventListener('change', check);

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!drawn) {
      document.getElementById('sigErr').textContent = 'Please draw your signature.';
      document.getElementById('sigErr').classList.add('on');
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Saving…';

    var data = new FormData(form);
    data.append('signature_data', canvas.toDataURL('image/png'));

    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: data
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.redirect) { window.location.href = res.redirect; return; }
      btn.disabled = false;
      btn.textContent = 'Sign & continue to onboarding';
      box.innerHTML = '<div class="alert err"><strong>Could not save your signature.</strong><p style="margin:4px 0 0">' +
        (res.message || 'Please try again.') + '</p></div>';
    })
    .catch(function () {
      btn.disabled = false;
      btn.textContent = 'Sign & continue to onboarding';
      box.innerHTML = '<div class="alert err"><strong>Connection problem.</strong>' +
        '<p style="margin:4px 0 0">Please check your connection and try again.</p></div>';
    });
  });
})();
</script>
@endpush
