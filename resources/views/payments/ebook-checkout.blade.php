@extends('layouts.app')

@section('title', 'Secure Checkout — ' . $ebook->title . ' | Victoria Love')
@section('description', 'Secure 256-bit encrypted checkout for ' . $ebook->title . '. Instant download after payment.')
@section('bodyClass', 'page-checkout page-ebook-checkout')

@section('content')

<style>
/* ============ EBOOK CHECKOUT — VICTORIA LOVE BRAND ============ */
.checkout-hero {
  padding: 130px 0 30px;
  position: relative;
}
.checkout-hero .container { max-width: 1180px; }
.checkout-hero-head { text-align: center; margin-bottom: 40px; }
.checkout-hero-head .eyebrow { margin-bottom: 16px; }
.checkout-hero-head h1 {
  font-size: clamp(2rem, 3.6vw, 2.8rem);
  margin-bottom: 12px;
}
.checkout-hero-head p { font-size: 16px; max-width: 580px; margin: 0 auto; }

.checkout-trust-row {
  display: flex; justify-content: center; gap: 28px; flex-wrap: wrap;
  margin-top: 24px;
  font-size: 12.5px; color: var(--ink-3);
  text-transform: uppercase; letter-spacing: 0.14em; font-weight: 600;
}
.checkout-trust-row span { display: inline-flex; align-items: center; gap: 8px; }
.checkout-trust-row svg { width: 14px; height: 14px; color: var(--pink); }

.checkout-section { padding: 30px 0 100px; }
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 40px;
  align-items: start;
}

.checkout-form-wrap {
  background: var(--bg-3);
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 36px;
  box-shadow: 0 30px 60px -30px rgba(20,16,14,0.08);
}
.checkout-form-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 28px; padding-bottom: 22px;
  border-bottom: 1px dashed var(--line-2);
}
.checkout-form-head h2 { font-size: 22px; margin: 0; }
.checkout-form-head .secure-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 600;
  letter-spacing: 0.12em; text-transform: uppercase;
  color: #0f8a4a;
  background: rgba(15,138,74,0.08);
  padding: 6px 12px; border-radius: 100px;
}
.checkout-form-head .secure-badge svg { width: 12px; height: 12px; }

.co-section { margin-bottom: 28px; }
.co-section + .co-section { padding-top: 4px; }
.co-section-ttl {
  display: flex; align-items: center; gap: 10px;
  font-size: 12px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--ink-3); margin-bottom: 14px;
}
.co-section-ttl::before {
  content: ''; width: 18px; height: 1px; background: var(--pink);
}

.co-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.co-grid-card { display: grid; grid-template-columns: 1fr 110px 110px; gap: 14px; }
.co-full { grid-column: 1 / -1; }

.co-field { display: flex; flex-direction: column; gap: 6px; }
.co-field > span {
  font-size: 12.5px; font-weight: 600; color: var(--ink);
}
.co-field > span em { color: var(--pink); font-style: normal; }
.co-input, .co-select {
  width: 100%;
  padding: 13px 14px;
  border: 1.5px solid var(--line-2);
  border-radius: var(--r-sm);
  background: #fff;
  font-family: inherit; font-size: 15px;
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s, background .2s;
  outline: none;
}
.co-input:focus, .co-select:focus {
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.10);
}
.co-input.is-invalid, .co-select.is-invalid {
  border-color: #d93838;
  box-shadow: 0 0 0 4px rgba(217,56,56,0.10);
}
.co-help { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

.co-card-wrap { position: relative; }
.co-card-brand {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.08em;
  color: var(--ink-3);
  background: var(--bg-2);
  padding: 4px 10px; border-radius: 6px;
  pointer-events: none;
  transition: color .2s, background .2s;
}
.co-card-brand.is-visa  { color: #1a1f71; background: #eef0f8; }
.co-card-brand.is-mc    { color: #b8201c; background: #fce8e6; }
.co-card-brand.is-amex  { color: #2671b9; background: #e6f0fa; }
.co-card-brand.is-disc  { color: #ff6000; background: #fff1e6; }

.co-agree {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px;
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: var(--r-sm);
  margin-bottom: 10px;
  font-size: 13.5px; line-height: 1.55;
  color: var(--ink-2);
  cursor: pointer;
}
.co-agree input[type="checkbox"] {
  margin-top: 3px;
  width: 18px; height: 18px; flex: 0 0 18px;
  accent-color: var(--pink);
  cursor: pointer;
}
.co-agree a { color: var(--pink); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }

.co-pay-wrap { margin-top: 28px; }
.co-pay-btn {
  width: 100%;
  background: var(--pink); color: #fff;
  padding: 18px 24px;
  border-radius: 100px;
  font-size: 16px; font-weight: 700;
  letter-spacing: 0.01em;
  display: inline-flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 16px 36px -12px rgba(230,49,121,0.55);
  transition: transform .25s, background .25s, box-shadow .25s;
  position: relative; overflow: hidden;
  border: 0; cursor: pointer; font-family: inherit;
}
.co-pay-btn:hover:not(:disabled) {
  background: var(--ink); transform: translateY(-2px);
  box-shadow: 0 22px 44px -12px rgba(20,16,14,0.5);
}
.co-pay-btn:disabled { opacity: 0.55; cursor: not-allowed; }
.co-pay-btn .spinner {
  display: none;
  width: 18px; height: 18px;
  border: 2.5px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: cospin .7s linear infinite;
}
.co-pay-btn.is-loading .label { opacity: 0; }
.co-pay-btn.is-loading .spinner { display: inline-block; position: absolute; }
@keyframes cospin { to { transform: rotate(360deg); } }
.co-pay-fine {
  font-size: 12px; color: var(--ink-3);
  text-align: center; margin-top: 14px; line-height: 1.5;
}
.co-pay-fine svg { width: 11px; height: 11px; margin-right: 4px; vertical-align: -1px; color: #0f8a4a; }

.co-alert {
  display: none;
  background: #fce8e8;
  border: 1px solid #f1b5b5;
  color: #8a1f1f;
  padding: 14px 16px;
  border-radius: var(--r-sm);
  margin-bottom: 20px;
  font-size: 14px; line-height: 1.5;
}
.co-alert.is-show { display: block; }
.co-alert strong { color: #5d1010; }

/* ── EBOOK SUMMARY CARD (right column) ─────────────────────────── */
.eb-summary {
  position: sticky; top: 110px;
  background: linear-gradient(180deg, #fff 0%, #fff8fb 100%);
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 28px 26px;
  box-shadow: 0 30px 60px -30px rgba(230,49,121,0.20);
  text-align: center;
}
.eb-summary .eyebrow {
  display: inline-block;
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 5px 14px; border-radius: 100px;
  margin-bottom: 18px;
}
.eb-summary .cover-wrap {
  position: relative;
  display: inline-block;
  margin-bottom: 18px;
  transform: rotate(-2deg);
  transition: transform .35s cubic-bezier(.2,.7,.2,1);
}
.eb-summary .cover-wrap:hover { transform: rotate(0deg) scale(1.03); }
.eb-summary .cover-wrap img {
  width: 200px;
  display: block;
  border-radius: 6px;
  box-shadow:
    -2px 0 0 rgba(20,16,14,0.06),
    -4px 0 0 rgba(20,16,14,0.04),
    0 30px 60px -20px rgba(20,16,14,0.4),
    0 0 0 1px rgba(20,16,14,0.06);
}
.eb-summary .cover-wrap::after {
  content: ""; position: absolute;
  top: 0; bottom: 0; left: 2px; width: 4px;
  background: linear-gradient(90deg, rgba(0,0,0,0.18), transparent);
  border-radius: 6px 0 0 6px;
}
.eb-summary .ebook-ttl {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 24px; line-height: 1.15;
  letter-spacing: -0.01em; color: var(--ink);
  margin: 0 0 8px;
}
.eb-summary .ebook-sub {
  font-size: 13px; color: var(--ink-2);
  line-height: 1.5; margin: 0 auto 18px; max-width: 280px;
}
.eb-summary .price-pill {
  display: inline-flex; align-items: baseline; gap: 2px;
  padding: 12px 22px;
  background: linear-gradient(135deg, var(--ink) 0%, #2c1f1a 100%);
  color: #fff;
  border-radius: 100px;
  margin-bottom: 18px;
  box-shadow: 0 14px 30px -12px rgba(20,16,14,0.45);
}
.eb-summary .price-pill .dollar { font-size: 14px; font-weight: 700; opacity: 0.7; margin-right: 1px; }
.eb-summary .price-pill .whole  { font-size: 28px; font-weight: 700; letter-spacing: -0.02em; }
.eb-summary .price-pill .cents  { font-size: 14px; font-weight: 700; opacity: 0.7; }
.eb-summary .feat {
  list-style: none; padding: 0; margin: 8px 0 16px;
  text-align: left;
}
.eb-summary .feat li {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 8px 0;
  font-size: 13.5px; line-height: 1.5;
  color: var(--ink-2);
  border-bottom: 1px dashed var(--line);
}
.eb-summary .feat li:last-child { border-bottom: 0; }
.eb-summary .feat li::before {
  content: "✓";
  flex-shrink: 0;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: var(--pink-soft);
  color: var(--pink);
  font-size: 11px; font-weight: 700;
  display: inline-grid; place-items: center;
  margin-top: 2px;
}
.eb-summary .delivery-note {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 14px;
  background: rgba(34,197,94,0.08);
  border: 1px solid rgba(34,197,94,0.18);
  border-radius: var(--r-sm);
  font-size: 12.5px; color: #157a3d;
  text-align: left;
  margin-top: 8px;
}
.eb-summary .delivery-note .ic {
  width: 28px; height: 28px; flex-shrink: 0;
  border-radius: 50%;
  background: #22c55e; color: #fff;
  font-size: 14px;
  display: grid; place-items: center;
}
.eb-summary .delivery-note strong { display: block; color: #0d5a2a; font-weight: 700; margin-bottom: 2px; font-size: 12px; }

@media (max-width: 960px) {
  .checkout-grid { grid-template-columns: 1fr; gap: 24px; }
  .eb-summary { position: static; order: -1; }
  .checkout-form-wrap { padding: 26px 22px; }
  .co-grid, .co-grid-card { grid-template-columns: 1fr; }
}
@media (max-width: 540px) {
  .checkout-hero { padding-top: 110px; }
  .checkout-form-head { flex-direction: column; align-items: flex-start; gap: 10px; }
  .eb-summary .cover-wrap img { width: 160px; }
}
</style>

<!-- ============ EBOOK CHECKOUT HERO ============ -->
<section class="checkout-hero">
  <div class="container">
    <div class="checkout-hero-head reveal">
      <span class="eyebrow">Secure checkout · Instant digital delivery</span>
      <h1>One click from <em class="serif gradient-text">unlocking</em> your ebook.</h1>
      <p>Your card is encrypted on this page by Authorize.Net. The moment your payment clears, you'll be redirected to your private download link.</p>
      <div class="checkout-trust-row">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>256-BIT SSL</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z"/></svg>PCI COMPLIANT</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>AUTHORIZE.NET</span>
      </div>
    </div>
  </div>
</section>

@php
  $priceParts = explode('.', number_format((float) $ebook->price, 2, '.', ''));
@endphp

<!-- ============ CHECKOUT GRID ============ -->
<section class="checkout-section">
  <div class="container">
    <div class="checkout-grid">

      <!-- ── FORM COLUMN ─────────────────────────────────── -->
      <div class="checkout-form-wrap reveal">

        <div class="checkout-form-head">
          <h2>Complete your order</h2>
          <span class="secure-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Secured
          </span>
        </div>

        <div class="co-alert" id="coAlert" role="alert">
          <strong>Heads up:</strong>
          <div id="coAlertMsg"></div>
        </div>

        <form id="ebookCheckoutForm" novalidate autocomplete="on">
          @csrf
          <input type="hidden" name="ebook_slug"     value="{{ $ebook->slug }}" />
          <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
          <input type="hidden" name="dataValue"      id="dataValue" />

          <!-- ── 01 · Your details ───────────────────────── -->
          <div class="co-section">
            <div class="co-section-ttl">01 · Your details</div>
            <div class="co-grid">
              <label class="co-field">
                <span>First name <em>*</em></span>
                <input class="co-input" type="text" name="first_name" required maxlength="100" autocomplete="given-name" />
              </label>
              <label class="co-field">
                <span>Last name <em>*</em></span>
                <input class="co-input" type="text" name="last_name" required maxlength="100" autocomplete="family-name" />
              </label>
              <label class="co-field">
                <span>Email <em>*</em></span>
                <input class="co-input" type="email" name="email" required maxlength="150" autocomplete="email" />
                <span class="co-help">We'll email a receipt and a backup download link here.</span>
              </label>
              <label class="co-field">
                <span>Phone <em>*</em></span>
                <input class="co-input" type="tel" name="phone" id="coPhone" required maxlength="20" placeholder="(555) 123-4567" autocomplete="tel" />
              </label>
            </div>
          </div>

          <!-- ── 02 · Billing address ──────────────────── -->
          <div class="co-section">
            <div class="co-section-ttl">02 · Billing address</div>
            <div class="co-grid">
              <label class="co-field co-full">
                <span>Street address <em>*</em></span>
                <input class="co-input" type="text" name="address" required maxlength="255" autocomplete="street-address" />
              </label>
              <label class="co-field">
                <span>City <em>*</em></span>
                <input class="co-input" type="text" name="city" required maxlength="100" autocomplete="address-level2" />
              </label>
              <label class="co-field">
                <span>State <em>*</em></span>
                <select class="co-select" name="state" required autocomplete="address-level1">
                  <option value="">Select…</option>
                  @php
                    $states = ['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
                  @endphp
                  @foreach($states as $st)<option value="{{ $st }}">{{ $st }}</option>@endforeach
                </select>
              </label>
              <label class="co-field">
                <span>ZIP <em>*</em></span>
                <input class="co-input" type="text" name="zip" required maxlength="10" inputmode="numeric" autocomplete="postal-code" />
              </label>
            </div>
          </div>

          <!-- ── 03 · Card details ─────────────────────── -->
          <div class="co-section">
            <div class="co-section-ttl">03 · Card details</div>
            <div class="co-grid">
              <label class="co-field co-full">
                <span>Name on card <em>*</em></span>
                <input class="co-input" type="text" name="cardName" required maxlength="150" autocomplete="cc-name" />
              </label>
            </div>
            <div class="co-grid-card" style="margin-top:14px">
              <label class="co-field co-card-wrap">
                <span>Card number <em>*</em></span>
                <input class="co-input" type="text" id="cardNumber" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" maxlength="23" />
                <span class="co-card-brand" id="cardBrand">CARD</span>
              </label>
              <label class="co-field">
                <span>Expiry <em>*</em></span>
                <input class="co-input" type="text" id="cardExp" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/YY" maxlength="7" />
              </label>
              <label class="co-field">
                <span>CVC <em>*</em></span>
                <input class="co-input" type="text" id="cardCvc" inputmode="numeric" autocomplete="cc-csc" placeholder="123" maxlength="4" />
              </label>
            </div>
            <p class="co-help" style="margin-top:8px">Your card is encrypted on this page by Authorize.Net before it reaches us.</p>
          </div>

          <!-- ── 04 · Agreements ───────────────────────── -->
          <div class="co-section">
            <div class="co-section-ttl">04 · Agreements</div>

            <label class="co-agree">
              <input type="checkbox" name="agree_terms" value="1" required />
              <span>I have read and agree to the <a href="#" target="_blank">Terms of Service</a> and authorize Victorious Opportunities to charge my card for this digital download.</span>
            </label>

            <label class="co-agree">
              <input type="checkbox" name="agree_privacy" value="1" required />
              <span>I have read the <a href="#" target="_blank">Privacy Policy</a> and consent to my data being used to deliver this product.</span>
            </label>

            <label class="co-agree">
              <input type="checkbox" name="marketing_opt_in" value="1" />
              <span>Send me occasional credit, funding &amp; homeownership tips via email and SMS (optional — unsubscribe anytime).</span>
            </label>
          </div>

          <!-- ── Pay button ─────────────────────────────── -->
          <div class="co-pay-wrap">
            <button type="submit" class="co-pay-btn" id="coPayBtn">
              <span class="label">Pay ${{ rtrim(rtrim(number_format((float) $ebook->price, 2, '.', ''), '0'), '.') }} &amp; get instant access</span>
              <span class="spinner" aria-hidden="true"></span>
            </button>
            <p class="co-pay-fine">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
              Instant download — you'll be redirected to your private link the moment your payment clears.
            </p>
          </div>
        </form>
      </div>

      <!-- ── EBOOK SUMMARY ────────────────────────────────── -->
      <aside class="eb-summary reveal reveal-d2">
        <span class="eyebrow">Your ebook</span>

        @if($ebook->cover_image)
          <div class="cover-wrap">
            <img src="{{ asset($ebook->cover_image) }}" alt="{{ $ebook->title }}" loading="lazy" />
          </div>
        @endif

        <h3 class="ebook-ttl">{{ $ebook->title }}</h3>
        @if($ebook->subtitle)
          <p class="ebook-sub">{{ $ebook->subtitle }}</p>
        @endif

        <div class="price-pill">
          <span class="dollar">$</span>
          <span class="whole">{{ $priceParts[0] }}</span>
          <span class="cents">.{{ $priceParts[1] }}</span>
        </div>

        @if(!empty($ebook->features))
          <ul class="feat">
            @foreach($ebook->features as $f)
              <li>{{ $f }}</li>
            @endforeach
          </ul>
        @endif

        <div class="delivery-note">
          <span class="ic">↓</span>
          <span><strong>Instant digital delivery</strong>Download starts immediately after payment.</span>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============ ACCEPT.JS ============ -->
@php
  $acceptJsUrl = $environment === 'sandbox'
    ? 'https://jstest.authorize.net/v1/Accept.js'
    : 'https://js.authorize.net/v1/Accept.js';
@endphp
<script src="{{ $acceptJsUrl }}" charset="utf-8"></script>

<script>
(function () {
  const AUTH = {
    apiLoginID: {!! json_encode($apiLogin ?? '') !!},
    clientKey:  {!! json_encode($clientKey ?? '') !!}
  };

  const form         = document.getElementById('ebookCheckoutForm');
  const payBtn       = document.getElementById('coPayBtn');
  const alertBox     = document.getElementById('coAlert');
  const alertMsg     = document.getElementById('coAlertMsg');

  const cardNumberEl = document.getElementById('cardNumber');
  const cardExpEl    = document.getElementById('cardExp');
  const cardCvcEl    = document.getElementById('cardCvc');
  const cardBrandEl  = document.getElementById('cardBrand');
  const phoneEl      = document.getElementById('coPhone');

  /* ─── Phone mask ─── */
  if (phoneEl) {
    phoneEl.addEventListener('input', () => {
      let d = phoneEl.value.replace(/\D+/g, '');
      if (d.length > 10 && d.startsWith('1')) d = d.slice(1);
      d = d.slice(0, 10);
      let out = '';
      if (d.length === 0) out = '';
      else if (d.length < 4) out = '(' + d;
      else if (d.length < 7) out = '(' + d.slice(0,3) + ') ' + d.slice(3);
      else out = '(' + d.slice(0,3) + ') ' + d.slice(3,6) + '-' + d.slice(6);
      phoneEl.value = out;
    });
  }

  /* ─── Card masks + brand sniff ─── */
  const detectBrand = (raw) => {
    const n = raw.replace(/\D+/g, '');
    if (/^4/.test(n))                  return { brand: 'visa', label: 'VISA' };
    if (/^(5[1-5]|2[2-7])/.test(n))    return { brand: 'mc',   label: 'MC' };
    if (/^3[47]/.test(n))              return { brand: 'amex', label: 'AMEX' };
    if (/^(6011|65|64[4-9])/.test(n))  return { brand: 'disc', label: 'DISCOVER' };
    return { brand: '', label: 'CARD' };
  };
  cardNumberEl.addEventListener('input', () => {
    let d = cardNumberEl.value.replace(/\D+/g, '').slice(0, 19);
    const groups = d.match(/.{1,4}/g) || [];
    cardNumberEl.value = groups.join(' ');
    const { brand, label } = detectBrand(d);
    cardBrandEl.textContent = label;
    cardBrandEl.className = 'co-card-brand' + (brand ? ' is-' + brand : '');
  });
  cardExpEl.addEventListener('input', () => {
    let d = cardExpEl.value.replace(/\D+/g, '').slice(0, 4);
    cardExpEl.value = d.length >= 3 ? d.slice(0, 2) + '/' + d.slice(2) : d;
  });
  cardCvcEl.addEventListener('input', () => {
    cardCvcEl.value = cardCvcEl.value.replace(/\D+/g, '').slice(0, 4);
  });

  /* ─── Helpers ─── */
  const setInvalid = (el, on) => el.classList.toggle('is-invalid', !!on);
  const showAlert = (text) => {
    alertMsg.textContent = text;
    alertBox.classList.add('is-show');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };
  const hideAlert = () => alertBox.classList.remove('is-show');

  const luhn = (num) => {
    const d = num.replace(/\D+/g, '');
    if (d.length < 12) return false;
    let sum = 0, alt = false;
    for (let i = d.length - 1; i >= 0; i--) {
      let n = parseInt(d[i], 10);
      if (alt) { n *= 2; if (n > 9) n -= 9; }
      sum += n; alt = !alt;
    }
    return sum % 10 === 0;
  };
  const validExp = (val) => {
    const m = val.match(/^(\d{2})\/(\d{2})$/);
    if (!m) return false;
    const month = parseInt(m[1], 10);
    const year  = 2000 + parseInt(m[2], 10);
    if (month < 1 || month > 12) return false;
    return new Date(year, month, 0, 23, 59, 59) >= new Date();
  };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    hideAlert();

    let firstInvalid = null;
    form.querySelectorAll('input[required], select[required]').forEach(el => {
      const ok = el.type === 'checkbox' ? el.checked : (el.value || '').trim() !== '';
      setInvalid(el, !ok);
      if (!ok && !firstInvalid) firstInvalid = el;
    });

    const cardNumRaw = cardNumberEl.value.replace(/\s+/g, '');
    const cardOk = luhn(cardNumRaw);
    setInvalid(cardNumberEl, !cardOk);
    if (!cardOk && !firstInvalid) firstInvalid = cardNumberEl;

    const expOk = validExp(cardExpEl.value);
    setInvalid(cardExpEl, !expOk);
    if (!expOk && !firstInvalid) firstInvalid = cardExpEl;

    const cvcOk = /^\d{3,4}$/.test(cardCvcEl.value);
    setInvalid(cardCvcEl, !cvcOk);
    if (!cvcOk && !firstInvalid) firstInvalid = cardCvcEl;

    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus({ preventScroll: true });
      return;
    }

    if (!AUTH.apiLoginID || !AUTH.clientKey) {
      showAlert('Payment system is not yet configured. Please contact support.');
      return;
    }

    if (typeof Accept === 'undefined' || typeof Accept.dispatchData !== 'function') {
      showAlert('Payment library failed to load. Refresh the page and try again.');
      return;
    }

    payBtn.classList.add('is-loading');
    payBtn.disabled = true;

    const [mm, yy] = cardExpEl.value.split('/');

    Accept.dispatchData({
      authData: AUTH,
      cardData: {
        cardNumber: cardNumRaw,
        month:      mm,
        year:       yy,
        cardCode:   cardCvcEl.value,
        zip:        form.querySelector('input[name="zip"]').value,
        fullName:   form.querySelector('input[name="cardName"]').value,
      }
    }, function (resp) {
      if (resp.messages.resultCode !== 'Ok') {
        const msg = (resp.messages.message[0] && resp.messages.message[0].text) || 'Your card could not be tokenized.';
        showAlert(msg);
        payBtn.classList.remove('is-loading');
        payBtn.disabled = false;
        return;
      }

      document.getElementById('dataDescriptor').value = resp.opaqueData.dataDescriptor;
      document.getElementById('dataValue').value      = resp.opaqueData.dataValue;

      const fd = new FormData(form);
      ['agree_terms','agree_privacy','marketing_opt_in'].forEach(name => {
        const el = form.querySelector(`input[name="${name}"]`);
        if (el && !el.checked) fd.set(name, '0');
      });

      fetch('{{ route('ebooks.checkout.process') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: fd,
      })
      .then(r => r.json().then(j => ({ ok: r.ok, body: j })))
      .then(({ ok, body }) => {
        if (ok && body.success && body.redirect) {
          window.location.href = body.redirect;
          return;
        }
        showAlert(body.message || 'Payment could not be completed. Please try a different card or contact support.');
        payBtn.classList.remove('is-loading');
        payBtn.disabled = false;
      })
      .catch(() => {
        showAlert('A network error stopped your payment. Please try again.');
        payBtn.classList.remove('is-loading');
        payBtn.disabled = false;
      });
    });
  });
})();
</script>

@endsection
