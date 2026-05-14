@extends('layouts.app')

@section('title', 'DIY Business + Funding — Unlock $100K+ Without Equity | Victoria Love')
@section('description', 'The LLC + business credit playbook that unlocks $100K+ in funding without giving up equity. Step-by-step setup, tradelines and lender intros.')

@section('content')

<!-- HERO -->
<section class="svc-hero">
  <div class="container">
    <div class="svc-hero-grid">
      <div class="svc-hero-text reveal">
        <span class="eyebrow">02 · DIY Business + Funding</span>
        <h1>Unlock $100K+ <em class="serif gradient-text">without giving up equity.</em></h1>
        <p class="lede">
          The LLC + business credit playbook I teach clients to keep <strong>100% of their company</strong> —
          and still walk into capital their friends only dream about.
        </p>
        <div class="svc-hero-ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
          <a href="{{ url('/') }}#ebooks" class="btn btn-ghost">Get the playbook</a>
        </div>

        <div class="svc-hero-stats">
          <div class="stt"><strong>$127K</strong><span>Avg funding unlocked</span></div>
          <div class="stt"><strong>0%</strong><span>Equity given up</span></div>
          <div class="stt"><strong>60–90 days</strong><span>Typical fundable timeline</span></div>
        </div>
      </div>

      <div class="svc-hero-img reveal reveal-d2">
        <span class="tag">Build · Fund · Scale</span>
        <img src="https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=900&q=75" alt="Business funding dashboard" width="900" height="1125" fetchpriority="high" decoding="async" />


          <div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ FUNDING QUALIFICATION FORM (multi-step) ============ -->
<section class="fund-qual" id="qualify">
  <div class="container">
    <div class="fund-card reveal">

      <div class="fund-header" id="fundHeader">
        <span class="fund-badge">📋 Application — takes 2 minutes</span>
        <h2>See how much funding you <em class="serif gradient-text">qualify for.</em></h2>
        <p>Answer a few quick questions and a specialist will map out your best funding path.</p>
      </div>

      <div class="fund-progress" id="fundProgress">
        <div class="fund-progress-bar"><span></span></div>
        <div class="fund-step-count"><strong id="fundCurrent">1</strong> / 9</div>
      </div>

      <form id="fundForm" method="POST" action="{{ route('funding.submit') }}" novalidate>
        @csrf

        <!-- Step 1 -->
        <div class="fund-step active" data-step="1">
          <span class="fund-step-tag">Step 1 · Funding goal</span>
          <h3>What funding amount would support your goals?</h3>
          <div class="fund-options">
            <label><input type="radio" name="amount" value="$10,000 - $25,000" required><span><span class="opt-letter">A</span><span>$10,000 - $25,000</span></span></label>
            <label><input type="radio" name="amount" value="$25,000 - $50,000"><span><span class="opt-letter">B</span><span>$25,000 - $50,000</span></span></label>
            <label><input type="radio" name="amount" value="$50,000 - $100,000"><span><span class="opt-letter">C</span><span>$50,000 - $100,000</span></span></label>
            <label><input type="radio" name="amount" value="$100,000 - $250,000"><span><span class="opt-letter">D</span><span>$100,000 - $250,000</span></span></label>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="fund-step" data-step="2">
          <span class="fund-step-tag">Step 2 · Accuracy</span>
          <h3>This form helps us evaluate how we can best assist you.</h3>
          <p class="fund-step-sub">Please provide accurate and honest information so we can offer the right solutions.</p>
          <p class="fund-step-question">Do you confirm that all information provided will be accurate and truthful?</p>
          <div class="fund-options">
            <label><input type="radio" name="confirmed" value="yes" required><span><span class="opt-letter">A</span><span>Yes, I confirm all information provided will be accurate and truthful.</span></span></label>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="fund-step" data-step="3">
          <span class="fund-step-tag">Step 3 · Credit limits</span>
          <h3>Do your personal credit cards have a combined credit limit of $15,000 or more?</h3>
          <p class="fund-step-sub">Do not include business credit cards or authorized user accounts.</p>
          <div class="fund-options">
            <label><input type="radio" name="limits" value="$15K or more" required><span><span class="opt-letter">A</span><span>Yes, I have $15,000 or more in total personal credit card limits</span></span></label>
            <label><input type="radio" name="limits" value="Less than $15K"><span><span class="opt-letter">B</span><span>No, I have less than $15,000 in total personal credit card limits</span></span></label>
            <label><input type="radio" name="limits" value="No personal cards"><span><span class="opt-letter">C</span><span>No, I do not have any personal credit cards</span></span></label>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="fund-step" data-step="4">
          <span class="fund-step-tag">Step 4 · Credit usage</span>
          <h3>Approximately what percentage of your personal credit card limits are you using right now?</h3>
          <p class="fund-step-sub">For reference: Using $10,000 of a $20,000 total limit equals 50% usage.</p>
          <div class="fund-options">
            <label><input type="radio" name="usage" value="0 - 10%" required><span><span class="opt-letter">A</span><span>0 – 10%</span></span></label>
            <label><input type="radio" name="usage" value="11 - 20%"><span><span class="opt-letter">B</span><span>11 – 20%</span></span></label>
            <label><input type="radio" name="usage" value="21 - 30%"><span><span class="opt-letter">C</span><span>21 – 30%</span></span></label>
            <label><input type="radio" name="usage" value="Over 30% — can reduce in 30-60 days"><span><span class="opt-letter">D</span><span>Over 30% — I can reduce my balances to 10–20% within 30–60 days <small>(required to qualify)</small></span></span></label>
            <label><input type="radio" name="usage" value="Over 30% — cannot reduce"><span><span class="opt-letter">E</span><span>Over 30% — I cannot reduce my balances within the next 30–60 days</span></span></label>
          </div>
        </div>

        <!-- Step 5 -->
        <div class="fund-step" data-step="5">
          <span class="fund-step-tag">Step 5 · FICO score</span>
          <h3>Exciting news! Based on what you've shared, you could potentially access significant capital.</h3>
          <p class="fund-step-sub">To continue, please tell us your personal FICO score as reported by Experian, TransUnion, and Equifax.</p>
          <div class="fund-options">
            <label><input type="radio" name="fico" value="800+" required><span><span class="opt-letter">A</span><span>800+</span></span></label>
            <label><input type="radio" name="fico" value="750 - 799"><span><span class="opt-letter">B</span><span>750 – 799</span></span></label>
            <label><input type="radio" name="fico" value="700 - 749"><span><span class="opt-letter">C</span><span>700 – 749</span></span></label>
            <label><input type="radio" name="fico" value="650 - 699"><span><span class="opt-letter">D</span><span>650 – 699</span></span></label>
            <label><input type="radio" name="fico" value="Below 650"><span><span class="opt-letter">E</span><span>Below 650</span></span></label>
          </div>
        </div>

        <!-- Step 6 -->
        <div class="fund-step" data-step="6">
          <span class="fund-step-tag">Step 6 · Business</span>
          <h3>Which best describes your situation?</h3>
          <div class="fund-options">
            <label><input type="radio" name="situation" value="Have a business" required><span><span class="opt-letter">A</span><span>I have a registered business</span></span></label>
            <label><input type="radio" name="situation" value="Personal funding"><span><span class="opt-letter">B</span><span>I don't have a business, I'm interested in personal funding</span></span></label>
            <label><input type="radio" name="situation" value="Aged corp purchase"><span><span class="opt-letter">C</span><span>I don't have a business but I'm interested in purchasing an existing company (aged corp)</span></span></label>
          </div>
        </div>

        <!-- Step 7 -->
        <div class="fund-step" data-step="7">
          <span class="fund-step-tag">Step 7 · Income</span>
          <h3>What is your current annual personal income?</h3>
          <p class="fund-step-sub">Include your salary, wages, or other personal income sources.</p>
          <div class="fund-options">
            <label><input type="radio" name="income" value="Less than $25K" required><span><span class="opt-letter">A</span><span>Less than $25,000</span></span></label>
            <label><input type="radio" name="income" value="$25K - $49,999"><span><span class="opt-letter">B</span><span>$25,000 – $49,999</span></span></label>
            <label><input type="radio" name="income" value="$50K - $74,999"><span><span class="opt-letter">C</span><span>$50,000 – $74,999</span></span></label>
            <label><input type="radio" name="income" value="$75K - $99,999"><span><span class="opt-letter">D</span><span>$75,000 – $99,999</span></span></label>
            <label><input type="radio" name="income" value="$100K or more"><span><span class="opt-letter">E</span><span>$100,000 or more</span></span></label>
          </div>
        </div>

        <!-- Step 8 -->
        <div class="fund-step" data-step="8">
          <span class="fund-step-tag">Step 8 · Credit profile</span>
          <h3>Do you have any of the following negative marks on your credit profile?</h3>
          <p class="fund-step-sub">Select all that apply.</p>
          <div class="fund-options fund-options-multi">
            <label><input type="checkbox" name="negatives[]" value="Bankruptcy"><span><span class="opt-letter">A</span><span>Bankruptcy</span></span></label>
            <label><input type="checkbox" name="negatives[]" value="Collections or Charge-offs"><span><span class="opt-letter">B</span><span>Collections or Charge-offs</span></span></label>
            <label><input type="checkbox" name="negatives[]" value="Foreclosures"><span><span class="opt-letter">C</span><span>Foreclosures</span></span></label>
            <label><input type="checkbox" name="negatives[]" value="Late Payments"><span><span class="opt-letter">D</span><span>Late Payments</span></span></label>
            <label data-clean="1"><input type="checkbox" name="negatives[]" value="None — credit is clean"><span><span class="opt-letter">E</span><span>None of the above, my credit is clean</span></span></label>
          </div>
        </div>

        <!-- Step 9 — Contact -->
        <div class="fund-step" data-step="9">
          <span class="fund-step-tag">Final step · Contact details</span>
          <div class="fund-success-box">
            <strong>🎉 Great news — you're well positioned!</strong>
            <p>Based on your answers, you have a strong credit profile and solid potential for funding. Our team can match you with the right 0% interest capital program right away.</p>
            <p>Please enter your information below so a specialist can reach out and walk you through your options — at zero cost and zero obligation.</p>
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

        <!-- Nav -->
        <div class="fund-nav" id="fundNav">
          <button type="button" class="fund-back" id="fundBack" hidden>← Back</button>
          <button type="button" class="fund-next" id="fundNext" disabled>OK / Next <span class="arr">→</span></button>
          <button type="submit" class="fund-submit" id="fundSubmit" hidden disabled>Submit Application <span class="arr">→</span></button>
        </div>
      </form>

      <!-- Final success replaces the form -->
      <div class="fund-final-success" id="fundFinalSuccess" hidden>
        <div class="ico">✓</div>
        <h3>Application submitted.</h3>
        <p>Thank you. A funding specialist will reach out within 24 hours to walk you through your matched programs.</p>
      </div>

    </div>
  </div>
</section>


<!-- OUTCOMES -->
<section class="svc-outcomes">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">What you walk away with</span>
      <h2>A fundable business — <em class="serif gradient-text">on paper and in practice.</em></h2>
      <p>By the end you don't just <em>look</em> fundable. You are. Lenders, vendors and underwriters can see it.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><div class="ic">L</div><h3>LLC done right</h3><p>Entity, EIN, registered agent, banking — set up the way lenders expect.</p></div>
      <div class="outcome reveal reveal-d2"><div class="ic">★</div><h3>Tradelines stacked</h3><p>Net-30 vendors, business credit cards and reporting tradelines in the right order.</p></div>
      <div class="outcome reveal reveal-d3"><div class="ic">$</div><h3>Lender intros</h3><p>Direct intros to the lenders that actually fund what we just built.</p></div>
      <div class="outcome reveal reveal-d4"><div class="ic">∞</div><h3>Keep 100%</h3><p>No investors, no dilution. Your equity stays yours — for life.</p></div>
    </div>
  </div>
</section>



<!-- INCLUDED -->
<section class="svc-included">
  <div class="container">
    <div class="included-grid">
      <div class="included-img reveal">
        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=75" alt="Business funding strategy" width="900" height="600" loading="lazy" decoding="async" />
      </div>
      <div class="included-list reveal reveal-d2">
        <span class="eyebrow">What's included</span>
        <h2>The full <em class="serif gradient-text">build-to-fund</em> stack.</h2>
        <p>This is the same step-by-step that's taken hundreds of clients from "side hustle" to fundable.</p>
        <ul>
          <li><span class="ck">✓</span><div><strong>LLC + entity setup</strong><small>State filing, registered agent, operating agreement — done lender-friendly.</small></div></li>
          <li><span class="ck">✓</span><div><strong>EIN, D-U-N-S &amp; banking</strong><small>The IDs and accounts every business credit application needs.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Business credit foundation</strong><small>Net-30 vendors, tradelines, and the reporting order that builds the file fast.</small></div></li>
          <li><span class="ck">✓</span><div><strong>$100K+ funding strategy</strong><small>Personal credit prep, business cards, lines of credit, term loans — sequenced.</small></div></li>
          <li><span class="ck">✓</span><div><strong>Direct lender introductions</strong><small>The bankers and brokers I actually send my clients to, with intros.</small></div></li>
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
      <h2>Three phases. <em class="serif gradient-text">From zero to fundable.</em></h2>
    </div>
    <div class="process-grid cols-3">
      <div class="step reveal">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>1</span> Setup</span>
          <span class="day">Weeks 1–3</span>
        </div>
        <div class="step-body"><h3>Form the entity, the right way</h3><p>LLC, EIN, D-U-N-S, business banking and address — every box lenders check before they fund.</p></div>
      </div>
      <div class="step reveal reveal-d2">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1556742031-c6961e8560b0?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>2</span> Build</span>
          <span class="day">Weeks 4–8</span>
        </div>
        <div class="step-body"><h3>Stack tradelines fast</h3><p>Net-30 vendors first, then store cards, then real business credit cards. Reporting in the right order.</p></div>
      </div>
      <div class="step reveal reveal-d3">
        <div class="step-img" style="background-image: url('https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=900&q=80');">
          <span class="num"><span>3</span> Apply</span>
          <span class="day">Weeks 9–12</span>
        </div>
        <div class="step-body"><h3>Apply &amp; deploy</h3><p>Lender intros, application sequencing, and capital deployed — without giving up a single point of equity.</p></div>
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
        "Walked in with a side hustle and walked out with a real LLC, $145K in business funding, and zero investors. I still own 100% of the company. That's the part nobody else teaches."
      </div>
      <div class="who">
        <div class="av">DT</div>
        <div>
          <div class="nm">Devin T.</div>
          <div class="loc">Dallas, TX · DIY Funding client</div>
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
        <span class="eyebrow">FAQ · DIY Funding</span>
        <h2>Questions, <em class="serif gradient-text">answered.</em></h2>
        <p>What founders ask before they buy in.</p>
        <div class="ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book free strategy call <span class="arr">→</span></a>
        </div>
      </div>
      <div class="faq-list reveal reveal-d2">
        <div class="faq-item"><div class="faq-q">How long until I'm fundable? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">60–90 days for most clients. The personal-credit foundation matters; if your file is heavy, I'll have you stack credit repair first.</div></div></div>
        <div class="faq-item"><div class="faq-q">Do I need an existing business? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">No. I'll walk you through forming the LLC the way lenders expect — entity name, address, banking, the works.</div></div></div>
        <div class="faq-item"><div class="faq-q">How much funding can I expect? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">Average client unlocks $100K–$150K across business cards, lines of credit and term products. It depends on personal credit, revenue and how disciplined you are with the build.</div></div></div>
        <div class="faq-item"><div class="faq-q">Is this just an e-book? <span class="icon">+</span></div><div class="faq-a"><div class="faq-a-inner">No. The e-book is the playbook; the service is the <em>application</em> of it — done with you, with direct lender intros at the end.</div></div></div>
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
        <h2>Let's get you <em class="serif">fundable.</em></h2>
        <p>Free 15-min call. I'll map your file, your business, and the next 90 days — with real numbers attached.</p>
        <div class="ctas">
          <a href="{{ route('contact.show') }}" class="btn btn-pink">Book my free call <span class="arr">→</span></a>
          <a href="{{ url('/') }}#ebooks" class="btn btn-ghost-light">Get the playbook</a>
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
  const form = document.getElementById('fundForm');
  if (!form) return;

  const TOTAL        = 9;
  const card         = form.closest('.fund-card');
  const header       = document.getElementById('fundHeader');
  const progressWrap = document.getElementById('fundProgress');
  const progressFill = card.querySelector('.fund-progress-bar > span');
  const currentLabel = document.getElementById('fundCurrent');
  const backBtn      = document.getElementById('fundBack');
  const nextBtn      = document.getElementById('fundNext');
  const submitBtn    = document.getElementById('fundSubmit');
  const finalOk      = document.getElementById('fundFinalSuccess');
  const navWrap      = document.getElementById('fundNav');
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
    } else if (current === 8) {
      ok = stepEl.querySelectorAll('input[type="checkbox"]:checked').length > 0;
      nextBtn.disabled = !ok;
    } else {
      ok = stepEl.querySelectorAll('input[type="radio"]:checked').length > 0;
      nextBtn.disabled = !ok;
    }
  };

  // Radio buttons — toggle .is-checked + auto-advance
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

  // Step 8 checkbox logic
  const negCbs = form.querySelectorAll('input[type="checkbox"][name="negatives[]"]');
  const noneCb = form.querySelector('input[type="checkbox"][name="negatives[]"][value="None — credit is clean"]');
  negCbs.forEach(cb => {
    cb.addEventListener('change', () => {
      if (cb === noneCb && cb.checked) {
        negCbs.forEach(other => {
          if (other !== cb) other.checked = false;
        });
      } else if (cb !== noneCb && cb.checked && noneCb) {
        noneCb.checked = false;
      }
      negCbs.forEach(c => c.closest('label').classList.toggle('is-checked', c.checked));
      validateCurrent();
    });
  });

  // Step 9 input listeners
  form.querySelectorAll('.fund-step[data-step="9"] input').forEach(i => {
    i.addEventListener('input', validateCurrent);
  });

  // Phone live-format
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
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
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
      console.error(err);
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
