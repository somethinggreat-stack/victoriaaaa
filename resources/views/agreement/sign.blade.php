@extends('burgundy.layout')
@section('title', 'Your Service Agreement — ' . $brand)
@section('description', 'Review and sign your service agreement.')

@push('head')
<style>
  .signer{border:1px solid var(--line-2);border-radius:var(--r);padding:16px;margin-bottom:14px}
  .signer-done{background:var(--green-soft);border-color:rgba(21,128,61,.3)}
  .signer-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
  .signer-head strong{font-size:14.5px}
  .pill-done{background:var(--green);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:100px;white-space:nowrap}
  .signer-doneline{font-weight:700;font-size:15px}
  .signer-done .signer-head{margin-bottom:4px}
</style>
@endpush

@section('body')

<section class="hero">
  <div class="wrap">
    <span class="eyebrow">Payment received</span>
    <h1>
      @if ($agreement->requires_cosigner)
        Two signatures and <em class="serif">you're done</em>.
      @else
        One signature and <em class="serif">you're done</em>.
      @endif
    </h1>
    <p>Please read your service agreement and sign below. We keep a copy on file with your record.</p>
  </div>
</section>

<main>
  <div class="wrap narrow">

    <div id="alertBox"></div>

    {{-- The money, before the document. Read back from the sale, so it cannot
         disagree with what their statement says. --}}
    <div class="summary">
      <h2>{{ $agreement->plan_label }}</h2>
      <p class="tag">What you are paying</p>

      @php
        $every = $agreement->recurring_interval === 'week' ? 'week' : 'month';
        $hasRecurring = $agreement->installment_amount && (float) $agreement->installment_amount > 0;
        $howOften = 'Then every ' . $every;
        if ($agreement->installment_count) {
            $howOften .= ', ' . $agreement->installment_count . ' time'
                       . ($agreement->installment_count == 1 ? '' : 's');
        }
      @endphp

      <div class="line">
        <span class="l">Charged today</span>
        <span class="r">${{ number_format((float) $agreement->deposit_amount, 2) }}</span>
      </div>

      @if ($hasRecurring)
        <div class="line">
          <span class="l">{{ $howOften }}</span>
          <span class="r">${{ number_format((float) $agreement->installment_amount, 2) }}/{{ $every === 'week' ? 'wk' : 'mo' }}</span>
        </div>
        @if ($agreement->installment_count)
          <div class="line total">
            <span class="l">Total</span>
            <span class="r">${{ number_format((float) $agreement->deposit_amount + ((float) $agreement->installment_amount * $agreement->installment_count), 2) }}</span>
          </div>
        @else
          <div class="line"><span class="l">Minimum term</span><span class="r">None — cancel any time</span></div>
        @endif
      @else
        <div class="line"><span class="l">Ongoing</span><span class="r">Nothing further</span></div>
      @endif

      <p class="note">
        @if ($agreement->requires_cosigner)
          This is the total for both people named below, not per person.
        @endif
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

        @if ($agreement->requires_cosigner)
          <div class="alert warn" style="margin-bottom:18px">
            <strong>This agreement covers two people.</strong>
            Both must sign. You can both sign here now, or one of you can sign and send
            the same link to the other.
          </div>
        @endif

        @include('agreement.partials.signature-block', [
          'key'   => 'primary',
          'label' => $agreement->requires_cosigner ? ($agreement->client_name ?: 'First client') : 'Your signature',
          'name'  => $agreement->full_name ?: $agreement->client_name,
          'done'  => $agreement->primarySigned(),
          'when'  => $agreement->signed_at,
        ])

        @if ($agreement->requires_cosigner)
          @include('agreement.partials.signature-block', [
            'key'   => 'cosigner',
            'label' => $agreement->cosigner_name ?: 'Second client',
            'name'  => $agreement->cosigner_full_name ?: $agreement->cosigner_name,
            'done'  => $agreement->cosignerSigned(),
            'when'  => $agreement->cosigner_signed_at,
          ])
        @endif

        <div class="err-msg" id="sigErr"></div>

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
  var form = document.getElementById('signForm');
  var btn  = document.getElementById('signBtn');
  var box  = document.getElementById('alertBox');

  // One controller per signature pad, so a joint agreement can carry two.
  var pads = Array.prototype.map.call(document.querySelectorAll('.js-sigpad'), function (canvas) {
    var ctx   = canvas.getContext('2d');
    var block = canvas.closest('.signer');
    var ph    = block.querySelector('.sigph');
    var out   = block.querySelector('.js-sigdata');
    var drawn = false, drawing = false;

    // Match the backing store to the CSS size so signatures are not blurry on
    // high-DPI screens, and survive a rotate.
    function size() {
      var ratio = window.devicePixelRatio || 1;
      var rect  = canvas.getBoundingClientRect();
      var data  = drawn ? canvas.toDataURL() : null;
      canvas.width  = rect.width  * ratio;
      canvas.height = rect.height * ratio;
      ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
      ctx.lineWidth = 2.2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
      ctx.strokeStyle = '#1a1214';
      if (data) {
        var img = new Image();
        img.onload = function () { ctx.drawImage(img, 0, 0, rect.width, rect.height); };
        img.src = data;
      }
    }
    size();
    window.addEventListener('resize', size);

    function pos(e) {
      var r = canvas.getBoundingClientRect();
      var p = e.touches ? e.touches[0] : e;
      return { x: p.clientX - r.left, y: p.clientY - r.top };
    }
    function start(e) {
      e.preventDefault(); drawing = true;
      if (!drawn) { drawn = true; ph.classList.add('hide'); }
      var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y);
      check();
    }
    function move(e) { if (!drawing) return; e.preventDefault(); var p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }
    function end() { drawing = false; check(); }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);
    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    canvas.addEventListener('touchend', end);

    block.querySelector('.js-clearsig').addEventListener('click', function () {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      drawn = false; ph.classList.remove('hide'); out.value = ''; check();
    });

    var nameInput = block.querySelector('.js-signer-name');

    return {
      nameInput: nameInput,
      commit: function () { if (drawn) { out.value = canvas.toDataURL('image/png'); } },
      complete: function () {
        return drawn && nameInput && nameInput.value.trim().length >= 3;
      }
    };
  });

  // Gate on actually reading the agreement.
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

  function anyComplete() {
    return pads.some(function (p) { return p.complete(); });
  }

  function check() {
    var agreeOk = document.getElementById('agree_terms').checked;
    // At least one outstanding signer must be complete. The other can sign
    // later from the same link, so a couple who are not together is fine.
    btn.disabled = !(read && agreeOk && anyComplete());
  }

  pads.forEach(function (p) {
    if (p.nameInput) { p.nameInput.addEventListener('input', check); }
  });
  document.getElementById('agree_terms').addEventListener('change', check);
  check();

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!anyComplete()) {
      var err = document.getElementById('sigErr');
      err.textContent = 'Please type a name and draw a signature.';
      err.classList.add('on');
      return;
    }

    pads.forEach(function (p) { p.commit(); });

    btn.disabled = true; btn.textContent = 'Saving…';

    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: new FormData(form)
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
