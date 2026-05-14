@extends('layouts.app')

@section('title', 'Secure Checkout — ' . $plan['label'] . ' | Victoria Love')
@section('description', 'Secure 256-bit encrypted checkout. Complete your enrollment with Victoria Love and start your credit transformation today.')
@section('bodyClass', 'page-checkout')

@section('content')

<style>
/* ============ SECURE CHECKOUT — VICTORIA LOVE BRAND ============ */
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
.checkout-hero-head p { font-size: 16px; max-width: 560px; margin: 0 auto; }

.checkout-trust-row {
  display: flex;
  justify-content: center;
  gap: 28px;
  flex-wrap: wrap;
  margin-top: 24px;
  font-size: 12.5px;
  color: var(--ink-3);
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-weight: 600;
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

/* ── Form column ───────────────────────────────────────────────── */
.checkout-form-wrap {
  background: var(--bg-3);
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 36px;
  box-shadow: 0 30px 60px -30px rgba(20,16,14,0.08);
}
.checkout-form-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  padding-bottom: 22px;
  border-bottom: 1px dashed var(--line-2);
}
.checkout-form-head h2 {
  font-size: 22px;
  margin: 0;
}
.checkout-form-head .secure-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #0f8a4a;
  background: rgba(15,138,74,0.08);
  padding: 6px 12px;
  border-radius: 100px;
}
.checkout-form-head .secure-badge svg { width: 12px; height: 12px; }

.co-section {
  margin-bottom: 28px;
}
.co-section + .co-section { padding-top: 4px; }
.co-section-ttl {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 14px;
}
.co-section-ttl::before {
  content: '';
  width: 18px;
  height: 1px;
  background: var(--pink);
}

.co-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.co-grid-3 { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 14px; }
.co-grid-card { display: grid; grid-template-columns: 1fr 110px 110px; gap: 14px; }
.co-full { grid-column: 1 / -1; }

.co-field { display: flex; flex-direction: column; gap: 6px; }
.co-field > span {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ink);
}
.co-field > span em { color: var(--pink); font-style: normal; }
.co-input,
.co-select {
  width: 100%;
  padding: 13px 14px;
  border: 1.5px solid var(--line-2);
  border-radius: var(--r-sm);
  background: #fff;
  font-family: inherit;
  font-size: 15px;
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s, background .2s;
  outline: none;
}
.co-input:focus,
.co-select:focus {
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.10);
}
.co-input.is-invalid,
.co-select.is-invalid {
  border-color: #d93838;
  box-shadow: 0 0 0 4px rgba(217,56,56,0.10);
}
.co-help {
  font-size: 12px;
  color: var(--ink-3);
  margin-top: 2px;
}
.co-help.error {
  color: #d93838;
  font-weight: 600;
}

/* Card brand sniff indicator */
.co-card-wrap { position: relative; }
.co-card-brand {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--ink-3);
  background: var(--bg-2);
  padding: 4px 10px;
  border-radius: 6px;
  pointer-events: none;
  transition: color .2s, background .2s;
}
.co-card-brand.is-visa  { color: #1a1f71; background: #eef0f8; }
.co-card-brand.is-mc    { color: #b8201c; background: #fce8e6; }
.co-card-brand.is-amex  { color: #2671b9; background: #e6f0fa; }
.co-card-brand.is-disc  { color: #ff6000; background: #fff1e6; }

/* Agreement rows */
.co-agree {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  padding: 14px 16px;
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: var(--r-sm);
  margin-bottom: 10px;
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--ink-2);
  cursor: pointer;
}
.co-agree input[type="checkbox"] {
  margin-top: 3px;
  width: 18px;
  height: 18px;
  flex: 0 0 18px;
  accent-color: var(--pink);
  cursor: pointer;
}
.co-agree a { color: var(--pink); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
.co-agree strong { color: var(--ink); }

/* Pay button */
.co-pay-wrap { margin-top: 28px; }
.co-pay-btn {
  width: 100%;
  background: var(--pink);
  color: #fff;
  padding: 18px 24px;
  border-radius: 100px;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.01em;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  box-shadow: 0 16px 36px -12px rgba(230,49,121,0.55);
  transition: transform .25s var(--ease), background .25s, box-shadow .25s;
  position: relative;
  overflow: hidden;
}
.co-pay-btn:hover:not(:disabled) {
  background: var(--ink);
  transform: translateY(-2px);
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
  font-size: 12px;
  color: var(--ink-3);
  text-align: center;
  margin-top: 14px;
  line-height: 1.5;
}
.co-pay-fine svg { width: 11px; height: 11px; margin-right: 4px; vertical-align: -1px; color: #0f8a4a; }

/* Inline alert (errors from gateway) */
.co-alert {
  display: none;
  background: #fce8e8;
  border: 1px solid #f1b5b5;
  color: #8a1f1f;
  padding: 14px 16px;
  border-radius: var(--r-sm);
  margin-bottom: 20px;
  font-size: 14px;
  line-height: 1.5;
}
.co-alert.is-show { display: block; }
.co-alert strong { color: #5d1010; }

/* ── Plan summary card ─────────────────────────────────────────── */
.checkout-summary {
  position: sticky;
  top: 110px;
  background: linear-gradient(180deg, #fff 0%, #fff8fb 100%);
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 30px 28px;
  box-shadow: 0 30px 60px -30px rgba(230,49,121,0.18);
}
.checkout-summary-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 18px;
  border-bottom: 1px dashed var(--line-2);
  margin-bottom: 18px;
}
.checkout-summary-head .eyebrow { margin: 0; }
.checkout-summary-head .change-link {
  font-size: 12.5px;
  color: var(--pink);
  font-weight: 600;
  text-decoration: underline;
  text-underline-offset: 3px;
}
.checkout-summary-plan {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 6px;
}
.checkout-summary-plan .name {
  font-size: 19px;
  font-weight: 700;
  color: var(--ink);
}
.checkout-summary-plan .price {
  font-size: 28px;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.02em;
}
.checkout-summary-tagline { font-size: 13px; color: var(--ink-2); margin-bottom: 18px; }
.checkout-summary-list {
  list-style: none;
  padding: 0;
  margin: 0 0 22px 0;
}
.checkout-summary-list li {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  font-size: 13.5px;
  color: var(--ink-2);
  padding: 7px 0;
  border-bottom: 1px solid var(--line);
}
.checkout-summary-list li:last-child { border-bottom: 0; }
.checkout-summary-list li::before {
  content: '✓';
  color: #0f8a4a;
  font-weight: 700;
  font-size: 14px;
  margin-top: 1px;
}

.checkout-summary-recurring {
  background: var(--pink-soft);
  border: 1px solid rgba(230,49,121,0.18);
  color: #8a1845;
  padding: 12px 14px;
  border-radius: var(--r-sm);
  font-size: 12.5px;
  line-height: 1.55;
  margin-bottom: 18px;
}
.checkout-summary-recurring strong { color: #6b1234; }

.checkout-totals {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-sm);
  padding: 14px 16px;
  margin-bottom: 18px;
}
.checkout-totals .row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 5px 0;
  font-size: 14px;
  color: var(--ink-2);
}
.checkout-totals .row.total {
  border-top: 1px solid var(--line);
  margin-top: 5px;
  padding-top: 12px;
  font-size: 17px;
  font-weight: 700;
  color: var(--ink);
}

.summary-trust {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 12px;
  color: var(--ink-3);
}
.summary-trust span { display: inline-flex; align-items: center; gap: 8px; }
.summary-trust svg { width: 12px; height: 12px; color: var(--pink); }

/* Plan switcher (mobile-friendly select) */
.plan-switcher {
  display: none;
  margin-bottom: 12px;
}
.plan-switcher select {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid var(--line-2);
  border-radius: var(--r-sm);
  font-family: inherit;
  font-size: 14px;
  background: #fff;
  color: var(--ink);
}

/* ── Responsive ────────────────────────────────────────────────── */
@media (max-width: 960px) {
  .checkout-grid { grid-template-columns: 1fr; gap: 24px; }
  .checkout-summary { position: static; order: -1; }
  .checkout-form-wrap { padding: 26px 22px; }
  .co-grid, .co-grid-3, .co-grid-card { grid-template-columns: 1fr; }
}
@media (max-width: 540px) {
  .checkout-hero { padding-top: 110px; }
  .checkout-form-head { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>

<!-- ============ CHECKOUT HERO ============ -->
<section class="checkout-hero">
  <div class="container">
    <div class="checkout-hero-head reveal">
      <span class="eyebrow">Secure checkout · 256-bit encrypted</span>
      <h1>You're <em class="serif gradient-text">one step</em> from changing your credit.</h1>
      <p>Your details stay encrypted. Your card never touches our servers — Authorize.Net tokenizes it on the page.</p>
      <div class="checkout-trust-row">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>256-BIT SSL</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z"/></svg>PCI COMPLIANT</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>AUTHORIZE.NET</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ CHECKOUT GRID ============ -->
<section class="checkout-section">
  <div class="container">
    <div class="checkout-grid">

      <!-- ── FORM COLUMN ─────────────────────────────────── -->
      <div class="checkout-form-wrap reveal">

        <div class="checkout-form-head">
          <h2>Complete your enrollment</h2>
          <span class="secure-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Secured
          </span>
        </div>

        <div class="co-alert" id="coAlert" role="alert">
          <strong>Heads up:</strong>
          <div id="coAlertMsg"></div>
        </div>

        <form id="acceptCheckoutForm" novalidate autocomplete="on">
          @csrf

          <input type="hidden" name="selected_plan" id="selectedPlanInput" value="{{ $planKey }}" />
          <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
          <input type="hidden" name="dataValue"      id="dataValue" />

          <!-- Plan switcher (only on small screens) -->
          <div class="plan-switcher">
            <label>
              <span style="display:block;font-size:12px;font-weight:600;color:var(--ink-3);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:6px">Plan</span>
              <select id="planSwitcher">
                @foreach ($allPlans as $key => $p)
                  <option value="{{ $key }}" @selected($key === $planKey)>{{ $p['label'] }} — ${{ rtrim(rtrim($p['amount'], '0'), '.') }}@if($p['recurring']) + ${{ rtrim(rtrim($p['recurring'], '0'), '.') }}/mo @endif</option>
                @endforeach
              </select>
            </label>
          </div>

          <!-- ── 01 · Contact ───────────────────────────── -->
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
              <span>I have read and agree to the <a href="{{ route('legal.terms-of-service') }}" target="_blank" rel="noopener">Terms of Service</a> and <a href="{{ route('legal.disclaimer') }}" target="_blank" rel="noopener">Disclaimer</a>, and authorize Victorious Opportunities to charge my card for the selected plan. <strong>I understand the recurring monthly charge (if any) will begin 30 days after today.</strong></span>
            </label>

            <label class="co-agree">
              <input type="checkbox" name="agree_privacy" value="1" required />
              <span>I have read the <a href="{{ route('legal.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a> and consent to my data being used to deliver this service.</span>
            </label>

            <label class="co-agree">
              <input type="checkbox" name="marketing_opt_in" value="1" />
              <span>Send me occasional credit, funding &amp; homeownership tips via email and SMS (optional — unsubscribe anytime).</span>
            </label>
          </div>

          <!-- ── Pay button ─────────────────────────────── -->
          <div class="co-pay-wrap">
            <button type="submit" class="co-pay-btn" id="coPayBtn">
              <span class="label">Pay <span id="coPayAmount">${{ rtrim(rtrim($plan['amount'], '0'), '.') }}</span> &amp; activate my plan</span>
              <span class="spinner" aria-hidden="true"></span>
            </button>
            <p class="co-pay-fine">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
              You'll be redirected to your secure onboarding form after a successful payment.
            </p>
          </div>
        </form>
      </div>

      <!-- ── PLAN SUMMARY ────────────────────────────────── -->
      <aside class="checkout-summary reveal reveal-d2" id="planSummary">
        <div class="checkout-summary-head">
          <span class="eyebrow">Your plan</span>
          <a href="{{ url('/#pricing') }}" class="change-link">Change</a>
        </div>

        <div class="checkout-summary-plan">
          <div class="name" id="summaryName">{{ $plan['label'] }}</div>
          <div class="price" id="summaryPrice">${{ rtrim(rtrim($plan['amount'], '0'), '.') }}</div>
        </div>
        <p class="checkout-summary-tagline" id="summaryTagline">{{ $plan['tagline'] }}</p>

        <ul class="checkout-summary-list" id="summaryFeatures">
          @foreach ($plan['features'] as $f)
            <li>{{ $f }}</li>
          @endforeach
        </ul>

        <div class="checkout-summary-recurring" id="summaryRecurring" @if(!$plan['recurring']) style="display:none" @endif>
          <strong>Monthly billing:</strong> After today's payment, you'll be billed <strong>${{ rtrim(rtrim($plan['recurring'] ?? '0', '0'), '.') }}/month</strong> starting in 30 days. Cancel anytime.
        </div>

        <div class="checkout-totals">
          <div class="row">
            <span>Plan</span>
            <span id="totalsLabel">{{ $plan['label'] }}</span>
          </div>
          <div class="row">
            <span>Today's charge</span>
            <span id="totalsAmount">${{ number_format((float) $plan['amount'], 2) }}</span>
          </div>
          <div class="row total">
            <span>Total today</span>
            <span id="totalsFinal">${{ number_format((float) $plan['amount'], 2) }}</span>
          </div>
        </div>

        <div class="summary-trust">
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Bank-grade 256-bit SSL encryption</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z"/></svg>Processed by Authorize.Net — PCI Level 1</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>BBB-aligned · FCRA compliant credit work</span>
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

@php
  $plansForJs = [];
  foreach ($allPlans as $key => $p) {
      $plansForJs[$key] = [
          'label'     => $p['label'],
          'amount'    => $p['amount'],
          'recurring' => $p['recurring'],
          'tagline'   => $p['tagline'],
          'features'  => $p['features'],
      ];
  }
@endphp
<script>
(function () {
  /* ─────────────── PLAN CATALOGUE (mirror of PHP) ─────────────── */
  const PLANS = {!! json_encode($plansForJs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

  /* ─────────────── CONFIG ─────────────── */
  const AUTH = {
    apiLoginID: {!! json_encode($apiLogin ?? '') !!},
    clientKey:  {!! json_encode($clientKey ?? '') !!}
  };

  /* ─────────────── EL refs ─────────────── */
  const form          = document.getElementById('acceptCheckoutForm');
  const planSwitcher  = document.getElementById('planSwitcher');
  const planInput     = document.getElementById('selectedPlanInput');
  const summaryName   = document.getElementById('summaryName');
  const summaryPrice  = document.getElementById('summaryPrice');
  const summaryTag    = document.getElementById('summaryTagline');
  const summaryFeats  = document.getElementById('summaryFeatures');
  const summaryRec    = document.getElementById('summaryRecurring');
  const totalsLabel   = document.getElementById('totalsLabel');
  const totalsAmount  = document.getElementById('totalsAmount');
  const totalsFinal   = document.getElementById('totalsFinal');
  const coPayAmount   = document.getElementById('coPayAmount');
  const payBtn        = document.getElementById('coPayBtn');
  const alertBox      = document.getElementById('coAlert');
  const alertMsg      = document.getElementById('coAlertMsg');

  const cardNumberEl  = document.getElementById('cardNumber');
  const cardExpEl     = document.getElementById('cardExp');
  const cardCvcEl     = document.getElementById('cardCvc');
  const cardBrandEl   = document.getElementById('cardBrand');
  const phoneEl       = document.getElementById('coPhone');

  /* ─────────────── Money formatter ─────────────── */
  const money = (v) => '$' + Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const moneyShort = (v) => { const n = Number(v); return '$' + (n % 1 === 0 ? n.toFixed(0) : n.toFixed(2)); };

  /* ─────────────── Plan switching ─────────────── */
  const applyPlan = (key) => {
    const p = PLANS[key];
    if (!p) return;
    planInput.value = key;

    summaryName.textContent  = p.label;
    summaryPrice.textContent = moneyShort(p.amount);
    summaryTag.textContent   = p.tagline;
    summaryFeats.innerHTML   = p.features.map(f => `<li>${f}</li>`).join('');

    if (p.recurring) {
      summaryRec.style.display = '';
      summaryRec.innerHTML = `<strong>Monthly billing:</strong> After today's payment, you'll be billed <strong>${moneyShort(p.recurring)}/month</strong> starting in 30 days. Cancel anytime.`;
    } else {
      summaryRec.style.display = 'none';
    }

    totalsLabel.textContent  = p.label;
    totalsAmount.textContent = money(p.amount);
    totalsFinal.textContent  = money(p.amount);
    coPayAmount.textContent  = moneyShort(p.amount);

    // Update URL without reload (so a refresh keeps the plan)
    try { history.replaceState({}, '', '/checkout/' + key); } catch (e) {}
  };

  if (planSwitcher) {
    planSwitcher.addEventListener('change', (e) => applyPlan(e.target.value));
  }

  /* ─────────────── Phone mask ─────────────── */
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

  /* ─────────────── Card masks + brand sniff ─────────────── */
  const detectBrand = (raw) => {
    const n = raw.replace(/\D+/g, '');
    if (/^4/.test(n))                        return { brand: 'visa', label: 'VISA' };
    if (/^(5[1-5]|2[2-7])/.test(n))          return { brand: 'mc',   label: 'MC' };
    if (/^3[47]/.test(n))                    return { brand: 'amex', label: 'AMEX' };
    if (/^(6011|65|64[4-9])/.test(n))        return { brand: 'disc', label: 'DISCOVER' };
    return { brand: '', label: 'CARD' };
  };
  cardNumberEl.addEventListener('input', () => {
    let d = cardNumberEl.value.replace(/\D+/g, '').slice(0, 19);
    // group in 4s; AMEX uses 4-6-5 but Authorize accepts either, we keep 4-4-4-4 visually
    const groups = d.match(/.{1,4}/g) || [];
    cardNumberEl.value = groups.join(' ');
    const { brand, label } = detectBrand(d);
    cardBrandEl.textContent = label;
    cardBrandEl.className = 'co-card-brand' + (brand ? ' is-' + brand : '');
  });

  cardExpEl.addEventListener('input', () => {
    let d = cardExpEl.value.replace(/\D+/g, '').slice(0, 4);
    if (d.length >= 3) cardExpEl.value = d.slice(0, 2) + '/' + d.slice(2);
    else cardExpEl.value = d;
  });

  cardCvcEl.addEventListener('input', () => {
    cardCvcEl.value = cardCvcEl.value.replace(/\D+/g, '').slice(0, 4);
  });

  /* ─────────────── Field validation helpers ─────────────── */
  const setInvalid = (el, on) => el.classList.toggle('is-invalid', !!on);
  const showAlert = (text) => {
    alertMsg.textContent = text;
    alertBox.classList.add('is-show');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };
  const hideAlert = () => alertBox.classList.remove('is-show');

  const validateCardLuhn = (num) => {
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

  const validateExp = (val) => {
    const m = val.match(/^(\d{2})\/(\d{2})$/);
    if (!m) return false;
    const month = parseInt(m[1], 10);
    const year  = 2000 + parseInt(m[2], 10);
    if (month < 1 || month > 12) return false;
    const now = new Date();
    const expEnd = new Date(year, month, 0, 23, 59, 59);
    return expEnd >= now;
  };

  /* ─────────────── Form submit → Accept.js → POST ─────────────── */
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    hideAlert();

    // Quick required-field check
    let firstInvalid = null;
    form.querySelectorAll('input[required], select[required]').forEach(el => {
      const ok = el.type === 'checkbox' ? el.checked : (el.value || '').trim() !== '';
      setInvalid(el, !ok);
      if (!ok && !firstInvalid) firstInvalid = el;
    });

    const cardNumRaw = cardNumberEl.value.replace(/\s+/g, '');
    const cardOk     = validateCardLuhn(cardNumRaw);
    setInvalid(cardNumberEl, !cardOk);
    if (!cardOk && !firstInvalid) firstInvalid = cardNumberEl;

    const expOk = validateExp(cardExpEl.value);
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
      // Ensure unchecked checkboxes still send a value (Laravel needs them present for `required|accepted`)
      ['agree_terms','agree_privacy','marketing_opt_in'].forEach(name => {
        const el = form.querySelector(`input[name="${name}"]`);
        if (el && !el.checked) fd.set(name, '0');
      });

      fetch('{{ route('checkout.process') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: fd,
      })
      .then(r => r.json().then(j => ({ ok: r.ok, status: r.status, body: j })))
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
