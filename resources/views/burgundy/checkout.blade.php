@extends('burgundy.layout')
@section('title', 'Secure Enrollment — $' . number_format((float) $enrollment, 0) . ' Today')
@section('description', 'Secure enrollment for your credit restoration program.')

@section('body')

<section class="hero">
  <div class="wrap">
    <span class="eyebrow">Step 1 of 3 · Enrollment</span>
    <h1>Let's get your credit <em class="serif">moving again</em>.</h1>
    <p>${{ number_format((float) $enrollment, 0) }} today to enroll, then ${{ number_format((float) $monthly, 0) }} per month. No minimum term — cancel any time.</p>

    <div class="steps">
      <div class="step on"><span class="n">1</span> Payment</div>
      <div class="step"><span class="n">2</span> Agreement</div>
      <div class="step"><span class="n">3</span> Onboarding</div>
    </div>
  </div>
</section>

<main>
  <div class="wrap">

    <div class="summary">
      <h2>{{ $planLabel }}</h2>
      <p class="tag">Full-service credit restoration</p>
      <div class="line"><span class="l">Enrollment today</span><span class="r">${{ number_format((float) $enrollment, 2) }}</span></div>
      <div class="line"><span class="l">Then monthly, starting in 30 days</span><span class="r">${{ number_format((float) $monthly, 2) }}/mo</span></div>
      <div class="line total"><span class="l">Due today</span><span class="r">${{ number_format((float) $enrollment, 2) }}</span></div>
      <p class="note">
        Your card is charged ${{ number_format((float) $enrollment, 2) }} now, then ${{ number_format((float) $monthly, 2) }} every month until you cancel.
        There is no contract length and no cancellation fee. You'll sign your service agreement on the next step.
      </p>
    </div>

    <div id="alertBox"></div>

    <form id="payForm" class="card" method="POST" action="{{ $postUrl }}" autocomplete="on" novalidate>
      @csrf

      <h3>Your details</h3>
      <div class="grid">
        <div class="field col-6">
          <label for="first_name">First name <span class="req">*</span></label>
          <input type="text" id="first_name" name="first_name" required autocomplete="given-name">
          <div class="err-msg"></div>
        </div>
        <div class="field col-6">
          <label for="last_name">Last name <span class="req">*</span></label>
          <input type="text" id="last_name" name="last_name" required autocomplete="family-name">
          <div class="err-msg"></div>
        </div>
        <div class="field col-6">
          <label for="email">Email <span class="req">*</span></label>
          <input type="email" id="email" name="email" required autocomplete="email">
          <div class="hint">Use the email Burgundy has on file, if you can.</div>
          <div class="err-msg"></div>
        </div>
        <div class="field col-6">
          <label for="phone">Phone <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone" required autocomplete="tel">
          <div class="err-msg"></div>
        </div>
      </div>

      <h3>Billing address</h3>
      <div class="grid">
        <div class="field full">
          <label for="address">Street address <span class="req">*</span></label>
          <input type="text" id="address" name="address" required autocomplete="street-address">
          <div class="err-msg"></div>
        </div>
        <div class="field col-6">
          <label for="city">City <span class="req">*</span></label>
          <input type="text" id="city" name="city" required autocomplete="address-level2">
          <div class="err-msg"></div>
        </div>
        <div class="field col-3 m-6">
          <label for="state">State <span class="req">*</span></label>
          <input type="text" id="state" name="state" required maxlength="2" placeholder="TX" autocomplete="address-level1" style="text-transform:uppercase">
          <div class="err-msg"></div>
        </div>
        <div class="field col-3 m-6">
          <label for="zip">Zip <span class="req">*</span></label>
          <input type="text" id="zip" name="zip" required autocomplete="postal-code">
          <div class="err-msg"></div>
        </div>
      </div>

      <h3>Card details</h3>
      <div class="grid">
        <div class="field full">
          <label for="cardName">Name on card <span class="req">*</span></label>
          <input type="text" id="cardName" name="cardName" required autocomplete="cc-name">
          <div class="err-msg"></div>
        </div>
        <div class="field full">
          <label for="cardNumber">Card number <span class="req">*</span></label>
          <input type="text" id="cardNumber" name="cardNumber" required inputmode="numeric" placeholder="1234 5678 9012 3456" autocomplete="cc-number" maxlength="23">
          <div class="err-msg"></div>
        </div>
        <div class="field col-4 m-4">
          <label for="expMonth">Exp. month <span class="req">*</span></label>
          <select id="expMonth" name="expMonth" required autocomplete="cc-exp-month">
            <option value="">MM</option>
            @for($i = 1; $i <= 12; $i++)
              <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
            @endfor
          </select>
          <div class="err-msg"></div>
        </div>
        <div class="field col-4 m-4">
          <label for="expYear">Exp. year <span class="req">*</span></label>
          <select id="expYear" name="expYear" required autocomplete="cc-exp-year">
            <option value="">YYYY</option>
            @for($y = (int) date('Y'); $y <= (int) date('Y') + 14; $y++)
              <option value="{{ $y }}">{{ $y }}</option>
            @endfor
          </select>
          <div class="err-msg"></div>
        </div>
        <div class="field col-4 m-4">
          <label for="cardCode">CVV <span class="req">*</span></label>
          <input type="text" id="cardCode" name="cardCode" required inputmode="numeric" maxlength="4" placeholder="123" autocomplete="cc-csc">
          <div class="err-msg"></div>
        </div>
      </div>

      <h3>Agreements</h3>
      <div class="check">
        <input type="checkbox" id="agree_terms" name="agree_terms" value="1" required>
        <label for="agree_terms" style="margin:0;font-weight:500">
          I agree to the <a href="{{ route('legal.terms-of-service') }}" target="_blank" rel="noopener">Terms of Service</a>,
          and I authorize a charge of ${{ number_format((float) $enrollment, 2) }} today followed by
          ${{ number_format((float) $monthly, 2) }} monthly until I cancel. <span class="req">*</span>
        </label>
      </div>
      <div class="check">
        <input type="checkbox" id="agree_privacy" name="agree_privacy" value="1" required>
        <label for="agree_privacy" style="margin:0;font-weight:500">
          I have read the <a href="{{ route('legal.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a>. <span class="req">*</span>
        </label>
      </div>

      <button type="submit" id="payBtn" class="btn wide" style="margin-top:16px">
        Pay ${{ number_format((float) $enrollment, 2) }} &amp; continue
      </button>

      <p class="hint" style="text-align:center;margin-top:13px">
        🔒 Processed securely by Authorize.Net. Your card details are never stored on our servers.
      </p>
    </form>
  </div>
</main>

@endsection

@push('scripts')
<script>
(function () {
  var form = document.getElementById('payForm');
  var btn  = document.getElementById('payBtn');
  var box  = document.getElementById('alertBox');

  // Card number: group in fours as they type.
  var cn = document.getElementById('cardNumber');
  cn.addEventListener('input', function () {
    var v = cn.value.replace(/\D/g, '').slice(0, 19);
    cn.value = v.replace(/(.{4})/g, '$1 ').trim();
  });

  document.getElementById('cardCode').addEventListener('input', function (e) {
    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
  });

  function alertBox(kind, title, body) {
    box.innerHTML = '<div class="alert ' + kind + '"><strong>' + title + '</strong>' + (body || '') + '</div>';
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function clearErrors() {
    form.querySelectorAll('.bad').forEach(function (el) { el.classList.remove('bad'); });
    form.querySelectorAll('.err-msg.on').forEach(function (el) { el.classList.remove('on'); el.textContent = ''; });
  }

  function fieldError(name, message) {
    var el = form.querySelector('[name="' + name + '"]');
    if (!el) return;
    el.classList.add('bad');
    var msg = el.parentElement.querySelector('.err-msg');
    if (msg) { msg.textContent = message; msg.classList.add('on'); }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();
    box.innerHTML = '';

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Processing…';

    var data = new FormData(form);
    data.set('cardNumber', data.get('cardNumber').replace(/\s/g, ''));
    data.set('state', (data.get('state') || '').toUpperCase());

    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: data
    })
    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, body: j }; }); })
    .then(function (res) {
      if (res.body && res.body.success && res.body.redirect) {
        btn.textContent = 'Approved — continuing…';
        window.location.href = res.body.redirect;
        return;
      }

      btn.disabled = false;
      btn.textContent = 'Pay ${{ number_format((float) $enrollment, 2) }} & continue';

      if (res.status === 422 && res.body && res.body.errors) {
        Object.keys(res.body.errors).forEach(function (k) {
          fieldError(k, [].concat(res.body.errors[k])[0]);
        });
        alertBox('err', 'Please check the highlighted fields.', '');
        return;
      }

      alertBox('err', 'We could not process that payment.',
        '<p style="margin:4px 0 0">' + ((res.body && res.body.message) || 'Please check your card details and try again.') + '</p>');
    })
    .catch(function () {
      btn.disabled = false;
      btn.textContent = 'Pay ${{ number_format((float) $enrollment, 2) }} & continue';
      alertBox('err', 'Connection problem.',
        '<p style="margin:4px 0 0">Your card was not charged. Please check your connection and try again.</p>');
    });
  });
})();
</script>
@endpush
