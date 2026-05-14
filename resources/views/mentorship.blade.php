@extends('layouts.app')

@section('title', '1:1 Mentorship — Build the Credit Business I Built | Victoria Love')
@section('description', 'Private 1:1 mentorship straight from Victoria. Weekly calls, SOPs, scripts, and a lifetime community — until you book real clients with real confidence.')

@section('content')

<!-- HERO -->
<section class="svc-hero">
  <div class="container">
    <div class="svc-hero-grid">
      <div class="svc-hero-text reveal">
        <span class="eyebrow">★ 1:1 Mentorship · Limited Spots</span>
        <h1>Build the credit business <em class="serif gradient-text">I built.</em></h1>
        <p class="lede">
          Private 1:1 mentorship straight from me. We meet, plan, build — until you book
          <strong>real clients with real confidence</strong> and run a credit business
          that pays you while you sleep.
        </p>
        <div class="svc-hero-ctas">
          <a href="#mentor-pricing" class="btn btn-pink">Apply for mentorship <span class="arr">→</span></a>
          <a href="#discuss" class="btn btn-ghost">Want to discuss first?</a>
        </div>

        <div class="svc-hero-stats">
          <div class="stt"><strong>$997</strong><span>One-time · was $2,497</span></div>
          <div class="stt"><strong>1:1</strong><span>Weekly calls with Victoria</span></div>
          <div class="stt"><strong>Limited</strong><span>Spots open each quarter</span></div>
        </div>
      </div>

      <div class="svc-hero-img reveal reveal-d2">
        <span class="tag">Apply · Plan · Launch</span>
        <img src="{{ asset('images/founderimage5.jpeg') }}" alt="Victoria Love · 1:1 mentor" width="520" height="650" fetchpriority="high" decoding="async" />
        <div class="fly-card">
          <div class="icn">★</div>
          <div>
            <div class="lab">Save $1,500</div>
            <div class="val">$2,497 → $997</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ DISCUSS-FIRST QUALIFICATION FORM ============ -->
<section class="fund-qual" id="discuss">
  <div class="container">
    <div class="fund-card reveal">

      @if (session('mentorship_submitted'))
        <div class="fund-final-success" style="display:block;">
          <div class="ico">✓</div>
          <h3>Application received.</h3>
          <p>Thank you. I'll personally review your answers and reach out within 24 hours about a free fit call.</p>
        </div>
      @else

      <div class="fund-header" id="mentorHeader">
        <span class="fund-badge">💬 Want to discuss first? — Takes 90 seconds</span>
        <h2>Not ready to apply yet? <em class="serif gradient-text">Let's talk.</em></h2>
        <p>Answer 4 quick questions and I'll set up a free 15-min fit call to see if the mentorship is right for you.</p>
      </div>

      <div class="fund-progress" id="mentorProgress">
        <div class="fund-progress-bar"><span></span></div>
        <div class="fund-step-count"><strong id="mentorCurrent">1</strong> / 5</div>
      </div>

      <form id="mentorForm" method="POST" action="{{ route('mentorship.submit') }}" novalidate>
        @csrf

        <!-- Step 1 -->
        <div class="fund-step active" data-step="1">
          <span class="fund-step-tag">Step 1 · Your situation</span>
          <h3>What best describes you right now?</h3>
          <div class="fund-options">
            <label><input type="radio" name="situation" value="Working 9–5 — want out"            required><span><span class="opt-letter">A</span><span>Working 9–5 — and I want out</span></span></label>
            <label><input type="radio" name="situation" value="Already side-hustling in credit"          ><span><span class="opt-letter">B</span><span>Already side-hustling in credit / freelance</span></span></label>
            <label><input type="radio" name="situation" value="Run a small business — adding credit"    ><span><span class="opt-letter">C</span><span>I run a small business — want to add credit-repair</span></span></label>
            <label><input type="radio" name="situation" value="Just exploring"                          ><span><span class="opt-letter">D</span><span>Just exploring — curious what this is</span></span></label>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="fund-step" data-step="2">
          <span class="fund-step-tag">Step 2 · Timeline</span>
          <h3>When do you want to start your credit business?</h3>
          <div class="fund-options">
            <label><input type="radio" name="timeline" value="Immediately — within 30 days" required><span><span class="opt-letter">A</span><span>Immediately — within 30 days</span></span></label>
            <label><input type="radio" name="timeline" value="1–3 months"                          ><span><span class="opt-letter">B</span><span>In the next 1–3 months</span></span></label>
            <label><input type="radio" name="timeline" value="3–6 months"                          ><span><span class="opt-letter">C</span><span>3–6 months from now</span></span></label>
            <label><input type="radio" name="timeline" value="Just exploring"                      ><span><span class="opt-letter">D</span><span>Just exploring — no timeline yet</span></span></label>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="fund-step" data-step="3">
          <span class="fund-step-tag">Step 3 · Time commitment</span>
          <h3>How much time can you commit weekly?</h3>
          <p class="fund-step-sub">Honest answer — this changes how aggressive your plan can be.</p>
          <div class="fund-options">
            <label><input type="radio" name="hours" value="Less than 3 hours" required><span><span class="opt-letter">A</span><span>Less than 3 hours / week</span></span></label>
            <label><input type="radio" name="hours" value="3–7 hours"                ><span><span class="opt-letter">B</span><span>3–7 hours / week</span></span></label>
            <label><input type="radio" name="hours" value="8–15 hours"               ><span><span class="opt-letter">C</span><span>8–15 hours / week</span></span></label>
            <label><input type="radio" name="hours" value="15+ hours"                ><span><span class="opt-letter">D</span><span>15+ hours / week — I'm all in</span></span></label>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="fund-step" data-step="4">
          <span class="fund-step-tag">Step 4 · Investment</span>
          <h3>Are you ready to invest in mentorship?</h3>
          <p class="fund-step-sub">The mentorship is currently <strong>$997</strong> (down from $2,497).</p>
          <div class="fund-options">
            <label><input type="radio" name="investment" value="Yes — ready to invest"      required><span><span class="opt-letter">A</span><span>Yes — ready to invest in myself</span></span></label>
            <label><input type="radio" name="investment" value="Ready, need a payment plan"        ><span><span class="opt-letter">B</span><span>Ready, but I need a payment plan</span></span></label>
            <label><input type="radio" name="investment" value="Not yet — want to learn first"     ><span><span class="opt-letter">C</span><span>Not yet — want to learn more first</span></span></label>
            <label><input type="radio" name="investment" value="Not ready financially"             ><span><span class="opt-letter">D</span><span>Not ready financially</span></span></label>
          </div>
        </div>

        <!-- Step 5 — Contact -->
        <div class="fund-step" data-step="5">
          <span class="fund-step-tag">Final step · Where do I send your reply?</span>
          <div class="fund-success-box">
            <strong>🎉 Last step.</strong>
            <p>Drop your details below and I'll personally reach out within 24 hours about a free 15-min fit call — at zero cost and zero obligation.</p>
          </div>

          <div class="fund-fields">
            <div class="row">
              <label class="fund-field">
                <span class="lab">First Name <em>*</em></span>
                <input type="text" name="first_name" required maxlength="80" placeholder="James" autocomplete="given-name">
              </label>
              <label class="fund-field">
                <span class="lab">Last Name <em>*</em></span>
                <input type="text" name="last_name" required maxlength="80" placeholder="Wilson" autocomplete="family-name">
              </label>
            </div>
            <label class="fund-field">
              <span class="lab">Phone Number <em>*</em></span>
              <div class="ph-wrap">
                <span class="ph-prefix">🇺🇸 +1</span>
                <input type="tel" name="phone" required placeholder="(555) 000-0000" autocomplete="tel">
              </div>
            </label>
            <label class="fund-field">
              <span class="lab">Email Address <em>*</em></span>
              <input type="email" name="email" required maxlength="255" placeholder="james@email.com" autocomplete="email">
            </label>
          </div>
        </div>

        <div class="fund-nav" id="mentorNav">
          <button type="button" class="fund-back" id="mentorBack" hidden>← Back</button>
          <button type="button" class="fund-next" id="mentorNext" disabled>OK / Next <span class="arr">→</span></button>
          <button type="submit" class="fund-submit" id="mentorSubmit" hidden disabled>Submit application <span class="arr">→</span></button>
        </div>
      </form>

      <div class="fund-final-success" id="mentorFinalSuccess" hidden>
        <div class="ico">✓</div>
        <h3>Application received.</h3>
        <p>Thank you. I'll personally review your answers and reach out within 24 hours about a free fit call.</p>
      </div>

      @endif
    </div>
  </div>
</section>

<!-- WHO IT'S FOR -->
<section class="svc-outcomes">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Who this is for</span>
      <h2>Built for the person <em class="serif gradient-text">who's ready to bet on themselves.</em></h2>
      <p>If any of these sound like you, the mentorship is built for you.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><div class="ic">⏰</div><h3>Done with the 9–5</h3><p>You want time freedom and a real income — without trading 40 hours a week for it.</p></div>
      <div class="outcome reveal reveal-d2"><div class="ic">$</div><h3>Want a real business</h3><p>Not a side-hustle. A credit-repair business with a brand, systems, and recurring revenue.</p></div>
      <div class="outcome reveal reveal-d3"><div class="ic">✱</div><h3>Tried a course already</h3><p>You watched the videos but got stuck mid-build. You need a real human coach, not another PDF.</p></div>
      <div class="outcome reveal reveal-d4"><div class="ic">↑</div><h3>Ready to do the work</h3><p>You'll show up to weekly calls, run the plays, and follow the playbook I hand you.</p></div>
    </div>
  </div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="svc-included">
  <div class="container">
    <div class="included-grid">
      <div class="included-img reveal">
        <img src="{{ asset('images/founderimage2.jpeg') }}" alt="Inside the 1:1 mentorship" loading="lazy" decoding="async" />
      </div>
      <div class="included-list reveal reveal-d2">
        <span class="eyebrow">Inside the mentorship</span>
        <h2>Everything I use to <em class="serif gradient-text">run a profitable credit business.</em></h2>
        <p>This is what we'll build together — week by week, on private calls.</p>
        <ul>
          <li><span class="ck">✓</span><div><strong>Private weekly 1:1 calls with me</strong><small>We meet every week. Strategy, accountability, and the exact next move.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Full SOP &amp; script library</strong><small>Client intake, sales calls, dispute scripts, follow-up sequences — every doc I use, given to you.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Software, tools &amp; tech stack</strong><small>The exact CRM, dispute software, payment processor and automations to run your business.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Pricing &amp; offer architecture</strong><small>How to price your packages, what to bundle, and the upsells that double client value.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Client-acquisition playbook</strong><small>The Instagram, TikTok, and referral system that brought me 1,000+ clients without paid ads.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Lifetime community access</strong><small>The same Skool community of credit coaches, founders, and operators that I built.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Lender + funding intros</strong><small>The bankers, brokers and program-vendors I personally connect my mentees to.</small></div></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="process-section" style="background: var(--bg-2); border-block: 1px solid var(--line);">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">How it works</span>
      <h2>Four steps. <em class="serif gradient-text">From applying to booking real clients.</em></h2>
    </div>
    <div class="process-grid">
      <div class="step reveal">
        <div class="step-img" style="background-image: url('{{ asset('images/founderimage1.jpeg') }}'); background-position: center 16%;">
          <span class="num"><span>1</span> Apply</span>
          <span class="day">Day 1</span>
        </div>
        <div class="step-body"><h3>Apply for a spot</h3><p>Fill out the application. If we're a fit, you'll get a personal note from me with the next step.</p></div>
      </div>
      <div class="step reveal reveal-d2">
        <div class="step-img" style="background-image: url('{{ asset('images/founderimage3.jpeg') }}'); background-position: center 18%;">
          <span class="num"><span>2</span> Map</span>
          <span class="day">Week 1</span>
        </div>
        <div class="step-body"><h3>Strategy call &amp; plan</h3><p>We meet 1:1, map your goals, your niche, and the 90-day plan we'll execute together.</p></div>
      </div>
      <div class="step reveal reveal-d3">
        <div class="step-img" style="background-image: url('{{ asset('images/founderimage7.jpeg') }}'); background-position: center 16%;">
          <span class="num"><span>3</span> Build</span>
          <span class="day">Weeks 2–10</span>
        </div>
        <div class="step-body"><h3>Build the business</h3><p>Entity, branding, offer, scripts, processes. Weekly 1:1 calls — every move tracked, refined, executed.</p></div>
      </div>
      <div class="step reveal reveal-d4">
        <div class="step-img" style="background-image: url('{{ asset('images/founderimage4.jpeg') }}'); background-position: center 14%;">
          <span class="num"><span>4</span> Launch</span>
          <span class="day">Weeks 11+</span>
        </div>
        <div class="step-body"><h3>Book real clients</h3><p>You launch with paying clients, real proof, and a business that runs while you sleep.</p></div>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section id="mentor-pricing" class="pricing-section" style="background: var(--bg);">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">One offer · one price</span>
      <h2>Mentorship pricing. <em class="serif gradient-text">Save $1,500 right now.</em></h2>
      <p>One investment. Lifetime tools, lifetime community, lifetime support.</p>
    </div>

    <div class="mentor-price reveal">
      <div class="mentor-price-tag">★ Limited spots · Save $1,500</div>
      <div class="mentor-price-amt">
        <span class="strike">$2,497</span>
        <span class="now">$997</span>
        <small>one-time</small>
      </div>
      <p class="mentor-price-desc">Private 1:1 mentorship straight from Victoria. Every system, script, and conversation it takes to launch a profitable credit business.</p>

      <ul class="mentor-price-list">
        <li><span class="ck">✓</span> Private 1:1 weekly calls with Victoria</li>
        <li><span class="ck">✓</span> Full SOP &amp; client-template library</li>
        <li><span class="ck">✓</span> Sales scripts that close calls</li>
        <li><span class="ck">✓</span> Software, CRM, and dispute tech stack</li>
        <li><span class="ck">✓</span> Client-acquisition playbook (organic + referral)</li>
        <li><span class="ck">✓</span> Lifetime Skool community access</li>
        <li><span class="ck">✓</span> Lender + funding intros</li>
        <li><span class="ck">✓</span> Lifetime updates as the business evolves</li>
      </ul>

      <a href="{{ route('contact.show') }}" class="btn btn-pink-glow mentor-price-btn">Apply for mentorship <span class="arr">→</span></a>
      <p class="mentor-price-fine">Application required · We only accept committed people · Free 15-min fit call before any payment</p>
    </div>
  </div>
</section>

<!-- TESTIMONIAL -->
<section class="svc-quote">
  <div class="container">
    <div class="quote-card reveal">
      <div class="stars">★★★★★</div>
      <div class="q">
        "The 1:1 mentorship paid for itself in month two. I run my own credit business now and I just signed my eighth client — Victoria walks the talk."
      </div>
      <div class="who">
        <div class="av">AR</div>
        <div>
          <div class="nm">Aaliyah R.</div>
          <div class="loc">Atlanta, GA · Now runs her own credit-repair company</div>
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
        <span class="eyebrow">FAQ · Mentorship</span>
        <h2>Questions, <em class="serif gradient-text">answered.</em></h2>
        <p>The honest answers I give every founder before they apply.</p>
        <div class="ctas">
          <a href="#mentor-pricing" class="btn btn-pink">Apply for mentorship <span class="arr">→</span></a>
        </div>
      </div>
      <div class="faq-list reveal reveal-d2">
        <div class="faq-item"><div class="faq-q">Is this just a course? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">No. The mentorship is private 1:1 with me — weekly calls, real conversations, and personalised feedback on your business. The SOPs and templates come with it.</div></div></div>
        <div class="faq-item"><div class="faq-q">Do I need experience in credit repair? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">No. I'll teach you the whole game — disputes, software, sales, pricing — starting from zero. If you already have a few clients, we'll accelerate.</div></div></div>
        <div class="faq-item"><div class="faq-q">How long is the mentorship? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">The active 1:1 phase is roughly 90 days. After that you keep <strong>lifetime access</strong> to the SOPs, scripts, community, and updates — so the program grows with you.</div></div></div>
        <div class="faq-item"><div class="faq-q">How much time does it take? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Plan on 5–7 hours a week. Most mentees do it nights and weekends while they're still working a 9–5, then transition out as the business takes over their income.</div></div></div>
        <div class="faq-item"><div class="faq-q">What's the actual investment? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">$997 one-time, down from $2,497. Limited spots open each quarter so I can give every mentee the attention they signed up for.</div></div></div>
        <div class="faq-item"><div class="faq-q">Is there a guarantee? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">I can't legally guarantee an income number — that depends on you. What I <em>can</em> guarantee: I'll show up to every call, hand you every tool I use, and keep the door open for as long as you stay in the program.</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="svc-cta">
  <div class="container">
    <div class="cta-card reveal">
      <div class="cta-text">
        <span class="eyebrow">Ready to apply?</span>
        <h2>Let's build the <em class="serif">business you keep promising yourself.</em></h2>
        <p>Limited spots each quarter. Free 15-min fit call. No card, no pressure — just a real conversation about whether this is right for you.</p>
        <div class="ctas">
          <a href="#mentor-pricing" class="btn btn-pink">Apply for mentorship <span class="arr">→</span></a>
          <a href="{{ route('contact.show') }}" class="btn btn-ghost-light">Book a free fit call</a>
        </div>
        <div class="stamp">
          <img src="{{ asset('images/founderimage7.jpeg') }}" alt="Victoria Love" width="48" height="48" loading="lazy" decoding="async" />
          <div><div class="nm">Victoria Love</div><div class="ttl">Founder · Victorious Opportunities</div></div>
        </div>
      </div>
      <div class="cta-image">
        <img src="{{ asset('images/founderimage6.jpeg') }}" alt="Victoria Love" loading="lazy" decoding="async" />
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  const form = document.getElementById('mentorForm');
  if (!form) return;

  const TOTAL        = 5;
  const card         = form.closest('.fund-card');
  const header       = document.getElementById('mentorHeader');
  const progressWrap = document.getElementById('mentorProgress');
  const progressFill = card.querySelector('.fund-progress-bar > span');
  const currentLabel = document.getElementById('mentorCurrent');
  const backBtn      = document.getElementById('mentorBack');
  const nextBtn      = document.getElementById('mentorNext');
  const submitBtn    = document.getElementById('mentorSubmit');
  const finalOk      = document.getElementById('mentorFinalSuccess');
  const navWrap      = document.getElementById('mentorNav');
  const steps        = form.querySelectorAll('.fund-step');
  let current        = 1;

  const updateProgress = () => {
    progressFill.style.width = ((current / TOTAL) * 100) + '%';
    currentLabel.textContent = current;
  };

  const showStep = (n) => {
    if (n < 1 || n > TOTAL) return;
    steps.forEach(s => s.classList.remove('active'));
    form.querySelector(`.fund-step[data-step="${n}"]`).classList.add('active');
    backBtn.hidden    = (n === 1);
    nextBtn.hidden    = (n === TOTAL);
    submitBtn.hidden  = (n !== TOTAL);
    current = n;
    updateProgress();
    validateCurrent();
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  const validateCurrent = () => {
    const stepEl = form.querySelector(`.fund-step[data-step="${current}"]`);
    let ok = false;
    if (current === TOTAL) {
      const inputs = stepEl.querySelectorAll('input[required]');
      ok = Array.from(inputs).every(i => i.value.trim() !== '' && i.checkValidity());
      submitBtn.disabled = !ok;
    } else {
      ok = stepEl.querySelectorAll('input[type="radio"]:checked').length > 0;
      nextBtn.disabled = !ok;
    }
  };

  form.querySelectorAll('input[type="radio"]').forEach(r => {
    r.addEventListener('change', () => {
      form.querySelectorAll(`input[name="${r.name}"]`).forEach(other => {
        other.closest('label').classList.toggle('is-checked', other.checked);
      });
      validateCurrent();
      if (current < TOTAL) {
        setTimeout(() => showStep(current + 1), 320);
      }
    });
  });

  form.querySelectorAll('.fund-step[data-step="5"] input').forEach(i => {
    i.addEventListener('input', validateCurrent);
  });

  /* Phone live-format */
  const phoneInput = form.querySelector('input[name="phone"]');
  if (phoneInput) {
    phoneInput.addEventListener('input', (e) => {
      let d = e.target.value.replace(/\D+/g, '');
      if (d.startsWith('1') && d.length > 10) d = d.slice(1);
      d = d.slice(0, 10);
      const p1 = d.slice(0, 3), p2 = d.slice(3, 6), p3 = d.slice(6, 10);
      let out = '';
      if (d.length === 0)      out = '';
      else if (d.length < 4)   out = '(' + p1;
      else if (d.length < 7)   out = '(' + p1 + ') ' + p2;
      else                     out = '(' + p1 + ') ' + p2 + '-' + p3;
      e.target.value = out;
      validateCurrent();
    });
  }

  backBtn.addEventListener('click', () => current > 1 && showStep(current - 1));
  nextBtn.addEventListener('click', () => current < TOTAL && showStep(current + 1));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (submitBtn.disabled) return;
    submitBtn.disabled = true;
    const originalLabel = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Submitting…';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      });
      if (res.ok) {
        form.style.display = 'none';
        progressWrap.style.display = 'none';
        header.style.display = 'none';
        navWrap.style.display = 'none';
        finalOk.hidden = false;
        finalOk.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        const data = await res.json().catch(() => ({}));
        alert(data.message || 'Something went wrong. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalLabel;
      }
    } catch (err) {
      alert('Submission failed. Please check your connection and try again.');
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalLabel;
    }
  });

  updateProgress();
  validateCurrent();
})();
</script>

@endsection
