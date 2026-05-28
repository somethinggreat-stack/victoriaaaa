@extends('layouts.app')

@section('title', 'Free Strategy Call | Victoria Love')
@section('description', 'Stuck on your credit file? Book a free 15-minute strategy call with Victoria. Pull your reports together, find the wins, walk away with a 90-day plan.')

@section('content')

<!-- HERO -->
<section class="svc-hero">
  <div class="container">
    <div class="svc-hero-grid">
      <div class="svc-hero-text reveal">
        <span class="eyebrow">03 · Free Strategy Call</span>
        <h1>Free 15-min strategy call — <em class="serif gradient-text">no card, no pitch.</em></h1>
        <p class="lede">
          Stuck on your file? Hop on Zoom with me. <strong>We pull your real reports together</strong>,
          find the wins, and you leave with a <strong>90-day plan you can run this week</strong>.
        </p>
        <div class="svc-hero-ctas">
          <a href="{{ route('strategy-call.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
          <a href="{{ url('/') }}#pricing" class="btn btn-ghost">See plans</a>
        </div>

        <div class="svc-hero-stats">
          <div class="stt"><strong>15 min</strong><span>On Zoom with Victoria</span></div>
          <div class="stt"><strong>This week</strong><span>Same-week openings</span></div>
          <div class="stt"><strong>$0</strong><span>Free · no card needed</span></div>
        </div>
      </div>

      <div class="svc-hero-img reveal reveal-d2">
        <span class="tag">Free · 15 min · Zoom</span>
        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=75" alt="Free strategy call" width="900" height="1125" fetchpriority="high" decoding="async" />
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
      <p>You leave the call knowing exactly what's hurting your score, what to tackle first, and what your next 90 days look like.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><div class="ic">⌕</div><h3>Live report review</h3><p>We log into your monitoring service together and walk every line — bureau by bureau.</p></div>
      <div class="outcome reveal reveal-d2"><div class="ic">★</div><h3>Clear next steps</h3><p>The exact moves to make this week — disputes, utilization, accounts to keep, accounts to leave alone.</p></div>
      <div class="outcome reveal reveal-d3"><div class="ic">$</div><h3>Pre-approval prep</h3><p>If a mortgage or business loan is on the horizon, we sequence the moves so you qualify on time.</p></div>
      <div class="outcome reveal reveal-d4"><div class="ic">▶</div><h3>Honest fit check</h3><p>If full credit repair makes sense, I'll tell you. If you can DIY it, I'll tell you that too.</p></div>
    </div>
  </div>
</section>

<!-- INCLUDED -->
<section class="svc-included">
  <div class="container">
    <div class="included-grid">
      <div class="included-img reveal">
        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=900&q=75" alt="Strategy call on Zoom" width="900" height="600" loading="lazy" decoding="async" />
      </div>
      <div class="included-list reveal reveal-d2">
        <span class="eyebrow">What's included</span>
        <h2>Everything in <em class="serif gradient-text">the 15.</em></h2>
        <p>One focused call — no fluff, no upsell, no card. Just real answers and a real plan.</p>
        <ul>
          <li><span class="ck">✓</span><div><strong>15-minute Zoom with Victoria</strong><small>Direct with me — not a closer, not an assistant.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Live 3-bureau report walkthrough</strong><small>You log into your monitoring service on the call and we audit together.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Custom dispute strategy</strong><small>What to dispute, in what order, and what to leave alone.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Pre-approval roadmap</strong><small>If a mortgage or business funding is next, we sequence it.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Zero pressure to sign</strong><small>If repair fits, we talk plans. If it doesn't, you still leave with the strategy.</small></div></li>
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
          <span class="num"><span>1</span> Qualify</span>
          <span class="day">Today</span>
        </div>
        <div class="step-body"><h3>Tell me about your file</h3><p>60-second qualification form so I show up ready — no time wasted on small talk.</p></div>
      </div>
      <div class="step reveal reveal-d2">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>2</span> Pull live</span>
          <span class="day">On call</span>
        </div>
        <div class="step-body"><h3>Pull &amp; audit live</h3><p>You bring your monitoring login, we open all 3 bureaus together, I rank the wins by score impact.</p></div>
      </div>
      <div class="step reveal reveal-d3">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>3</span> Run the plan</span>
          <span class="day">This week</span>
        </div>
        <div class="step-body"><h3>Walk away with a 90-day plan</h3><p>You execute the moves yourself, or we run full repair together. Either way, you leave knowing what to do.</p></div>
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
        "Fifteen minutes with Victoria saved me from sending the wrong disputes for the next six months. Walked off the call with a written plan and was pre-approved 11 weeks later."
      </div>
      <div class="who">
        <div class="av">AR</div>
        <div>
          <div class="nm">Aliyah R.</div>
          <div class="loc">Austin, TX · Strategy call client</div>
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
        <span class="eyebrow">FAQ · Strategy Call</span>
        <h2>Questions, <em class="serif gradient-text">answered.</em></h2>
        <p>What people want to know before they pick a time.</p>
        <div class="ctas">
          <a href="{{ route('strategy-call.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
        </div>
      </div>
      <div class="faq-list reveal reveal-d2">
        <div class="faq-item"><div class="faq-q">Is the call really free? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Yes — no card, no charge, no pitch. The qualification form filters tire-kickers so when we get on Zoom I can actually move the needle for you.</div></div></div>
        <div class="faq-item"><div class="faq-q">What should I bring to the call? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Your credit monitoring login (IdentityIQ, SmartCredit, MyScoreIQ, MyFICO, etc.) ready to go on your end. Without it we can't pull your real reports — and the call gets rescheduled.</div></div></div>
        <div class="faq-item"><div class="faq-q">Will you dispute items for me on the call? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">On the strategy call, no — that's full credit repair, which is a separate program. The plan I hand you is detailed enough that most people execute it solo. If you'd rather we run it for you, we'll talk plans at the end of the call.</div></div></div>
        <div class="faq-item"><div class="faq-q">Is the call virtual? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Yes — Zoom with screen-share so we can both see your bureau reports while we talk.</div></div></div>
        <div class="faq-item"><div class="faq-q">How is this different from full credit repair? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Repair is done-for-you over 90 days. The strategy call is done-with-you in 15 minutes — perfect if you want to run the plan yourself, or just want clarity before signing on to anything.</div></div></div>
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
        <h2>Grab a <em class="serif">free 15 with me.</em></h2>
        <p>Fifteen minutes is usually all it takes to know exactly what move to make next. No card. No pitch. Real plan.</p>
        <div class="ctas">
          <a href="{{ route('strategy-call.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
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
