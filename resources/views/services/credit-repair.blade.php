@extends('layouts.app')

@section('title', 'Credit Repair — Aggressive 3-Bureau Disputes | Victoria Love')
@section('description', 'Aggressive 3-bureau dispute strategy that removes inquiries, collections, charge-offs and late payments. +147 avg score gain. 1,000+ clients. Free 15-min strategy call.')

@section('content')

<!-- HERO -->
<section class="svc-hero">
  <div class="container">
    <div class="svc-hero-grid">
      <div class="svc-hero-text reveal">
        <span class="eyebrow">01 · Credit Repair</span>
        <h1>Aggressive disputes that <em class="serif gradient-text">actually work.</em></h1>
        <p class="lede">
          Targeted 3-bureau attacks on inquiries, collections, charge-offs and late payments — proven on
          <strong>thousands of files</strong>. Most clients see <strong>+100 points in 90 days</strong>.
        </p>
        <div class="svc-hero-ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
          <a href="{{ url('/') }}#pricing" class="btn btn-ghost">See pricing</a>
        </div>

        <div class="svc-hero-stats">
          <div class="stt"><strong>+147 pts</strong><span>Avg score gain</span></div>
          <div class="stt"><strong>90 days</strong><span>Typical timeline</span></div>
          <div class="stt"><strong>1,000+</strong><span>Files repaired</span></div>
        </div>
      </div>

      <div class="svc-hero-img reveal reveal-d2">
        <span class="tag">Score · Fund · Own</span>
        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=75" alt="Credit repair dashboard" width="900" height="1125" fetchpriority="high" decoding="async" />
        <div class="fly-card">
          <div class="icn">↑</div>
          <div>
            <div class="lab">Score gain</div>
            <div class="val">+147 pts in 90 days</div>
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
      <h2>Four wins. <em class="serif gradient-text">One clean file.</em></h2>
      <p>Every step of the dispute process is built around lender-ready outcomes — not vanity metrics.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><div class="ic">↑</div><h3>Higher score</h3><p>Average +100–180 points across the 3 bureaus inside 90 days.</p></div>
      <div class="outcome reveal reveal-d2"><div class="ic">✕</div><h3>Items removed</h3><p>Inquiries, collections, charge-offs and lates — gone for good.</p></div>
      <div class="outcome reveal reveal-d3"><div class="ic">$</div><h3>Lender-ready</h3><p>A file mortgage and business lenders actually approve.</p></div>
      <div class="outcome reveal reveal-d4"><div class="ic">∞</div><h3>Lifetime habits</h3><p>Utilization, mix, age — the moves that keep your score up forever.</p></div>
    </div>
  </div>
</section>

<!-- INCLUDED -->
<section class="svc-included">
  <div class="container">
    <div class="included-grid">
      <div class="included-img reveal">
        <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=75" alt="Dispute letters and credit reports" width="900" height="600" loading="lazy" decoding="async" />
      </div>
      <div class="included-list reveal reveal-d2">
        <span class="eyebrow">What's included</span>
        <h2>Everything we do <em class="serif gradient-text">for you.</em></h2>
        <p>This isn't a template service. Every letter, every round, every escalation is calibrated to your file.</p>
        <ul>
          <li><span class="ck">✓</span><div><strong>3-bureau dispute filing</strong><small>Equifax, Experian, TransUnion — every round, every cycle.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Inquiry &amp; charge-off removal</strong><small>Targeted attacks on derogatories that are dragging your score.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Personalized dispute letters</strong><small>FCRA-grounded — not the recycled template kit you'll find online.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Monthly monitoring &amp; updates</strong><small>You always know what's working and what's queued for the next round.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Lifetime credit guidance</strong><small>Utilization, mix, payment cadence — the rules that keep your score there.</small></div></li>
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
      <h2>Three phases. <em class="serif gradient-text">Ninety days.</em></h2>
      <p>Same playbook every time — only the file changes.</p>
    </div>
    <div class="process-grid cols-3">
      <div class="step reveal">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>1</span> Audit</span>
          <span class="day">Week 1</span>
        </div>
        <div class="step-body"><h3>Pull &amp; audit your file</h3><p>3-bureau pull, line-by-line audit, every dispute candidate flagged and prioritized.</p></div>
      </div>
      <div class="step reveal reveal-d2">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>2</span> Dispute</span>
          <span class="day">Weeks 2–10</span>
        </div>
        <div class="step-body"><h3>Round-by-round disputes</h3><p>Personalized letters across all three bureaus. Re-attack escalations on anything that doesn't fall.</p></div>
      </div>
      <div class="step reveal reveal-d3">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>3</span> Track &amp; lock in</span>
          <span class="day">Weeks 11–13</span>
        </div>
        <div class="step-body"><h3>Track &amp; lock in the gains</h3><p>Monthly score reports, utilization plan, and the moves that keep your score climbing — for life.</p></div>
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
        "Started at 540, sat at 712 ninety days later. Got pre-approved for our first home the same week we closed the file. Victoria didn't sell hope — she sent receipts."
      </div>
      <div class="who">
        <div class="av">JM</div>
        <div>
          <div class="nm">Jasmine M.</div>
          <div class="loc">Houston, TX · Credit Repair client</div>
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
        <span class="eyebrow">FAQ · Credit Repair</span>
        <h2>Questions, <em class="serif gradient-text">answered.</em></h2>
        <p>The questions clients actually ask before they sign on.</p>
        <div class="ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book free strategy call <span class="arr">→</span></a>
        </div>
      </div>
      <div class="faq-list reveal reveal-d2">
        <div class="faq-item"><div class="faq-q">How long does credit repair take? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Most clients see meaningful gains within 30 days, with full results landing inside 90 days. Heavy files can take longer — we'll be honest about your timeline on the audit call.</div></div></div>
        <div class="faq-item"><div class="faq-q">What if removed items come back? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Re-insertions get re-attacked — at no extra cost while you're a client. Furnishers have to follow FCRA rules; we hold them to it.</div></div></div>
        <div class="faq-item"><div class="faq-q">Will my score dip during the process? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Sometimes briefly while accounts update — but the trend line is up. We coach you on utilization moves that protect the score during the process.</div></div></div>
        <div class="faq-item"><div class="faq-q">Do you guarantee specific items will be removed? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">No legitimate company can. What I guarantee is the work — every dispute, every escalation, every cycle. The track record speaks: 1,000+ files, +147 avg gain.</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="svc-cta">
  <div class="container">
    <div class="cta-card reveal">
      <div class="cta-text">
        <span class="eyebrow">Ready when you are</span>
        <h2>Let's clean the file <em class="serif">this quarter.</em></h2>
        <p>Free 15-min call. I'll pull the file with you, point at the wins, and tell you exactly what 90 days looks like.</p>
        <div class="ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
          <a href="{{ url('/') }}#pricing" class="btn btn-ghost-light">See pricing</a>
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
