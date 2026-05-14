@extends('layouts.app')

@section('title', 'Welcome — Final Step | Victoria Love')
@section('description', 'You are one step closer to transforming your credit. Submit this quick form so we can link your payment to your account and begin disputes.')
@section('bodyClass', 'page-onboarding')

@section('content')

@php
  $states = [
    'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California',
    'CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia',
    'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
    'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana',
    'ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota',
    'MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
    'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
    'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon',
    'PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota',
    'TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia',
    'WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming',
  ];
  $months = [
    '01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',
    '07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December',
  ];
  $yearMax = (int) date('Y') - 18;
  $yearMin = 1925;

  // Pre-fill DOB parts from old('birth_date') if validation failed
  $dobMonth = $dobDay = $dobYear = '';
  if (old('birth_date') && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', old('birth_date'), $m)) {
    [$dobMonth, $dobDay, $dobYear] = [$m[1], $m[2], $m[3]];
  }
@endphp

@if (session('success'))
  <!-- ============ SUCCESS STATE ============ -->
  <section class="ob-hero ob-hero-success">
    <div class="container">
      <div class="ob-success reveal">
        <div class="ob-success-ico">✓</div>
        <span class="ob-success-eyebrow">You're enrolled</span>
        <h1>Thank you, <em class="serif gradient-text">{{ session('client_name') }}</em>.</h1>
        <p class="lede">Your information is in. I'll personally review your file, link it to your payment, and send your secure portal access within 24 hours. Watch your inbox — and your spam folder — for an email from <strong>Victorious Opportunities</strong>.</p>

        <div class="ob-success-next">
          <div class="ob-success-next-head">What happens in the next 24 hours:</div>
          <ol>
            <li><span>1</span>I match your form to your payment.</li>
            <li><span>2</span>You receive a portal-access email with password setup.</li>
            <li><span>3</span>You sign up for MyScoreIQ to give us read-only access to your file.</li>
            <li><span>4</span>Your first dispute round goes out within 7 days.</li>
          </ol>
        </div>

        <div class="ob-success-ctas">
          <a href="{{ url('/') }}" class="btn btn-pink">Back to home <span class="arr">→</span></a>
          <a href="{{ route('contact.show') }}" class="btn btn-ghost">Contact support</a>
        </div>
      </div>
    </div>
  </section>
@else

<!-- ============ WELCOME HERO ============ -->
<section class="ob-hero">
  <div class="container">
    <div class="ob-hero-grid">

      <div class="ob-hero-text reveal">
        <span class="eyebrow"><span class="ob-eye-dot"></span> Welcome aboard</span>
        <h1>You're <em class="serif gradient-text">one step closer</em> to transforming your credit.</h1>
        <p class="lede">Watch this quick walkthrough, then complete the form below. The faster you submit, the faster we get to work — your first dispute round goes out within 7 days of receiving everything we need.</p>

        <ul class="ob-hero-trust">
          <li><span>🏆</span>BBB Accredited</li>
          <li><span>🔒</span>256-bit Secure</li>
          <li><span>✓</span>FCRA Compliant</li>
        </ul>
      </div>

      <div class="ob-hero-video reveal reveal-d2">
        <div class="ob-must-watch" aria-hidden="true">
          <span class="ob-mw-pulse"></span>
          <span class="ob-mw-text">Must Watch — Don't Skip</span>
        </div>

        <div class="ob-video-card" id="obVideoCard">
          <video id="obVideo" class="ob-video-el" preload="metadata" playsinline controlslist="nodownload" poster="">
            <source src="{{ asset('images/onboardingpagevideo.mp4') }}" type="video/mp4" />
            Your browser does not support the video tag.
          </video>

          <button type="button" class="ob-video-overlay" id="obVideoOverlay" aria-label="Play welcome video">
            <span class="ob-video-tag">📺 Personally recorded by Victoria</span>

            <span class="ob-play" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="currentColor" width="26" height="26"><path d="M8 5v14l11-7z"/></svg>
            </span>

            <span class="ob-video-meta">
              <span class="ob-video-title">Here's exactly what happens next.</span>
              <span class="ob-video-duration" id="obVideoDuration">▶ Play</span>
            </span>
          </button>
        </div>

        <p class="ob-video-note">✨ Watch this <strong>before</strong> filling out the form — your 2-minute success blueprint</p>
      </div>

    </div>
  </div>
</section>

<!-- ============ FORM ============ -->
<section class="ob-form-section">


    @if ($errors->any())
      <div class="ob-alert error reveal" role="alert">
        <strong>Please fix the highlighted fields:</strong>
        <ul>
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('error'))
      <div class="ob-alert error reveal" role="alert">
        <strong>Heads up:</strong>
        <p>{{ session('error') }}</p>
      </div>
    @endif

    <form id="onboardingForm" class="ob-form reveal" method="POST" action="{{ route('onboarding.submit') }}" autocomplete="on" novalidate>
      @csrf

      <header class="ob-form-head">
        <div>
          <span class="eyebrow">Your information</span>
          <h2>Tell me who's <em class="serif gradient-text">about to win.</em></h2>
        </div>
        <span class="ob-req">Fields with <em>*</em> are required</span>
      </header>

      <!-- Section 1 · Identity -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">01</span>
          <span class="ob-section-ttl">Identity</span>
        </legend>

        <div class="ob-grid">
          <label class="ob-field">
            <span class="ob-lab">First Name <em>*</em></span>
            <input type="text" name="firstname" value="{{ old('firstname') }}" required maxlength="100" placeholder="Jane" autocomplete="given-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Last Name <em>*</em></span>
            <input type="text" name="lastname" value="{{ old('lastname') }}" required maxlength="100" placeholder="Smith" autocomplete="family-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Middle Name</span>
            <input type="text" name="middlename" value="{{ old('middlename') }}" maxlength="100" placeholder="Optional" autocomplete="additional-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Suffix</span>
            <select name="suffix" autocomplete="honorific-suffix">
              <option value="">None</option>
              @foreach (['Jr','Sr','II','III','IV'] as $sx)
                <option value="{{ $sx }}" @selected(old('suffix')===$sx)>{{ $sx }}</option>
              @endforeach
            </select>
          </label>
        </div>
      </fieldset>

      <!-- Section 2 · Contact -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">02</span>
          <span class="ob-section-ttl">Contact</span>
        </legend>

        <div class="ob-grid">
          <label class="ob-field">
            <span class="ob-lab">Email Address <em>*</em></span>
            <div class="ob-input-wrap">
              <input type="email" name="email" id="ob-email" value="{{ old('email') }}" required maxlength="255" placeholder="you@email.com" autocomplete="email" />
              <span class="ob-input-state" aria-hidden="true"></span>
            </div>
            <span class="ob-help" data-help="email">We'll send your portal access here.</span>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Phone Number <em>*</em></span>
            <div class="ob-input-wrap ob-phone-wrap">
              <span class="ob-phone-prefix">🇺🇸 +1</span>
              <input type="tel" name="phone" id="ob-phone" value="{{ old('phone') }}" required placeholder="(555) 123-4567" inputmode="tel" autocomplete="tel" />
              <span class="ob-input-state" aria-hidden="true"></span>
            </div>
            <span class="ob-help" data-help="phone">10-digit US number. We text appointment reminders only.</span>
          </label>
        </div>
      </fieldset>

      <!-- Section 3 · Address -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">03</span>
          <span class="ob-section-ttl">Address</span>
        </legend>

        <div class="ob-grid">
          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Street Address</span>
            <input type="text" name="street_address" value="{{ old('street_address') }}" maxlength="255" placeholder="123 Main Street, Apt 4B" autocomplete="street-address" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">City</span>
            <input type="text" name="city" value="{{ old('city') }}" maxlength="100" placeholder="Your city" autocomplete="address-level2" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">State</span>
            <select name="state" autocomplete="address-level1">
              <option value="">Select state</option>
              @foreach ($states as $abbr => $name)
                <option value="{{ $abbr }}" @selected(old('state')===$abbr)>{{ $abbr }} — {{ $name }}</option>
              @endforeach
            </select>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Zip Code</span>
            <input type="text" name="zip" value="{{ old('zip') }}" maxlength="10" placeholder="12345" inputmode="numeric" autocomplete="postal-code" />
          </label>
        </div>
      </fieldset>

      <!-- Section 4 · Secure Verification -->
      <fieldset class="ob-section ob-section-secure">
        <legend>
          <span class="ob-section-num">04</span>
          <span class="ob-section-ttl">Verification</span>
          <span class="ob-section-pad">🔒 256-bit encrypted</span>
        </legend>

        <p class="ob-section-intro">We need these to pull your credit file and submit disputes to the bureaus on your behalf. Your data never leaves our secure system.</p>

        <div class="ob-grid">
          <label class="ob-field">
            <span class="ob-lab">Full Social Security Number <em>*</em></span>
            <div class="ob-input-wrap">
              <input type="text" name="ssn" id="ob-ssn" value="{{ old('ssn') }}" required placeholder="XXX-XX-XXXX" inputmode="numeric" autocomplete="off" maxlength="11" />
              <button type="button" class="ob-ssn-toggle" id="ob-ssn-toggle" aria-label="Show or hide SSN">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <span class="ob-help" data-help="ssn">Required by the credit bureaus to identify your file.</span>
          </label>

          <div class="ob-field">
            <span class="ob-lab">Date of Birth <em>*</em></span>
            <div class="ob-dob">
              <select name="dob_month" id="ob-dob-month" required aria-label="Month">
                <option value="">Month</option>
                @foreach ($months as $num => $name)
                  <option value="{{ $num }}" @selected($dobMonth===$num)>{{ $name }}</option>
                @endforeach
              </select>
              <select name="dob_day" id="ob-dob-day" required aria-label="Day">
                <option value="">Day</option>
                @for ($d = 1; $d <= 31; $d++)
                  @php $dd = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                  <option value="{{ $dd }}" @selected($dobDay===$dd)>{{ $d }}</option>
                @endfor
              </select>
              <select name="dob_year" id="ob-dob-year" required aria-label="Year">
                <option value="">Year</option>
                @for ($y = $yearMax; $y >= $yearMin; $y--)
                  <option value="{{ $y }}" @selected($dobYear===(string)$y)>{{ $y }}</option>
                @endfor
              </select>
            </div>
            <input type="hidden" name="birth_date" id="ob-birth-date" value="{{ old('birth_date') }}" />
            <span class="ob-help" data-help="dob">You must be 18 or older to enroll.</span>
          </div>
        </div>
      </fieldset>

      <div class="ob-submit-wrap">
        <button type="submit" class="ob-submit" id="ob-submit">
          <span class="ob-submit-label">Submit &amp; activate my account</span>
          <span class="ob-submit-spinner" aria-hidden="true"></span>
          <span class="arr">→</span>
        </button>
        <p class="ob-submit-fine">By submitting, you agree to our <a href="#">Privacy Policy</a> and <a href="#">Terms of Service</a>.</p>
      </div>

      <div class="ob-badges">
        <div class="ob-badge"><span class="ico">🏆</span><div><strong>BBB Accredited</strong><small>Trusted business</small></div></div>
        <div class="ob-badge"><span class="ico">🔒</span><div><strong>Bank-grade security</strong><small>256-bit SSL</small></div></div>
        <div class="ob-badge"><span class="ico">✓</span><div><strong>FCRA Compliant</strong><small>Federally regulated</small></div></div>
      </div>
    </form>
  </div>
</section>

<!-- ============ AFTER SUBMITTING — STEPS ============ -->
<section class="ob-steps-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Here's what happens next</span>
      <h2>After you <em class="serif gradient-text">submit.</em></h2>
      <p>Five things — in order. Follow them and your first dispute round goes out within 7 days.</p>
    </div>

    <div class="ob-steps">
      <div class="ob-step reveal">
        <div class="ob-step-num">1</div>
        <h3>Check your email for portal access</h3>
        <p>You'll receive an email with your secure portal login and all next steps.</p>
        <span class="ob-step-note">Didn't get it? Check spam or contact support.</span>
      </div>

      <div class="ob-step reveal reveal-d2 ob-step-critical">
        <div class="ob-step-num">2</div>
        <span class="ob-step-tag">Required monthly</span>
        <h3>Sign up for MyScoreIQ</h3>
        <p><strong>Important:</strong> To get results, keep your MyScoreIQ account active every month. If it's not active, we can't deliver.</p>
      </div>

      <div class="ob-step reveal reveal-d3">
        <div class="ob-step-num">3</div>
        <h3>Upload your documents</h3>
        <p>Log in to your portal and upload:</p>
        <ul class="ob-list">
          <li>Social Security Card</li>
          <li>Government-issued ID</li>
          <li>Proof of Address (within 30 days)</li>
          <li>MyScoreIQ login details</li>
        </ul>
      </div>

      <div class="ob-step reveal reveal-d4">
        <div class="ob-step-num">4</div>
        <h3>Forward any mail you receive</h3>
        <p>Mail from credit bureaus or creditors? Upload it to your portal immediately. We respond fast and keep your file moving.</p>
      </div>

      <div class="ob-step reveal">
        <div class="ob-step-num">5</div>
        <h3>What to expect</h3>
        <ul class="ob-list">
          <li>Dispute results take ~30 days for the bureaus to respond</li>
          <li>Monthly updates straight to your inbox</li>
          <li>Free 15-minute check-in calls any time</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="ob-tests-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Real results</span>
      <h2>From clients who've <em class="serif gradient-text">transformed their credit.</em></h2>
    </div>

    <div class="ob-tests">
      <div class="ob-test reveal">
        <div class="stars">★★★★★</div>
        <h4>700+ score &amp; a brand-new Tesla in under 3 months</h4>
        <p>"I leveled up to a 700+ score and qualified for a brand new Tesla in under 90 days — fast turnaround and no fluff."</p>
        <div class="ob-test-who"><span class="av">B</span><div><strong>Brittney B.</strong><small>Tier 3 Client</small></div></div>
      </div>

      <div class="ob-test reveal reveal-d2">
        <div class="stars">★★★★★</div>
        <h4>From 400 credit score to 777</h4>
        <p>"I went from the 400s to a 777. The debt's gone and approvals started rolling in — $85,000 in new funding secured."</p>
        <div class="ob-test-who"><span class="av">J</span><div><strong>Josh T.</strong><small>Tier 2 Client</small></div></div>
      </div>

      <div class="ob-test reveal reveal-d3">
        <div class="stars">★★★★★</div>
        <h4>From bad credit to $90K in business credit</h4>
        <p>"We rebuilt my profile, crossed 700+, and secured $90,000 in business credit. That opened the door to the new Tesla."</p>
        <div class="ob-test-who"><span class="av">B</span><div><strong>Brandon B.</strong><small>Tier 1 Client</small></div></div>
      </div>
    </div>

    <div class="ob-footer-trust reveal">
      <p><strong>Your data is 100% secure.</strong> We never share, sell, or rent your information.</p>
      <p class="small">Questions? <a href="mailto:info@victoriousopportunities.com">Contact us anytime.</a></p>
    </div>
  </div>
</section>

@endif

@if (!session('success'))
<script>
(function () {
  /* ===== Hero video: click overlay to play, show native controls after start ===== */
  const obVideo    = document.getElementById('obVideo');
  const obOverlay  = document.getElementById('obVideoOverlay');
  const obCard     = document.getElementById('obVideoCard');
  const obDuration = document.getElementById('obVideoDuration');
  if (obVideo && obOverlay && obCard) {
    const fmtTime = (s) => {
      if (!isFinite(s)) return '▶ Play';
      const m = Math.floor(s / 60);
      const sec = Math.floor(s % 60).toString().padStart(2, '0');
      return `▶ ${m}:${sec}`;
    };
    obVideo.addEventListener('loadedmetadata', () => {
      if (obDuration) obDuration.textContent = fmtTime(obVideo.duration);
    });
    const startPlayback = () => {
      obCard.classList.add('is-playing');
      obVideo.setAttribute('controls', '');
      obVideo.play().catch(() => {
        obCard.classList.remove('is-playing');
        obVideo.removeAttribute('controls');
      });
    };
    obOverlay.addEventListener('click', startPlayback);
    obVideo.addEventListener('play',  () => obCard.classList.add('is-playing'));
    obVideo.addEventListener('ended', () => {
      obCard.classList.remove('is-playing');
      obVideo.removeAttribute('controls');
      obVideo.currentTime = 0;
    });
  }

  const form        = document.getElementById('onboardingForm');
  if (!form) return;
  const submitBtn   = document.getElementById('ob-submit');
  const emailEl     = document.getElementById('ob-email');
  const phoneEl     = document.getElementById('ob-phone');
  const ssnEl       = document.getElementById('ob-ssn');
  const ssnToggle   = document.getElementById('ob-ssn-toggle');
  const dobMonth    = document.getElementById('ob-dob-month');
  const dobDay      = document.getElementById('ob-dob-day');
  const dobYear     = document.getElementById('ob-dob-year');
  const dobHidden   = document.getElementById('ob-birth-date');

  /* ===== Phone mask: (555) 555-5555 ===== */
  const formatPhone = (raw) => {
    let d = raw.replace(/\D+/g, '');
    if (d.startsWith('1') && d.length > 10) d = d.slice(1);
    d = d.slice(0, 10);
    const p1 = d.slice(0, 3);
    const p2 = d.slice(3, 6);
    const p3 = d.slice(6, 10);
    if (d.length === 0) return '';
    if (d.length < 4)   return '(' + p1;
    if (d.length < 7)   return '(' + p1 + ') ' + p2;
    return '(' + p1 + ') ' + p2 + '-' + p3;
  };
  phoneEl.addEventListener('input', () => {
    phoneEl.value = formatPhone(phoneEl.value);
    setState(phoneEl, validatePhone(phoneEl.value));
  });
  phoneEl.addEventListener('blur', () => setState(phoneEl, validatePhone(phoneEl.value)));

  const validatePhone = (v) => {
    const d = (v || '').replace(/\D+/g, '');
    return d.length === 10;
  };

  /* ===== Email validation (RFC-ish) ===== */
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  const validateEmail = (v) => emailRe.test((v || '').trim());
  emailEl.addEventListener('input', () => setState(emailEl, validateEmail(emailEl.value)));
  emailEl.addEventListener('blur',  () => setState(emailEl, validateEmail(emailEl.value)));

  /* ===== SSN mask: XXX-XX-XXXX with show/hide ===== */
  let ssnVisible = false;
  let ssnRaw = (ssnEl.value || '').replace(/\D+/g, '').slice(0, 9);
  const renderSsn = () => {
    const d = ssnRaw;
    const masked = (i) => (ssnVisible ? d[i] : (i < d.length ? '•' : ''));
    let out = '';
    for (let i = 0; i < d.length; i++) {
      if (i === 3 || i === 5) out += '-';
      out += masked(i);
    }
    ssnEl.value = out;
    setState(ssnEl, validateSsn());
  };
  const validateSsn = () => ssnRaw.length === 9;
  ssnEl.addEventListener('input', (e) => {
    const incoming = (e.target.value || '').replace(/\D+/g, '').slice(0, 9);
    ssnRaw = incoming;
    renderSsn();
  });
  ssnEl.addEventListener('focus', () => { renderSsn(); });
  ssnEl.addEventListener('blur',  () => { renderSsn(); });
  ssnToggle.addEventListener('click', () => {
    ssnVisible = !ssnVisible;
    ssnToggle.classList.toggle('on', ssnVisible);
    renderSsn();
    ssnEl.focus();
  });
  // Initial render
  renderSsn();

  /* ===== DOB three selects → mm/dd/yyyy ===== */
  const daysInMonth = (m, y) => {
    if (!m || !y) return 31;
    return new Date(parseInt(y, 10), parseInt(m, 10), 0).getDate();
  };
  const refreshDayOptions = () => {
    const max = daysInMonth(dobMonth.value, dobYear.value);
    const cur = dobDay.value;
    [...dobDay.options].forEach((opt, i) => {
      if (i === 0) return;
      opt.hidden = parseInt(opt.value, 10) > max;
    });
    if (parseInt(cur, 10) > max) dobDay.value = '';
    syncDob();
  };
  const syncDob = () => {
    if (dobMonth.value && dobDay.value && dobYear.value) {
      dobHidden.value = `${dobMonth.value}/${dobDay.value}/${dobYear.value}`;
    } else {
      dobHidden.value = '';
    }
    setDobState();
  };
  const setDobState = () => {
    const valid = !!dobHidden.value;
    [dobMonth, dobDay, dobYear].forEach(el => {
      el.classList.toggle('is-valid', !!el.value);
    });
  };
  [dobMonth, dobYear].forEach(s => s.addEventListener('change', refreshDayOptions));
  dobDay.addEventListener('change', syncDob);
  refreshDayOptions();

  /* ===== Set valid/invalid state visuals ===== */
  function setState(el, ok) {
    el.classList.toggle('is-valid', ok && el.value !== '');
    el.classList.toggle('is-invalid', !ok && el.value !== '');
  }

  /* Initial validation pass (for old() values) */
  setState(emailEl, validateEmail(emailEl.value));
  setState(phoneEl, validatePhone(phoneEl.value));

  /* ===== Submit: validate, push SSN raw, lock button ===== */
  form.addEventListener('submit', (e) => {
    // Push the raw SSN digits as the actual value
    ssnEl.value = ssnRaw;

    // Force DOB sync one more time
    syncDob();

    // Native + custom checks
    let firstInvalid = null;
    form.querySelectorAll('input[required], select[required]').forEach(el => {
      const isOk = el.checkValidity()
        && (el.id !== 'ob-email'   || validateEmail(el.value))
        && (el.id !== 'ob-phone'   || validatePhone(el.value))
        && (el.id !== 'ob-ssn'     || validateSsn());
      if (!isOk && !firstInvalid) firstInvalid = el;
    });
    if (!dobHidden.value && !firstInvalid) firstInvalid = dobMonth;

    if (firstInvalid) {
      e.preventDefault();
      firstInvalid.classList.add('is-invalid');
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus({ preventScroll: true });
      return;
    }

    // Loading state
    submitBtn.classList.add('is-loading');
    submitBtn.disabled = true;
  });
})();
</script>
@endif

@endsection
