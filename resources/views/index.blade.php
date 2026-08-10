@extends('layouts.app')

@section('title', 'Victoria Love — Fix Your Credit. Own Your Home. Build Wealth.')
@section('description', 'Texas Realtor & Credit Coach. We remove collections, charge-offs & late payments — real results in as little as 2 weeks. Free 15-min phone consultation.')

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
        I'm Victoria — a Texas Realtor &amp; Credit Coach. I remove the collections, charge-offs, and late payments dragging you down — with real results in as little as <strong>2 weeks</strong>. <strong>1,000+ clients</strong> already did it.
      </p>
      <div class="hero-ctas">
        <a href="#pricing" class="btn btn-pink">See pricing &amp; start <span class="arr">→</span></a>
        <a href="{{ route('strategy-call.show') }}" class="btn btn-ghost">Free 15-min phone call</a>
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
      <span class="eyebrow">What we can remove</span>
      <h2>Everything dragging your score down — <em class="serif gradient-text">we challenge it.</em></h2>
      <p>The items below quietly wreck American credit scores. If even one is sitting on your report, you already know how it feels — and every single one of them <strong style="color:var(--ink)">can be disputed and removed.</strong></p>
    </div>

    <style>
      .remove-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
        max-width: 1040px; margin: 0 auto;
      }
      .remove-pill {
        position: relative; overflow: hidden;
        display: flex; align-items: center; gap: 15px;
        background: linear-gradient(180deg, #ffffff 0%, #fff7fb 100%);
        border: 1px solid var(--line);
        border-radius: 16px; padding: 18px 22px;
        font-weight: 650; font-size: 16px; letter-spacing: -0.005em; color: var(--ink);
        box-shadow: 0 10px 30px -22px rgba(20,16,14,0.28);
        transition: transform .28s var(--ease), box-shadow .28s, border-color .28s;
      }
      /* Brand accent bar that slides in on hover */
      .remove-pill::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        background: var(--grad-warm, linear-gradient(135deg,#e63179,#ff7eb3));
        transform: scaleY(0); transform-origin: bottom;
        transition: transform .3s var(--ease);
      }
      .remove-pill:hover {
        transform: translateY(-4px);
        box-shadow: 0 28px 52px -24px rgba(230,49,121,0.38);
        border-color: rgba(230,49,121,0.28);
      }
      .remove-pill:hover::before { transform: scaleY(1); }
      .remove-pill .rk {
        flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%;
        background: var(--grad-warm, linear-gradient(135deg,#e63179,#ff7eb3));
        color: #fff; display: grid; place-items: center;
        font-size: 14px; font-weight: 800;
        box-shadow: 0 6px 16px -5px rgba(230,49,121,0.7), 0 0 0 5px rgba(230,49,121,0.10);
        transition: transform .28s var(--ease), box-shadow .28s;
      }
      .remove-pill:hover .rk {
        transform: scale(1.08) rotate(-6deg);
        box-shadow: 0 8px 22px -5px rgba(230,49,121,0.85), 0 0 0 6px rgba(230,49,121,0.16);
      }
      @media (max-width: 800px) { .remove-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
      @media (max-width: 460px) { .remove-grid { grid-template-columns: 1fr; } .remove-pill { font-size: 15px; padding: 16px 18px; } }
    </style>

    <div class="remove-grid reveal">
      @foreach ([
        'Foreclosures', 'Collections', 'Charge-Offs',
        'Student Loans', 'Judgments', 'Medical Bills',
        'Late Payments', 'Repossessions', 'Public Records',
        'Bankruptcies', 'Child Support', 'Hard Inquiries',
      ] as $item)
        <div class="remove-pill"><span class="rk">✓</span>{{ $item }}</div>
      @endforeach
    </div>

    <div class="pain-foot reveal">
      <strong>Recognize even one of these?</strong> You're already in the right place. <a href="{{ route('strategy-call.show') }}" style="color:var(--pink); font-weight:600; text-decoration:underline; text-underline-offset:3px">Book your free 15-min phone call →</a>
    </div>
  </div>
</section>


<!-- ============ PRICING ============ -->
<section id="pricing" class="pricing-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Choose your plan</span>
      <h2>Simple pricing. <em class="serif gradient-text">Real removals.</em></h2>
      <p>Pick your speed — every plan runs aggressive 3-bureau disputes on your file. A free 15-min phone consult is available with any plan, but it's optional. You can start today.</p>
    </div>

    <div class="pricing-grid">
      <!-- Card 1 · Fast Dispute -->
      <div class="price reveal">
        <div class="name">Fast Dispute</div>
        <div class="amt">$297</div>
        <div class="strike">one-time · results in ~2 weeks</div>
        <p class="desc">All-in-one aggressive dispute. First results in as little as two weeks.</p>
        <ul>
          <li>One-time — nothing recurring</li>
          <li>Aggressive all-in-one 3-bureau dispute</li>
          <li>First results in ~2 weeks</li>
          <li>Priority handling on your file</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'fast-dispute']) }}" class="btn btn-ghost">Start fast dispute <span class="arr">→</span></a>
      </div>

      <!-- Card 2 · Monthly (FEATURED) -->
      <div class="price feat reveal reveal-d2">
        <span class="price-tag">Most Popular</span>
        <div class="name">Monthly</div>
        <div class="amt">$197 <span class="p">+ $100/mo</span></div>
        <div class="strike">full 90-day plan</div>
        <p class="desc">Full 90-day credit transformation. Cancel anytime after 90.</p>
        <ul>
          <li>Full 90-day credit plan</li>
          <li>Aggressive 3-bureau disputes</li>
          <li>Monthly progress updates</li>
          <li>Cancel after 90 days</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'monthly']) }}" class="btn btn-pink">Start monthly <span class="arr">→</span></a>
      </div>

      <!-- Card 3 · Unlimited Lifetime -->
      <div class="price reveal reveal-d3">
        <div class="name">Unlimited · Lifetime</div>
        <div class="amt">$597</div>
        <div class="strike">one payment · for life</div>
        <p class="desc">One payment, unlimited dispute rounds for life. Best long-term value.</p>
        <ul>
          <li>One-time $597 — lifetime access</li>
          <li>Unlimited dispute rounds</li>
          <li>Every negative item, every bureau</li>
          <li>Priority support for life</li>
        </ul>
        <a href="{{ route('checkout.show', ['plan' => 'unlimited']) }}" class="btn btn-ghost">Go unlimited <span class="arr">→</span></a>
      </div>
    </div>

    <div class="price-meta reveal">
      <strong>Together?</strong> Two people, unlimited lifetime rounds for <strong>$597</strong> total — <a href="{{ route('checkout.show', ['plan' => 'couple']) }}" style="color:var(--pink);font-weight:600;text-decoration:underline;text-underline-offset:3px">start the couples plan →</a>
    </div>
  </div>
</section>


<!-- ============ WHAT HAPPENS AFTER PURCHASE ============ -->
<section class="after-purchase">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">After you purchase</span>
      <h2>What happens <em class="serif gradient-text">next.</em></h2>
      <p>Four quick steps and we're working your file. The faster you finish onboarding, the faster your first results.</p>
    </div>

    <style>
      .after-purchase { padding: 100px 0; }
      .ap-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; max-width: 1140px; margin: 0 auto; }
      .ap-step {
        position: relative; background: #fff; border: 1px solid var(--line);
        border-radius: var(--r-lg, 24px); padding: 30px 26px 28px;
        transition: transform .3s, box-shadow .3s, border-color .3s;
      }
      .ap-step:hover { transform: translateY(-5px); box-shadow: 0 30px 60px -28px rgba(20,16,14,0.18); border-color: var(--line-2); }
      .ap-num {
        width: 46px; height: 46px; border-radius: 14px;
        background: var(--grad-warm, linear-gradient(135deg,#e63179,#ff7eb3));
        color: #fff; display: grid; place-items: center;
        font-size: 20px; font-weight: 700; margin-bottom: 18px;
        box-shadow: 0 12px 24px -10px rgba(230,49,121,0.6);
      }
      .ap-step h3 { font-size: 18px; font-weight: 600; margin: 0 0 8px; letter-spacing: -0.01em; }
      .ap-step p { font-size: 14px; line-height: 1.6; color: var(--ink-2); margin: 0; }
      .ap-foot { text-align: center; margin-top: 40px; font-size: 15px; color: var(--ink-2); }
      .ap-foot a { color: var(--pink); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
      @media (max-width: 1000px) { .ap-grid { grid-template-columns: 1fr 1fr; } }
      @media (max-width: 560px)  { .ap-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="ap-grid">
      <div class="ap-step reveal">
        <div class="ap-num">1</div>
        <h3>Complete your onboarding form</h3>
        <p>Right after checkout you'll fill out a quick, 256-bit encrypted form — your details, date of birth, SSN, and mailing address.</p>
      </div>
      <div class="ap-step reveal reveal-d2">
        <div class="ap-num">2</div>
        <h3>Upload your documents</h3>
        <p>Your driver's license and a proof of address (utility bill, bank statement, or lease). Your Social Security card is optional but helps.</p>
      </div>
      <div class="ap-step reveal reveal-d3">
        <div class="ap-num">3</div>
        <h3>Set up credit monitoring</h3>
        <p>Enroll in <strong>MyFreeScore</strong> (link is right on the form), then add your login so we can pull your live 3-bureau reports.</p>
      </div>
      <div class="ap-step reveal reveal-d4">
        <div class="ap-num">4</div>
        <h3>We go to work</h3>
        <p>We file aggressive disputes across all three bureaus. As results come back — usually in 10–15 days — we verify deletions and launch the next round.</p>
      </div>
    </div>

    <div class="ap-foot reveal">
      You'll get your secure onboarding link <strong>right after checkout</strong> — nothing to do until then.
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
        <a href="{{ route('strategy-call.show') }}" class="btn btn-primary">Work with me <span class="arr">→</span></a>
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
          <li><span class="ck">✓</span> Free 15-min phone consultation</li>
        </ul>



        <div class="auth-ctas">
          <a href="#pricing" class="btn btn-pink">Start your credit repair <span class="arr">→</span></a>
          <a href="{{ route('strategy-call.show') }}" class="btn btn-ghost">Free 15-min phone call</a>
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
        <a href="{{ route('strategy-call.show') }}" class="btn btn-primary">Book free phone call <span class="arr">→</span></a>
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
        <div class="faq-a"><div class="faq-a-inner">Yes — that's the <strong style="color:var(--ink)">Couple plan ($597)</strong>. Two people, unlimited lifetime rounds, coordinated so you both qualify for the same loan and close on your forever home together.</div></div>
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
          <a href="{{ route('strategy-call.show') }}" class="btn btn-ghost-light">Free 15-min phone call</a>
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

@endsection
