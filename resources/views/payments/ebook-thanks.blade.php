@extends('layouts.app')

@section('title', 'Your ebook is ready — ' . $order->ebook_title . ' | Victoria Love')
@section('description', 'Thank you for your purchase. Your private download link is ready.')
@section('bodyClass', 'page-ebook-thanks')

@section('content')

<style>
.ebt-section {
  padding: 130px 0 100px;
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(70% 60% at 0% 0%, rgba(230,49,121,0.10), transparent 65%),
    radial-gradient(60% 50% at 100% 100%, rgba(200,154,74,0.10), transparent 65%),
    linear-gradient(180deg, var(--bg) 0%, #fff 100%);
}
.ebt-section::before {
  content: ""; position: absolute; pointer-events: none;
  top: -120px; right: -160px; width: 540px; height: 540px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(230,49,121,0.14), transparent 70%);
  filter: blur(6px);
}
.ebt-wrap {
  max-width: 720px;
  margin: 0 auto;
  text-align: center;
  position: relative;
  z-index: 1;
}

.ebt-success-ico {
  width: 84px; height: 84px;
  border-radius: 50%;
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  color: #fff;
  display: grid; place-items: center;
  font-size: 40px;
  margin: 0 auto 22px;
  box-shadow:
    0 22px 44px -14px rgba(34,197,94,0.55),
    0 0 0 8px rgba(34,197,94,0.10);
  animation: ebtPop .55s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes ebtPop {
  0%   { transform: scale(0.4); opacity: 0; }
  100% { transform: scale(1);   opacity: 1; }
}
.ebt-eye {
  display: inline-block;
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase;
  font-weight: 700; color: #157a3d;
  background: rgba(34,197,94,0.12);
  padding: 6px 16px; border-radius: 100px;
  margin-bottom: 16px;
}
.ebt-wrap h1 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  line-height: 1.05;
  letter-spacing: -0.03em;
  margin: 0 0 14px;
}
.ebt-wrap .lede {
  font-size: 17px; line-height: 1.6;
  color: var(--ink-2);
  max-width: 560px; margin: 0 auto 32px;
}

.ebt-card {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 24px;
  align-items: center;
  text-align: left;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 24px;
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.18);
  margin: 0 auto 26px;
  max-width: 560px;
}
.ebt-card .cover {
  position: relative;
  transform: rotate(-2deg);
}
.ebt-card .cover img {
  width: 100%; display: block;
  border-radius: 6px;
  box-shadow:
    -2px 0 0 rgba(20,16,14,0.06),
    -4px 0 0 rgba(20,16,14,0.04),
    0 22px 44px -14px rgba(20,16,14,0.4);
}
.ebt-card .info .ttl {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 24px; line-height: 1.15;
  letter-spacing: -0.01em; color: var(--ink);
  margin: 0 0 6px;
}
.ebt-card .info .meta {
  font-size: 13px; color: var(--ink-3);
  margin: 0;
}
.ebt-card .info .order-no {
  display: inline-block;
  font-family: 'SF Mono', Menlo, monospace;
  font-size: 11.5px;
  color: var(--ink-2);
  background: var(--bg-2);
  padding: 3px 9px;
  border-radius: 100px;
  margin-top: 8px;
}

.ebt-cta-wrap {
  display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;
  margin-bottom: 22px;
}
.ebt-cta {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--pink);
  color: #fff;
  padding: 16px 30px;
  border-radius: 100px;
  font-size: 15.5px; font-weight: 700;
  box-shadow: 0 18px 36px -12px rgba(230,49,121,0.55);
  transition: transform .25s, background .25s, box-shadow .25s;
}
.ebt-cta:hover {
  background: var(--ink);
  transform: translateY(-2px);
  box-shadow: 0 22px 44px -12px rgba(20,16,14,0.5);
}
.ebt-cta .arr { font-size: 18px; transition: translate .25s; }
.ebt-cta:hover .arr { translate: 4px 0; }
.ebt-cta.ghost {
  background: transparent;
  color: var(--ink-2);
  border: 1px solid var(--line-2);
  box-shadow: none;
  padding: 14px 24px;
  font-size: 13.5px;
}
.ebt-cta.ghost:hover { color: var(--pink); border-color: var(--pink); transform: none; }

.ebt-redirect-bar {
  display: inline-flex; align-items: center; gap: 12px;
  padding: 10px 18px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 100px;
  font-size: 13px; color: var(--ink-2);
  margin-bottom: 32px;
  box-shadow: 0 8px 20px -12px rgba(20,16,14,0.15);
}
.ebt-redirect-bar .dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--pink);
  animation: ebtPulse 1.4s infinite;
}
.ebt-redirect-bar strong { color: var(--ink); font-weight: 700; }
@keyframes ebtPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(230,49,121,0.7); }
  60%      { box-shadow: 0 0 0 8px rgba(230,49,121,0); }
}

.ebt-receipt {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 32px;
  max-width: 560px;
  margin: 0 auto 24px;
  padding: 22px 26px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  text-align: left;
}
.ebt-receipt .lab {
  font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--ink-3); font-weight: 700;
  margin-bottom: 4px;
}
.ebt-receipt .val {
  font-size: 14px; color: var(--ink); font-weight: 600;
  word-break: break-word;
}
.ebt-receipt .val.mono { font-family: 'SF Mono', Menlo, monospace; font-size: 12.5px; }

.ebt-fine {
  font-size: 12.5px; color: var(--ink-3);
  max-width: 460px; margin: 0 auto;
  line-height: 1.6;
}
.ebt-fine a { color: var(--pink); font-weight: 600; }

.ebt-pending {
  max-width: 560px; margin: 0 auto;
  padding: 22px 26px;
  background: #fff7ed;
  border: 1px solid #fed7aa;
  border-radius: var(--r-lg);
  font-size: 14px; color: #9a3412;
  line-height: 1.55;
  text-align: left;
}
.ebt-pending strong { display: block; color: #7c2d12; font-weight: 700; margin-bottom: 6px; }
.ebt-pending a { color: var(--pink); font-weight: 600; }

@media (max-width: 540px) {
  .ebt-card { grid-template-columns: 1fr; text-align: center; }
  .ebt-card .cover { max-width: 140px; margin: 0 auto; transform: none; }
  .ebt-receipt { grid-template-columns: 1fr; gap: 14px; }
}
</style>

@php
  $hasLink = !empty($driveLink);
@endphp

<section class="ebt-section">
  <div class="container">
    <div class="ebt-wrap">

      <div class="ebt-success-ico">✓</div>
      <span class="ebt-eye">Payment confirmed</span>
      <h1>Your ebook is <em class="serif gradient-text">ready.</em></h1>
      <p class="lede">Thank you, <strong>{{ $order->first_name }}</strong>. We've charged your card and a receipt is on its way to <strong>{{ $order->email }}</strong>.</p>

      @if($ebook)
        <div class="ebt-card">
          @if($ebook->cover_image)
            <div class="cover">
              <img src="{{ asset($ebook->cover_image) }}" alt="{{ $ebook->title }}" />
            </div>
          @endif
          <div class="info">
            <h2 class="ttl">{{ $order->ebook_title }}</h2>
            <p class="meta">Digital download · PDF</p>
            <span class="order-no">Order #{{ $order->invoice_number }}</span>
          </div>
        </div>
      @endif

      @if($hasLink)
        <div class="ebt-redirect-bar" id="ebtRedirectBar">
          <span class="dot"></span>
          <span>Opening your download in <strong id="ebtCount">3</strong> seconds…</span>
        </div>

        <div class="ebt-cta-wrap">
          <a href="{{ $driveLink }}" class="ebt-cta" id="ebtDownloadBtn" target="_blank" rel="noopener">
            Open your ebook now <span class="arr">→</span>
          </a>
          <a href="{{ url('/') }}" class="ebt-cta ghost">Back to home</a>
        </div>
      @else
        <div class="ebt-pending">
          <strong>Your download is being prepared.</strong>
          We've received your payment in full — your private download link will be emailed to <strong>{{ $order->email }}</strong> within the next few minutes. If you don't see it, check your spam folder or <a href="{{ route('contact.show') }}">contact support</a> with order <code>{{ $order->invoice_number }}</code>.
        </div>
      @endif

      <div class="ebt-receipt">
        <div>
          <div class="lab">Item</div>
          <div class="val">{{ $order->ebook_title }}</div>
        </div>
        <div>
          <div class="lab">Amount paid</div>
          <div class="val">${{ number_format((float) $order->amount, 2) }}</div>
        </div>
        <div>
          <div class="lab">Order number</div>
          <div class="val mono">{{ $order->invoice_number }}</div>
        </div>
        <div>
          <div class="lab">Date</div>
          <div class="val">{{ $order->charged_at?->format('M j, Y · g:ia') ?? $order->created_at->format('M j, Y · g:ia') }}</div>
        </div>
      </div>

      <p class="ebt-fine">
        Need anything? <a href="{{ route('contact.show') }}">Contact support</a> with your order number and we'll respond within 24 hours.
      </p>

    </div>
  </div>
</section>

@if($hasLink)
<script>
(function () {
  const btn  = document.getElementById('ebtDownloadBtn');
  const cnt  = document.getElementById('ebtCount');
  const bar  = document.getElementById('ebtRedirectBar');
  if (!btn || !cnt) return;

  let remaining = 3;
  const tick = () => {
    remaining -= 1;
    if (remaining <= 0) {
      if (bar) bar.style.display = 'none';
      // Use location.assign so back button still works
      window.location.assign(btn.href);
      return;
    }
    cnt.textContent = remaining;
    setTimeout(tick, 1000);
  };
  setTimeout(tick, 1000);
})();
</script>
@endif

@endsection
