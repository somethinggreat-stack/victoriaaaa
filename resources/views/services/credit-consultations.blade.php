@extends('layouts.app')

@section('title', 'Credit Consultations — 1:1 Audit Calls | Victoria Love')
@section('description', 'Stuck on your credit file? Book a 60-minute 1:1 audit call with Victoria. Custom dispute strategy, pre-approval prep and an action plan you can run this week.')

@section('content')

<!-- HERO -->
<section class="svc-hero">
  <div class="container">
    <div class="svc-hero-grid">
      <div class="svc-hero-text reveal">
        <span class="eyebrow">03 · Credit Consultations</span>
        <h1>1:1 audit calls — <em class="serif gradient-text">today.</em></h1>
        <p class="lede">
          Stuck on your file? Hop on a call. <strong>I'll pull the report with you</strong>, find the wins,
          and hand you a plan you can run <strong>this week</strong>.
        </p>
        <div class="svc-hero-ctas">
          <a href="{{ url('/') }}#contact" class="btn btn-pink">Book my consult <span class="arr">→</span></a>
          <a href="{{ url('/') }}#pricing" class="btn btn-ghost">See pricing</a>
        </div>

        <div class="svc-hero-stats">
          <div class="stt"><strong>60 min</strong><span>1:1 with Victoria</span></div>
          <div class="stt"><strong>This week</strong><span>Same-week openings</span></div>
          <div class="stt"><strong>100%</strong><span>Custom to your file</span></div>
        </div>
      </div>

      <div class="svc-hero-img reveal reveal-d2">
        <span class="tag">Audit · Strategy · Action</span>
        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=75" alt="Credit consultation" width="900" height="1125" fetchpriority="high" decoding="async" />
        <div class="fly-card">
          <div class="icn">→</div>
          <div>
            <div class="lab">Action plan</div>
            <div class="val">Walk away with a roadmap</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OUTCOMES -->
<section class="svc-outcomes">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">What you walk away with</span>
      <h2>One call. <em class="serif gradient-text">Total clarity.</em></h2>
      <p>You leave the call knowing exactly what to dispute, what to leave, and what to do this week.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><div class="ic">⌕</div><h3>Full audit</h3><p>Line-by-line review of all 3 bureau reports, scored by lender impact.</p></div>
      <div class="outcome reveal reveal-d2"><div class="ic">★</div><h3>Custom plan</h3><p>A dispute strategy and utilization plan calibrated to your file, your goals.</p></div>
      <div class="outcome reveal reveal-d3"><div class="ic">$</div><h3>Pre-approval prep</h3><p>If a mortgage or business loan is on the horizon, we sequence the moves.</p></div>
      <div class="outcome reveal reveal-d4"><div class="ic">▶</div><h3>Recording &amp; PDF</h3><p>Call replay plus a written action plan so nothing slips through.</p></div>
    </div>
  </div>
</section>

<!-- INCLUDED -->
<section class="svc-included">
  <div class="container">
    <div class="included-grid">
      <div class="included-img reveal">
        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=900&q=75" alt="1:1 consultation call" width="900" height="600" loading="lazy" decoding="async" />
      </div>
      <div class="included-list reveal reveal-d2">
        <span class="eyebrow">What's included</span>
        <h2>Everything in <em class="serif gradient-text">the hour.</em></h2>
        <p>One concentrated session — no fluff, no upsells, just answers and a real plan.</p>
        <ul>
          <li><span class="ck">✓</span><div><strong>60-minute private 1:1 with Victoria</strong><small>Zoom or phone — your call.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Full 3-bureau report audit</strong><small>We pull live, line by line, and flag every dispute candidate together.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Custom dispute strategy</strong><small>What to dispute, in what order, and what to leave alone.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Pre-approval roadmap</strong><small>If you want a mortgage or business funding next, we sequence it.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Action-plan PDF + call recording</strong><small>So you can actually run the plan this week — not forget it tomorrow.</small></div></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- PROCESS -->
<section class="process-section" style="background: var(--bg-2); border-block: 1px solid var(--line);">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">How it works</span>
      <h2>Three moves. <em class="serif gradient-text">Same week.</em></h2>
    </div>
    <div class="process-grid cols-3">
      <div class="step reveal">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>1</span> Book</span>
          <span class="day">Today</span>
        </div>
        <div class="step-body"><h3>Lock the slot</h3><p>Pick a same-week time. You'll get a short intake form so we don't waste a minute on call.</p></div>
      </div>
      <div class="step reveal reveal-d2">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>2</span> Audit</span>
          <span class="day">On call</span>
        </div>
        <div class="step-body"><h3>Pull &amp; audit live</h3><p>We pull all 3 bureaus together, find the wins, and rank them by score impact.</p></div>
      </div>
      <div class="step reveal reveal-d3">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>3</span> Action plan</span>
          <span class="day">Within 24h</span>
        </div>
        <div class="step-body"><h3>Plan in your inbox</h3><p>You get a written PDF of the strategy plus the call replay. Run it solo or upgrade to full repair.</p></div>
      </div>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="svc-quote">
  <div class="container">
    <div class="quote-card reveal">
      <div class="stars">★★★★★</div>
      <div class="q">
        "Sixty minutes with Victoria saved me from sending the wrong disputes for the next six months. Walked off the call with a written plan and was pre-approved 11 weeks later."
      </div>
      <div class="who">
        <div class="av">AR</div>
        <div>
          <div class="nm">Aliyah R.</div>
          <div class="loc">Austin, TX · Consult client</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq-section">
  <div class="container">
    <div class="faq-wrap">
      <div class="faq-side reveal">
        <span class="eyebrow">FAQ · Consults</span>
        <h2>Questions, <em class="serif gradient-text">answered.</em></h2>
        <p>What clients want to know before they pick a time.</p>
        <div class="ctas">
          <a href="{{ url('/') }}#contact" class="btn btn-pink">Book your consult <span class="arr">→</span></a>
        </div>
      </div>
      <div class="faq-list reveal reveal-d2">
        <div class="faq-item"><div class="faq-q">What should I bring to the call? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Just yourself. The intake form covers everything we need; we pull the report live on the call.</div></div></div>
        <div class="faq-item"><div class="faq-q">How is this different from full credit repair? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Repair is done-for-you over 90 days. The consult is done-with-you in 60 minutes — perfect if you want to run the plan yourself, or just want clarity before signing on.</div></div></div>
        <div class="faq-item"><div class="faq-q">Will you dispute items for me? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">On the consult, no — that's full repair. But the plan I send is detailed enough that most people execute it just fine on their own.</div></div></div>
        <div class="faq-item"><div class="faq-q">Is the call virtual? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Yes — Zoom (with screen-share for the audit) or phone if you prefer.</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="svc-cta">
  <div class="container">
    <div class="cta-card reveal">
      <div class="cta-text">
        <span class="eyebrow">Same-week openings</span>
        <h2>Lock in <em class="serif">an hour with me.</em></h2>
        <p>Sixty minutes is usually all it takes to know exactly what move to make next. Free 15-min strategy call first if you want to test fit.</p>
        <div class="ctas">
          <a href="{{ url('/') }}#contact" class="btn btn-pink">Book my consult <span class="arr">→</span></a>
          <a href="{{ route('contact.show') }}" class="btn btn-ghost-light">Free 15-min first</a>
        </div>
        <div class="stamp">
          <img src="{{ asset('images/founderimage7.jpeg') }}" alt="Victoria Love" width="48" height="48" loading="lazy" decoding="async" />
          <div><div class="nm">Victoria Love</div><div class="ttl">Texas Realtor &amp; Credit Coach</div></div>
        </div>
      </div>
      <div class="cta-image">
        <img src="{{ asset('images/founderimage6.jpeg') }}" alt="Victoria Love" loading="lazy" decoding="async" />
      </div>
    </div>
  </div>
</section>


@endsection
