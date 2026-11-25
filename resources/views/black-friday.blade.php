@extends('layouts.app')

@section('title', 'Black Friday Credit Comeback Sale | Victoria Love')
@section('description', '3 months of credit repair: $99 for one person or $199 for two. The sale is open now.')

@php
  $plans = \App\Http\Controllers\AcceptJsPaymentController::PLANS;
  // Drop her sale flyer in at this path and it appears in the hero; until then
  // her portrait is used.
  $flyer = 'images/black-friday-flyer.jpg';
  $heroImage = file_exists(public_path($flyer)) ? $flyer : 'images/founderimage7.jpeg';
@endphp

@section('lcp_preload')
  <link rel="preload" as="image" href="{{ asset($heroImage) }}" fetchpriority="high">
@endsection

@section('content')
<style>
.bf { background: #0d0609; color: #fff; }
.bf section { padding: 80px 0; }
.bf p { color: rgba(255,255,255,0.75); }
.bf h1, .bf h2, .bf h3 { color: #fff; }

.bf .bf-hero {
  padding-top: 130px;
  background:
    radial-gradient(60% 70% at 85% 15%, rgba(230,49,121,0.45), transparent 65%),
    radial-gradient(45% 50% at 0% 100%, rgba(200,154,74,0.22), transparent 65%);
}
.bf-hero-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 56px; align-items: center; }
.bf-kicker { font-size: 12px; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; color: var(--gold-2); margin-bottom: 18px; }
/* The site's "See pricing" buttons lead to full-price plans: keep sale visitors here. */
.nav-cta, .nav-mobile a.cta, .sticky-cta { display: none !important; }
body { padding-bottom: 0 !important; }

.bf h1 { font-size: clamp(3rem, 7vw, 5.6rem); font-weight: 700; text-transform: uppercase; letter-spacing: -.015em; line-height: .95; margin-bottom: 12px; }
.bf h1 span {
  display: block; font-size: .45em; margin-top: 14px; line-height: 1.15; letter-spacing: .01em;
  background: linear-gradient(135deg, var(--pink-2), var(--pink) 45%, var(--gold-2));
  -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
}
.bf .bf-sub { font-size: 19px; max-width: 520px; margin: 18px 0 28px; }
.bf-ctas { display: flex; gap: 12px; flex-wrap: wrap; }
.bf-date { display: inline-block; margin-top: 28px; padding: 12px 20px; border: 1.5px solid var(--pink); border-radius: 100px; font-weight: 700; font-size: 14px; letter-spacing: .06em; text-transform: uppercase; }
.bf-art img { width: 100%; max-width: 460px; margin: 0 auto; border-radius: var(--r-lg); border: 1px solid rgba(230,196,129,0.4); box-shadow: 0 40px 90px -30px rgba(230,49,121,0.6); }

.bf-head { text-align: center; max-width: 640px; margin: 0 auto 44px; }
.bf-head h2 { margin-bottom: 12px; }

.bf-plans { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; max-width: 820px; margin: 0 auto; }
.bf-plan { display: flex; flex-direction: column; text-align: center; padding: 36px 30px; border-radius: var(--r-lg); background: linear-gradient(180deg, #241019, #150a0f); border: 1.5px solid rgba(230,49,121,0.55); box-shadow: 0 30px 60px -30px rgba(230,49,121,0.5); }
.bf-plan .name { font-size: 12.5px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-2); }
.bf-plan .amt { font-size: 76px; font-weight: 700; letter-spacing: -.04em; line-height: 1.1; margin: 8px 0 0; }
.bf-plan .per { font-size: 13.5px; color: rgba(255,255,255,0.65); margin-bottom: 22px; }
.bf-plan ul { list-style: none; margin: 0 0 26px; flex: 1; text-align: left; }
.bf-plan li { padding: 11px 0; border-top: 1px solid rgba(255,255,255,0.1); font-size: 14.5px; }
.bf-plan li::before { content: "✓"; color: var(--pink-2); font-weight: 700; margin-right: 10px; }
.bf-plan .btn { width: 100%; }

.bf-items { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 960px; margin: 0 auto; }
.bf-item { padding: 16px 18px; border-radius: 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); font-weight: 600; font-size: 14.5px; }
.bf-item::before { content: "✓"; color: var(--pink); margin-right: 10px; }

.bf-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; max-width: 960px; margin: 0 auto; }
.bf-step { padding: 28px 24px; border-radius: var(--r-md); background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
.bf-step b { display: block; font-size: 34px; color: var(--gold-2); line-height: 1; margin-bottom: 14px; }
.bf-step h3 { font-size: 19px; margin-bottom: 8px; }
.bf-step p { font-size: 14.5px; }

.bf-close { text-align: center; }
.bf .bf-fine { font-size: 12.5px; color: rgba(255,255,255,0.5); max-width: 720px; margin: 34px auto 0; }

@media (max-width: 900px) {
  .bf section { padding: 56px 0; }
  .bf .bf-hero { padding-top: 110px; }
  .bf-hero-grid { grid-template-columns: 1fr; gap: 36px; }
  .bf-items { grid-template-columns: repeat(2, 1fr); }
  .bf-steps { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .bf-plans { grid-template-columns: 1fr; }
}
</style>

<div class="bf">

  <section class="bf-hero">
    <div class="container bf-hero-grid">
      <div>
        <div class="bf-kicker">Victorious Opportunities LLC presents</div>
        <h1>Black Friday <span>The Credit Comeback Sale</span></h1>
        <p class="bf-sub">New year. New goals. Fresh start. Get 3 months of credit repair for one low total price.</p>
        <div class="bf-ctas">
          <a href="#bf-plans" class="btn btn-pink">Get the sale price <span class="arr">→</span></a>
        </div>
        <div class="bf-date">The sale is open now</div>
      </div>
      <div class="bf-art">
        <img src="{{ asset($heroImage) }}" alt="Victoria Love — Black Friday Credit Comeback Sale" width="460" height="560" fetchpriority="high" decoding="async" />
      </div>
    </div>
  </section>

  <section id="bf-plans">
    <div class="container">
      <div class="bf-head">
        <h2>Pick your comeback</h2>
        <p>One payment is the total service price. Nothing recurring.</p>
      </div>
      <div class="bf-plans">
        <div class="bf-plan">
          <div class="name">The Comeback Plan</div>
          <div class="amt">${{ (int) $plans['black-friday']['amount'] }}</div>
          <div class="per">Individual plan · one-time</div>
          <ul>
            <li>3 months of credit repair</li>
            <li>For one person</li>
            <li>No monthly charges</li>
          </ul>
          <a href="{{ route('checkout.show', 'black-friday') }}" class="btn btn-pink">Start my comeback <span class="arr">→</span></a>
        </div>
        <div class="bf-plan">
          <div class="name">The Couples Comeback</div>
          <div class="amt">${{ (int) $plans['black-friday-couples']['amount'] }}</div>
          <div class="per">For two · one-time</div>
          <ul>
            <li>3 months of credit repair</li>
            <li>For two people</li>
            <li>No monthly charges</li>
          </ul>
          <a href="{{ route('checkout.show', 'black-friday-couples') }}" class="btn btn-pink">Start our comeback <span class="arr">→</span></a>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="bf-head">
        <h2>We target to remove</h2>
        <p>Inaccurate, incomplete or unverifiable items such as:</p>
      </div>
      <div class="bf-items">
        @foreach (['Collections', 'Charge-Offs', 'Repossessions', 'Evictions', 'Late Payments', 'Child Support', 'Bankruptcy', 'Other Negative Items'] as $item)
          <div class="bf-item">{{ $item }}</div>
        @endforeach
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="bf-head"><h2>What happens after you pay</h2></div>
      <div class="bf-steps">
        <div class="bf-step"><b>1</b><h3>Sign your agreement</h3><p>It opens right after payment and shows exactly what you paid.</p></div>
        <div class="bf-step"><b>2</b><h3>Complete onboarding</h3><p>Fill in your details and upload your documents. Couples: each person completes their own form.</p></div>
        <div class="bf-step"><b>3</b><h3>We get to work</h3><p>We review your reports and start your disputes.</p></div>
      </div>
    </div>
  </section>

  <section class="bf-close">
    <div class="container">
      <div class="bf-head">
        <h2>Up your score &amp; open doors</h2>
        <p>Questions first? Call <a href="tel:+14693495553" style="color:var(--pink-2);font-weight:700">469-349-5553</a>, 10 AM – 5 PM Central.</p>
      </div>
      <a href="#bf-plans" class="btn btn-pink">Choose my plan <span class="arr">→</span></a>
      <p class="bf-fine">Results vary and are not guaranteed. No particular score increase, removal of any item, or approval is promised. Accurate, verifiable information cannot be removed. You may dispute inaccurate information with the credit bureaus yourself at no cost. See our <a href="{{ route('legal.disclaimer') }}" style="text-decoration:underline">Disclaimer</a>.</p>
    </div>
  </section>

</div>
@endsection
