@extends('burgundy.layout')
@section('title', 'Your Service Agreement — ' . $brand)
@section('description', 'Review and sign your service agreement.')

@section('body')

<section class="hero">
  <div class="wrap">
    <span class="eyebrow">Payment received</span>
    <h1>One signature and <em class="serif">you're done</em>.</h1>
    <p>Please read your service agreement and sign below. We keep a copy on file with your record.</p>
  </div>
</section>

<main>
  <div class="wrap narrow">

    <div id="alertBox"></div>

    {{-- The money, before the document. This is what the card was charged, read
         back from the sale — so it cannot disagree with their statement. --}}
    <div class="summary">
      <h2>{{ $agreement->plan_label }}</h2>
      <p class="tag">What you are paying</p>
      <div class="line">
        <span class="l">Charged today</span>
        <span class="r">${{ number_format((float) $agreement->deposit_amount, 2) }}</span>
      </div>
      @if ($agreement->installment_amount && (float) $agreement->installment_amount > 0)
        <div class="line">
          @php
            // Built in PHP rather than inline directives: Blade silently skips a
            // directive glued to a word character ("monthly@if"), which leaves the
            // matching @endif to close the wrong block.
            $forMonths = $agreement->installment_count
              ? ', for ' . $agreement->installment_count . ' month' . ($agreement->installment_count == 1 ? '' : 's')
              : '';
          @endphp
          <span class="l">Then monthly{{ $forMonths }}</span>
          <span class="r">${{ number_format((float) $agreement->installment_amount, 2) }}/mo</span>
        </div>
        @unless ($agreement->installment_count)
          <div class="line"><span class="l">Minimum term</span><span class="r">None — cancel any time</span></div>
        @endunless
      @else
        <div class="line"><span class="l">Ongoing</span><span class="r">Nothing further</span></div>
      @endif
      <p class="note">
        If any figure above does not match what you were charged, stop and
        <a href="mailto:support@victorialovecredit.com" style="color:#ff9dbd">tell us</a> before signing.
      </p>
    </div>

    <div class="card">
      <h2>Service Agreement</h2>
      <p class="sub">Please read in full before signing.</p>

      <div class="contract" id="contractBox">{{ $terms }}</div>
      <p class="scrollnote" id="scrollNote">Scroll to the bottom of the agreement to continue.</p>

      <form id="signForm" method="POST" action="{{ $postUrl }}" novalidate>
        @csrf

        <div class="field">
          <label for="full_name">Type your full legal name <span class="req">*</span></label>
          <input type="text" id="full_name" name="full_name" value="{{ $agreement->client_name }}" required autocomplete="name">
          <div class="hint">This acts as your typed signature.</div>
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
            I have read and agree to be bound by this Agreement, and I understand that my
            electronic signature is the legal equivalent of my handwritten signature. <span class="req">*</span>
          </label>
        </div>

        <button type="submit" id="signBtn" class="btn wide" style="margin-top:14px" disabled>
          Sign agreement
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
  var drawn = false, drawing = false;

  // Match the backing store to the CSS size so the signature is not blurry on
  // high-DPI screens, and survive a rotate.
  function sizeCanvas() {
    var ratio = window.devicePixelRatio || 1;
    var rect  = canvas.getBoundingClientRect();
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.lineWidth = 2.2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    ctx.strokeStyle = '#1a1214';
  }
  sizeCanvas();
  window.addEventListener('resize', function () {
    var data = drawn ? canvas.toDataURL() : null;
    sizeCanvas();
    if (data) {
      var img = new Image();
      img.onload = function () {
        var r = canvas.getBoundingClientRect();
        ctx.drawImage(img, 0, 0, r.width, r.height);
      };
      img.src = data;
    }
  });

  function pos(e) {
    var r = canvas.getBoundingClientRect();
    var p = e.touches ? e.touches[0] : e;
    return { x: p.clientX - r.left, y: p.clientY - r.top };
  }
  function start(e) {
    e.preventDefault(); drawing = true;
    if (!drawn) { drawn = true; ph.classList.add('hide'); check(); }
    var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y);
  }
  function move(e) { if (!drawing) return; e.preventDefault(); var p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }
  function end() { drawing = false; }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);

  document.getElementById('clearSig').addEventListener('click', function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawn = false; ph.classList.remove('hide'); check();
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
  if (contract.scrollHeight <= contract.clientHeight + 24) { checkScroll(); }

  function check() {
    var nameOk  = document.getElementById('full_name').value.trim().length >= 3;
    var agreeOk = document.getElementById('agree_terms').checked;
    btn.disabled = !(read && drawn && nameOk && agreeOk);
  }
  document.getElementById('full_name').addEventListener('input', check);
  document.getElementById('agree_terms').addEventListener('change', check);
  check();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!drawn) {
      document.getElementById('sigErr').textContent = 'Please draw your signature.';
      document.getElementById('sigErr').classList.add('on');
      return;
    }
    btn.disabled = true; btn.textContent = 'Saving…';

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
      btn.disabled = false; btn.textContent = 'Sign agreement';
      box.innerHTML = '<div class="alert err"><strong>Could not save your signature.</strong>' +
        '<p style="margin:4px 0 0">' + (res.message || 'Please try again.') + '</p></div>';
    })
    .catch(function () {
      btn.disabled = false; btn.textContent = 'Sign agreement';
      box.innerHTML = '<div class="alert err"><strong>Connection problem.</strong>' +
        '<p style="margin:4px 0 0">Please check your connection and try again.</p></div>';
    });
  });
})();
</script>
@endpush
