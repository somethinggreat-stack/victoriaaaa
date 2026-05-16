@extends('layouts.app')

@section('title', 'Victoria Love — Fix Your Credit. Own Your Home. Build Wealth.')
@section('description', 'Texas Realtor & Credit Coach. Raise your score 100+ points, unlock $100K in funding, and close on your first home in 90 days. Free 15-min strategy call.')

@section('content')
<!-- ============ HERO ============ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-text">
      <h1>
        Fix your credit.<br/>
        Own your home.<br/>
        Build <em class="serif gradient-text">real wealth.</em>
      </h1>
      <p class="lede">
        I'm Victoria — a Texas Realtor &amp; Credit Coach. In <strong>90 days</strong> I'll raise your score, unlock funding, and walk you to closing. <strong>1,000+ clients</strong> already did it.
      </p>
      <div class="hero-ctas">
        <a href="{{ route('contact.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
        <a href="#pricing" class="btn btn-ghost">Get started</a>
      </div>
      <div class="hero-meta">
        <div class="avs">
          <img src="{{ asset('images/founderimage3.jpeg') }}" alt="" width="38" height="38" decoding="async" />
          <img src="{{ asset('images/founderimage7.jpeg') }}" alt="" width="38" height="38" decoding="async" />
          <img src="{{ asset('images/founderimage4.jpeg') }}" alt="" width="38" height="38" decoding="async" />
        </div>
        <div class="avs-text">
          <div class="stars">★★★★★</div>
          <div><strong>1,000+ wins</strong> · 200+ Texas closings</div>
        </div>
      </div>
    </div>

    <div class="hero-stage reveal">
      <div class="hero-portrait">
        <span class=></span>
        <img src="{{ asset('images/founderimage7.jpeg') }}" alt="Victoria Love — Texas Realtor &amp; Credit Coach" width="520" height="650" fetchpriority="high" decoding="async" />
      </div>
    </div>
  </div>
</section>

<!-- ============ PAIN POINTS ============ -->
<section class="pain-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Sound familiar?</span>
      <h2>You're facing one of these. <em class="serif gradient-text">That's why you're here.</em></h2>
      <p>Eight things quietly wreck American credit scores. If even one is sitting on your report, you already know how it feels — and every single one of them <strong style="color:var(--ink)">can be challenged.</strong></p>
    </div>

    <div class="pain-grid">
      <div class="pain reveal">
        <div class="pain-ico">⏰</div>
        <h3>Late Payments</h3>
        <p>One missed due date can cost you 80–110 points and stay visible for seven years. We dispute every entry that isn't backed by airtight proof.</p>
        
      </div>

      <div class="pain reveal reveal-d2">
        <div class="pain-ico">🗂️</div>
        <h3>Collections</h3>
        <p>Old debts get sold, resold, and keep haunting your file long after you've moved on. We force the agencies to verify — most can't.</p>
   
      </div>

      <div class="pain reveal reveal-d3">
        <div class="pain-ico">💳</div>
        <h3>Charge-Offs</h3>
        <p>The lender already wrote it off as a loss — but your report still treats it like a live wound. It doesn't have to stay there.</p>
      
      </div>

      <div class="pain reveal reveal-d4">
        <div class="pain-ico">🚗</div>
        <h3>Repossessions</h3>
        <p>A car taken back can lock you out of auto loans for years. The paperwork behind a repo is rarely airtight — that's where we attack.</p>
       
      </div>

      <div class="pain reveal">
        <div class="pain-ico">⚖️</div>
        <h3>Bankruptcy</h3>
        <p>Chapter 7 or 13 isn't a 10-year sentence. Public-record reporting must be perfect — and most filings have errors we can use to push back.</p>
     
      </div>

      <div class="pain reveal reveal-d2">
        <div class="pain-ico">🎓</div>
        <h3>Student Loans</h3>
        <p>Defaults, mismatched balances, transfer errors, and outdated late marks from old federal or private loans — far more removable than you think.</p>
     
      </div>

      <div class="pain reveal reveal-d3">
        <div class="pain-ico">👶</div>
        <h3>Child Support</h3>
        <p>Wrong amounts, outdated balances, or already-paid obligations still showing on your file. We make sure your report reflects the truth — not the past.</p>
        
      </div>

      <div class="pain reveal reveal-d4">
        <div class="pain-ico">🏥</div>
        <h3>Medical Bills</h3>
        <p>New federal rules wiped most paid medical collections off reports — and balances under $500 must be removed. We make sure they actually are.</p>
    
      </div>
    </div>

    <div class="pain-foot reveal">
      <strong>Recognize even one of these?</strong> You're already in the right place. <a href="{{ route('contact.show') }}" style="color:var(--pink); font-weight:600; text-decoration:underline; text-underline-offset:3px">Book your free 15-min call →</a>
    </div>
  </div>
</section>




<!-- ============ ABOUT ============ -->
<section id="about" class="about-section">
  <div class="container about-grid">
    <div class="about-portrait reveal">
      <img src="{{ asset('images/founderimage1.jpeg') }}" alt="Victoria Love" loading="lazy" decoding="async" />
      <div class="badge">
        <div class="lhs">
          <div class="nm">Victoria Puente</div>
          <div class="ttl">Realtor · Credit Coach · Founder</div>
        </div>
        <div style="text-align:right">
          <div class="stars">★★★★★</div>
          <div class="num">1,000+ clients</div>
        </div>
      </div>
    </div>

    <div class="about-text reveal reveal-d2">
      <span class="eyebrow">Meet Victoria</span>
      <h2>I don't just fix credit. <em class="serif gradient-text"><br> I change lives.</em></h2>
      <p>I'm Victoria Love — a Texas Realtor, Credit Coach, and entrepreneur. I help individuals and families fix their credit, become homeowners, build wealth, and create financial freedom.</p>
      <p>I was once working a 9-to-5 at a doctor's office, knowing I wanted more out of life. So I invested in myself, found mentors, and fixed my own credit in 2021. Then I realized — this simple skill could help everyone around me who was struggling like I was.</p>
      <p>Today, I've helped <strong style="color:var(--ink)">1,000+ people across the U.S.</strong> raise their score and put themselves in position to win.</p>

      <ul class="about-bullets">
        <li><span class="ck">✓</span> Licensed Texas Realtor</li>
        <li><span class="ck">✓</span> Credit Coach since 2021</li>
        <li><span class="ck">✓</span> Founder · Victorious Opportunities</li>
        <li><span class="ck">✓</span> 1,000+ clients · 200+ TX closings</li>
      </ul>

      <div class="signature">— Victoria.</div>

      <div class="about-cta">
        <a href="{{ route('contact.show') }}" class="btn btn-primary">Work with me <span class="arr">→</span></a>
        <a href="#results" class="btn btn-ghost">Read client stories</a>
      </div>
    </div>
  </div>
</section>


<!-- ============ TESTIMONIALS / RESULTS ============ -->
<section id="results" class="tests-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Real results</span>
      <h2>The proof is in the <em class="serif gradient-text">scores.</em></h2>
      <p>1,000+ clients. 200+ Texas closings. Real people, real wins.</p>
    </div>

    <div class="tests-image-grid">
      @for ($i = 1; $i <= 10; $i++)
        <button type="button"
                class="test-card reveal {{ $i % 5 === 2 ? 'reveal-d2' : ($i % 5 === 3 ? 'reveal-d3' : ($i % 5 === 4 ? 'reveal-d4' : '')) }}"
                data-src="{{ asset('images/testimonial' . $i . '.jpg') }}"
                aria-label="Open client testimonial #{{ $i }}">
          <span class="badge">Verified</span>
          <img src="{{ asset('images/testimonial' . $i . '.jpg') }}" alt="Client testimonial #{{ $i }}" loading="lazy" />
          <span class="zoom" aria-hidden="true">⤢</span>
        </button>
      @endfor
    </div>

    <!-- Score-report proof — 4×2 desktop, swipe carousel on mobile -->
    <div class="scores-block">
      <div class="scores-head reveal">
        <span class="eyebrow">Score proof</span>
        <h3>Real reports. <em class="serif gradient-text">Real point gains.</em></h3>
        <p class="scores-sub">Actual before/after credit reports from clients in the program.</p>
      </div>

      @php
        $scoreGains = [
          ['pts' => 136, 'bureau' => 'EQ'],
          ['pts' => 112, 'bureau' => 'EX'],
          ['pts' => 225, 'bureau' => 'EQ'],
          ['pts' => 154, 'bureau' => 'EQ'],
          ['pts' => 187, 'bureau' => 'TU'],
          ['pts' =>  98, 'bureau' => 'EX'],
          ['pts' => 142, 'bureau' => 'EQ'],
          ['pts' => 173, 'bureau' => 'TU'],
        ];
      @endphp
      <div class="scores-grid" id="scoresGrid">
        @for ($i = 1; $i <= 8; $i++)
          <button type="button"
                  class="score-card reveal {{ $i % 4 === 2 ? 'reveal-d2' : ($i % 4 === 3 ? 'reveal-d3' : ($i % 4 === 0 ? 'reveal-d4' : '')) }}"
                  data-src="{{ asset('images/scoreimage' . $i . '.png') }}"
                  aria-label="Open score report #{{ $i }}">
            <div class="score-frame">
              <img src="{{ asset('images/scoreimage' . $i . '.png') }}" alt="Client credit score report #{{ $i }}" loading="lazy" />
            </div>
            <div class="score-foot">
              <span class="score-client">CLIENT #{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</span>
              <span class="score-gain">+{{ $scoreGains[$i-1]['pts'] }} {{ $scoreGains[$i-1]['bureau'] }}</span>
            </div>
          </button>
        @endfor
      </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Testimonial image">
      <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Close">×</button>
      <button type="button" class="lightbox-nav prev" id="lightboxPrev" aria-label="Previous">‹</button>
      <button type="button" class="lightbox-nav next" id="lightboxNext" aria-label="Next">›</button>
      <img id="lightboxImg" src="" alt="" />
    </div>
  </div>
</section>

<!-- ============ PRICING ============ -->
<section id="pricing" class="pricing-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Choose your plan</span>
      <h2>Simple pricing. <em class="serif gradient-text">Real outcomes.</em></h2>
      <p>Five ways in. Same destination — a score that opens every door. Free 15-min consult included with every plan.</p>
    </div>

    <div class="pricing-grid pricing-grid-5">
      <!-- Card 1 · Audit (entry) -->
      <div class="price reveal">
        <div class="name">Audit</div>
        <div class="amt">$97</div>
        <div class="strike">single session</div>
        <p class="desc">One focused 1:1 audit call. Walk away with a custom plan you can run this week.</p>
        <ul>
          <li>60-min call with Victoria</li>
          <li>Full 3-bureau report audit</li>
          <li>Custom dispute plan</li>
          <li>Pre-approval prep</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'audit']) }}" class="btn btn-ghost">Book my audit</a>
      </div>

      <!-- Card 2 · Monthly -->
      <div class="price reveal reveal-d2">
        <div class="name">Monthly</div>
        <div class="amt">$197 <span class="p">+ $100/mo</span></div>
        <div class="strike">was $297</div>
        <p class="desc">Full 90-day credit transformation. Cancel after 90.</p>
        <ul>
          <li>Full 90-day credit plan</li>
          <li>Aggressive 3-bureau disputes</li>
          <li>Monthly progress updates</li>
          <li>Cancel after 90 days</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'monthly']) }}" class="btn btn-ghost">Get started</a>
      </div>

      <!-- Card 3 · One-time (FEATURED) -->
      <div class="price feat reveal reveal-d3">
        <span class="price-tag">Most Popular</span>
        <div class="name">One-time</div>
        <div class="amt">$497</div>
        <div class="strike">save $197</div>
        <p class="desc">Single payment. Priority dispute filing + ongoing support.</p>
        <ul>
          <li>One-time, zero recurring</li>
          <li>Priority dispute filing</li>
          <li>Results in 30–45 days</li>
          <li>Ongoing support</li>
          <li>Lifetime credit guidance</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'onetime']) }}" class="btn btn-pink">Pay once, done <span class="arr">→</span></a>
      </div>

      <!-- Card 4 · Couple -->
      <div class="price reveal reveal-d4">
        <div class="name">Couple</div>
        <div class="amt">$900</div>
        <div class="strike">2 plans, 1 price</div>
        <p class="desc">For two. Coordinated attack so you and your partner buy together.</p>
        <ul>
          <li>Program for both partners</li>
          <li>Dual credit restoration</li>
          <li>Coordinated bureau attacks</li>
          <li>Joint funding prep</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'couple']) }}" class="btn btn-ghost">Apply as couple</a>
      </div>

      <!-- Card 5 · VIP (premium) -->
      <div class="price reveal">
        <span class="price-tag price-tag-gold">★ VIP</span>
        <div class="name">VIP</div>
        <div class="amt">$1,997</div>
        <div class="strike">white-glove</div>
        <p class="desc">Done-with-you. Priority everything. Direct text line to Victoria.</p>
        <ul>
          <li>Priority dispute filing</li>
          <li>Direct text line to Victoria</li>
          <li>Weekly progress calls</li>
          <li>Lender + funding intros</li>
          <li>Lifetime credit guidance</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'vip']) }}" class="btn btn-ghost">Apply for VIP</a>
      </div>
    </div>

    <div class="price-meta reveal">
      <strong>Not sure?</strong> Book a free 15-min call — I'll point you to the right plan, no pressure.
    </div>
  </div>
</section>



<!-- ============ EBOOKS LIBRARY ============ -->
<section id="ebooks" class="ebooks-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Digital library</span>
      <h2>Take a <em class="serif gradient-text">shortcut.</em></h2>
      <p>Self-paced playbooks built from real client wins.</p>
    </div>

    <div class="ebooks-grid">
      <div class="ebook ebook-1 reveal">
        <div class="ebook-cover">
          <img src="{{ asset('images/100kinfundingebookcover.png') }}" alt="Easy Steps to Get $100K+ in 90 Days — ebook cover" loading="lazy" />
        </div>
        <h4>7 Easy Steps to Get $100K+ in 90 Days</h4>
        <div class="meta">
          <div class="ep">$47<small>.00</small></div>
          <a href="{{ route('ebooks.checkout', '100k-funding-in-90-days') }}" class="buy">Get it →</a>
        </div>
      </div>

      <div class="ebook ebook-2 reveal reveal-d2">
        <div class="ebook-cover">
          <img src="{{ asset('images/hardinquiriesebookcover.png') }}" alt="How to Get Hard Inquiries Gone in One Day — ebook cover" loading="lazy" />
        </div>
        <h4>Get Hard Inquiries Gone</h4>
        <div class="meta">
          <div class="ep">$7<small>.47</small></div>
          <a href="{{ route('ebooks.checkout', 'hard-inquiries-gone') }}" class="buy">Get it →</a>
        </div>
      </div>

      <div class="ebook ebook-3 reveal reveal-d3">
        <div class="ebook-cover">
          <img src="{{ asset('images/realestatetermscheatsheetebookcover.png') }}" alt="Real Estate Terms Exam Cheat Sheet — ebook cover" loading="lazy" />
        </div>
        <h4>Real Estate Terms Exam Cheats</h4>
        <div class="meta">
          <div class="ep">$19<small>.47</small></div>
          <a href="{{ route('ebooks.checkout', 'real-estate-terms-cheat-sheet') }}" class="buy">Get it →</a>
        </div>
      </div>

      <div class="ebook ebook-4 reveal reveal-d4">
        <div class="ebook-cover">
          <img src="{{ asset('images/realtorroadmaptosuccessebookcover.png') }}" alt="The Realtor Roadmap to Success — ebook cover" loading="lazy" />
        </div>
        <h4>The Realtor Roadmap to Success</h4>
        <div class="meta">
          <div class="ep">$24<small>.47</small></div>
          <a href="{{ route('ebooks.checkout', 'realtor-roadmap-to-success') }}" class="buy">Get it →</a>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- ============ AUTHORITY · LUXURY CREDIT BRAND ============ -->
<section class="authority">
  <div class="container">
    <div class="auth-grid">

      <!-- LEFT — message, proof, trust, CTA -->
      <div class="auth-text reveal">
        <span class="eyebrow">Luxury credit · Real results</span>
        <h2>Building real wealth <em class="serif gradient-text">starts with better credit.</em></h2>
        <p class="lede">Premium credit repair, business funding, and homeownership coaching for women ready to level up — without giving up the lifestyle they've already built.</p>

        <div class="auth-meta">
          <div class="meta-item">
            <span class="meta-num">1,000+</span>
            <span class="meta-lab">Clients served nationwide</span>
          </div>
          <div class="meta-item">
            <span class="meta-num">$127K</span>
            <span class="meta-lab">Avg funding approved</span>
          </div>
          <div class="meta-item">
            <span class="meta-num">5.0★</span>
            <span class="meta-lab">Avg client review</span>
          </div>
          <div class="meta-item">
            <span class="meta-num">4+ yrs</span>
            <span class="meta-lab">Founder-led coaching</span>
          </div>
        </div>

        <ul class="auth-trust">
          <li><span class="ck">✓</span> CFPB-compliant dispute process</li>
          <li><span class="ck">✓</span> Trusted by women in all 50 states</li>
          <li><span class="ck">✓</span> Texas-licensed Realtor &amp; Coach</li>
          <li><span class="ck">✓</span> Free 15-min strategy call</li>
        </ul>



        <div class="auth-ctas">
          <a href="#contact" class="btn btn-pink">Start your credit review <span class="arr">→</span></a>
          <a href="{{ route('contact.show') }}" class="btn btn-ghost">Book free consultation</a>
        </div>
      </div>

      <!-- RIGHT — proof mosaic with financial captions -->
      <div class="auth-mosaic reveal reveal-d2">
        <div class="auth-tile m1">
          <img src="{{ asset('images/newimageforluxurycreditsection1.jpeg') }}" alt="Client celebrating credit transformation" loading="lazy" />
          <div class="m-cap">
            <span class="m-tag">Real Result</span>
            <span class="m-ttl">From 520 → 720.</span>
            <span class="m-sub">In just 12 weeks.</span>
          </div>
        </div>
        <div class="auth-tile m2">
          <img src="{{ asset('images/newimageforluxurycreditsection2.jpeg') }}" alt="Funding approval" loading="lazy" />
          <div class="m-cap">
            <span class="m-tag">Approved</span>
            <span class="m-ttl">$127K funding secured.</span>
          </div>
        </div>
        <div class="auth-tile m3">
          <img src="{{ asset('images/newimageforluxurycreditsection3.jpeg') }}" alt="Negative items removed" loading="lazy" />
          <div class="m-cap">
            <span class="m-tag">Removed</span>
            <span class="m-ttl">120+ items deleted.</span>
          </div>
        </div>
        <div class="auth-tile m4">
          <img src="{{ asset('images/newimageforluxurycreditsection4.jpeg') }}" alt="Closing day approval" loading="lazy" />
          <div class="m-cap">
            <span class="m-tag">Closing Day</span>
            <span class="m-ttl">Keys in her hand.</span>
          </div>
        </div>

        <div class="auth-floater">
          <div class="fl-stars">★★★★★</div>
          <div class="fl-quote">"From denied to approved in 90 days."</div>
          <div class="fl-by">— Verified client review</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section id="faq" class="faq-section">
  <div class="container faq-wrap">
    <div class="faq-side reveal">
      <span class="eyebrow">Frequently asked</span>
      <h2>Got <em class="serif gradient-text">questions?</em></h2>
      <p>Everything you need to know about working with me. Don't see your question? Just ask on the call.</p>
      <div class="ctas">
        <a href="{{ route('contact.show') }}" class="btn btn-primary">Book free call <span class="arr">→</span></a>
        <a href="{{ route('contact.show') }}" class="btn btn-ghost">Ask a question</a>
      </div>
    </div>

    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-q">How fast will I see score changes? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">Most clients see real movement inside <strong style="color:var(--ink)">30–45 days</strong>. Aggressive plans can deliver +100 points by day 90. Results vary by file, but I'll show you exactly what's possible on our free call.</div></div>
      </div>

      <div class="faq-item">
        <div class="faq-q">Do you only work with people in Texas? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">Credit work is done <strong style="color:var(--ink)">nationwide</strong> — I have clients in all 50 states. Real estate brokerage is Texas-only because that's where I'm licensed (Houston, Dallas, Austin, San Antonio, Fort Worth, El Paso, and more).</div></div>
      </div>

      <div class="faq-item">
        <div class="faq-q">What if I have collections, charge-offs, or bankruptcy? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">I work with all of them. The harder the file, the more I lean in. We'll dispute, negotiate, and remove what's holding you back. Bankruptcy clients have closed on homes — it's possible.</div></div>
      </div>

      <div class="faq-item">
        <div class="faq-q">Can my partner and I do this together? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">Yes — that's the <strong style="color:var(--ink)">Couple plan ($900)</strong>. Dual coordinated repair so you both qualify for the same loan and close on your forever home together.</div></div>
      </div>

      <div class="faq-item">
        <div class="faq-q">Is the free 15-min call really free? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">100%. No card, no fluff, no "secret upsell." We pull your report, look at the wins, and you leave with a clear next step — even if you never hire me.</div></div>
      </div>

      <div class="faq-item">
        <div class="faq-q">Do you guarantee results? <span class="icon">+</span></div>
        <div class="faq-a"><div class="faq-a-inner">No credit company can legally guarantee specific score points. What I <em>can</em> guarantee: I work your file as if it were my own, and you get lifetime credit guidance even after we wrap.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section id="contact" class="cta-section">
  <div class="container">
    <div class="cta-card reveal">
      <div class="cta-text">
        <span class="eyebrow">Choose your plan</span>
        <h2>Find the plan that <em class="serif">fits your goal.</em></h2>
        <p>Simple monthly pricing built around real credit transformation — pick the plan that gets you to your dream score, your dream home, your next chapter.</p>
        <div class="ctas">
          <a href="#pricing" class="btn btn-pink">View pricing <span class="arr">→</span></a>
          <a href="{{ route('contact.show') }}" class="btn btn-ghost-light">Book free call</a>
        </div>
        <div class="stamp">
          <img src="{{ asset('images/founderimage4.jpeg') }}" alt="Victoria Love" width="48" height="48" loading="lazy" decoding="async" />
          <div>
            <div class="nm">— Victoria Love</div>
            <div class="ttl">Founder · Victorious Opportunities</div>
          </div>
        </div>
      </div>
      <div class="cta-image">
        <img src="{{ asset('images/founderimage6.jpeg') }}" alt="Victoria Love" loading="lazy" decoding="async" />
      </div>
    </div>
  </div>
</section>

<!-- ============ MULTI-STEP LEAD POPUP ============ -->
<div class="lead-popup" id="leadPopup" role="dialog" aria-modal="true" aria-labelledby="leadPopupTitle">
  <div class="lead-card">
    <button type="button" class="lead-close" id="leadClose" aria-label="Close">×</button>

    <!-- Branded header -->
    <div class="lead-head">
      <div class="lead-avatar">
        <img src="{{ asset('images/founderimage7.jpeg') }}" alt="Victoria Love" width="40" height="40" loading="lazy" decoding="async" />
        <span class="online-dot"></span>
      </div>
      <div class="lead-greet">
        <div class="lead-name">Victoria Love</div>
        <div class="lead-role">Credit Coach · Founder</div>
      </div>
    </div>

    <!-- Progress dots -->
    <div class="lead-progress">
      <div class="lp-dot active"></div>
      <div class="lp-dot"></div>
      <div class="lp-dot"></div>
      <div class="lp-dot"></div>
    </div>

    <form id="leadForm" novalidate>
      <!-- STEP 1 — Credit Score -->
      <div class="lead-step active" data-step="1">
        <span class="lead-eyebrow">Step 1 of 4 · 30 seconds</span>
        <h3 id="leadPopupTitle">What's your current credit score range?</h3>
        <p>Be honest — it helps me match you to the right plan.</p>
        <div class="lead-options">
          <label><input type="radio" name="score" value="below-500" required /><span>Below 500 — major reset needed</span></label>
          <label><input type="radio" name="score" value="500-579" /><span>500–579 — Poor</span></label>
          <label><input type="radio" name="score" value="580-669" /><span>580–669 — Fair</span></label>
          <label><input type="radio" name="score" value="670-739" /><span>670–739 — Good, want better</span></label>
          <label><input type="radio" name="score" value="740-plus" /><span>740+ — push higher</span></label>
          <label><input type="radio" name="score" value="unknown" /><span>Not sure — haven't checked</span></label>
        </div>
      </div>

      <!-- STEP 2 — Biggest Issue -->
      <div class="lead-step" data-step="2">
        <span class="lead-eyebrow">Step 2 of 4</span>
        <h3>What's holding your credit back the most?</h3>
        <p>Pick the biggest culprit. We attack it first.</p>
        <div class="lead-options">
          <label><input type="radio" name="issue" value="collections" required /><span>🗂️ Collections</span></label>
          <label><input type="radio" name="issue" value="late-payments" /><span>⏰ Late payments</span></label>
          <label><input type="radio" name="issue" value="charge-offs" /><span>💳 Charge-offs</span></label>
          <label><input type="radio" name="issue" value="inquiries" /><span>🎯 Too many hard inquiries</span></label>
          <label><input type="radio" name="issue" value="bankruptcy" /><span>⚖️ Bankruptcy on file</span></label>
          <label><input type="radio" name="issue" value="multiple" /><span>📋 Multiple — all of the above</span></label>
        </div>
      </div>

      <!-- STEP 3 — Goal -->
      <div class="lead-step" data-step="3">
        <span class="lead-eyebrow">Step 3 of 4</span>
        <h3>What's your #1 goal in the next 90 days?</h3>
        <p>One target. Your plan gets built around it.</p>
        <div class="lead-options">
          <label><input type="radio" name="goal" value="home" required /><span>🏠 Buy my first home</span></label>
          <label><input type="radio" name="goal" value="funding" /><span>💼 Unlock $100K+ in business funding</span></label>
          <label><input type="radio" name="goal" value="cleanup" /><span>✨ Clean up my credit file fast</span></label>
          <label><input type="radio" name="goal" value="all" /><span>📈 All of the above</span></label>
        </div>
      </div>

      <!-- STEP 4 — Contact -->
      <div class="lead-step" data-step="4">
        <span class="lead-eyebrow">Final step · Almost done</span>
        <h3>Where should I send your free 90-day plan?</h3>
        <p>I'll personally review your answers and reach out within 24 hours.</p>
        <div class="lead-fields">
          <label class="lead-field">
            <span>Full name</span>
            <input type="text" name="name" required placeholder="Your name" autocomplete="name" />
          </label>
          <label class="lead-field">
            <span>Email address</span>
            <input type="email" name="email" required placeholder="you@email.com" autocomplete="email" />
          </label>
          <label class="lead-field">
            <span>Phone number</span>
            <input type="tel" name="phone" required placeholder="(555) 123-4567" autocomplete="tel" />
          </label>
        </div>
      </div>

      <!-- Success state -->
      <div class="lead-step" data-step="success">
        <div class="lead-success-icon">✓</div>
        <h3>You're in. I'll be in touch.</h3>
        <p>I'll personally review your answers and send your custom 90-day plan within 24 hours. Check your inbox &amp; phone.</p>
        <button type="button" class="btn btn-pink" id="leadDoneBtn">Close &amp; keep exploring</button>
      </div>

      <!-- Nav buttons -->
      <div class="lead-nav" id="leadNav">
        <button type="button" class="lead-back" id="leadBack" disabled>← Back</button>
        <button type="button" class="lead-next" id="leadNext" disabled>Next →</button>
        <button type="submit" class="lead-submit" id="leadSubmit" hidden disabled>Send my plan →</button>
      </div>
    </form>

    <div class="lead-trust">
      <span class="lt-stars">★★★★★</span>
      <strong>1,000+ clients served</strong>
      <small>· Your info stays private</small>
    </div>
  </div>
</div>

@endsection
