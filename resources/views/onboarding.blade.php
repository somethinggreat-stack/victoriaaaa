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

<style>
  /* New onboarding form bits (files, credit monitoring, locked provider) */
  .ob-opt { color: var(--ink-3); font-weight: 400; font-size: 0.85em; }

  .ob-file-input {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px dashed var(--line-2);
    border-radius: var(--r-sm, 12px);
    background: var(--bg-2);
    font-family: inherit;
    font-size: 14px;
    color: var(--ink-2);
    cursor: pointer;
    transition: border-color .2s, background .2s;
  }
  .ob-file-input:hover { border-color: var(--pink); background: var(--pink-tint, #fff5f9); }
  .ob-file-input::file-selector-button {
    margin-right: 12px;
    padding: 8px 14px;
    border: 0;
    border-radius: 100px;
    background: var(--ink);
    color: #fff;
    font-family: inherit;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background .2s;
  }
  .ob-file-input:hover::file-selector-button { background: var(--pink); }
  .ob-file-input.is-invalid { border-color: #d93838; background: #fdecec; }

  .ob-enroll-btn {
    display: inline-flex; align-items: center; gap: 8px;
    margin: 4px 0 20px;
    padding: 13px 22px;
    border-radius: 100px;
    background: var(--grad-warm, linear-gradient(135deg,#e63179,#ff7eb3));
    color: #fff; font-weight: 700; font-size: 14px;
    box-shadow: 0 12px 26px -10px rgba(230,49,121,0.55);
    transition: transform .2s, box-shadow .2s;
  }
  .ob-enroll-btn:hover { transform: translateY(-2px); box-shadow: 0 18px 34px -10px rgba(230,49,121,0.6); }
  .ob-enroll-btn .arr { transition: transform .2s; }
  .ob-enroll-btn:hover .arr { transform: translateX(4px); }

  .ob-locked {
    background: var(--bg-2) !important;
    color: var(--ink-2) !important;
    cursor: not-allowed;
    font-weight: 600;
  }

  .ob-encrypted-note {
    text-align: center;
    font-size: 13px;
    color: #0f8a4a;
    font-weight: 600;
    margin-top: 12px;
  }

  /* Video removed — hero is now a single centered column */
  .ob-hero-grid { grid-template-columns: 1fr; justify-items: center; }
  .ob-hero-text { text-align: center; max-width: 760px; }
  .ob-hero-text .eyebrow { justify-content: center; }
  .ob-hero-text .lede { margin-left: auto; margin-right: auto; }
  .ob-hero-text .ob-hero-trust { justify-content: center; }
</style>

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
            <li><span>2</span>I review your documents and credit-monitoring access.</li>
            <li><span>3</span>Your first dispute round goes out within 7 days.</li>
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
        <p class="lede">Complete the form below. The faster you submit, the faster we get to work — your first dispute round goes out within 7 days of receiving everything we need.</p>

        <ul class="ob-hero-trust">
          <li><span>🏆</span>BBB Accredited</li>
          <li><span>🔒</span>256-bit Secure</li>
          <li><span>✓</span>FCRA Compliant</li>
        </ul>
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

    <form id="onboardingForm" class="ob-form reveal" method="POST" action="{{ route('onboarding.submit') }}" enctype="multipart/form-data" autocomplete="on" novalidate>
      @csrf

      <header class="ob-form-head">
        <div>
          <span class="eyebrow">Your information</span>
          <h2>Tell me who's <em class="serif gradient-text">about to win.</em></h2>
        </div>
        <span class="ob-req">Fields with <em>*</em> are required</span>
      </header>

      <!-- Section 1 · Your Details -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">01</span>
          <span class="ob-section-ttl">Your Details</span>
        </legend>

        <div class="ob-grid">
          <label class="ob-field">
            <span class="ob-lab">First Name <em>*</em></span>
            <input type="text" name="firstname" value="{{ old('firstname') }}" required maxlength="100" placeholder="Jane" autocomplete="given-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Middle Name <span class="ob-opt">(optional)</span></span>
            <input type="text" name="middlename" value="{{ old('middlename') }}" maxlength="100" placeholder="Optional" autocomplete="additional-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Last Name <em>*</em></span>
            <input type="text" name="lastname" value="{{ old('lastname') }}" required maxlength="100" placeholder="Smith" autocomplete="family-name" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Suffix <span class="ob-opt">(optional)</span></span>
            <select name="suffix" autocomplete="honorific-suffix">
              @foreach (['None','Jr.','Sr.','I','II','III','IV','V'] as $sx)
                <option value="{{ $sx }}" @selected(old('suffix', 'None')===$sx)>{{ $sx }}</option>
              @endforeach
            </select>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Email Address <em>*</em></span>
            <div class="ob-input-wrap">
              <input type="email" name="email" id="ob-email" value="{{ old('email') }}" required maxlength="255" placeholder="you@email.com" autocomplete="email" />
              <span class="ob-input-state" aria-hidden="true"></span>
            </div>
            <span class="ob-help" data-help="email">We'll send your portal access here.</span>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Phone <em>*</em></span>
            <div class="ob-input-wrap ob-phone-wrap">
              <span class="ob-phone-prefix">🇺🇸 +1</span>
              <input type="tel" name="phone" id="ob-phone" value="{{ old('phone') }}" required placeholder="(555) 123-4567" inputmode="tel" autocomplete="tel" />
              <span class="ob-input-state" aria-hidden="true"></span>
            </div>
            <span class="ob-help" data-help="phone">10-digit US number.</span>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Date of Birth <em>*</em></span>
            <div class="ob-input-wrap">
              <input type="text" name="birth_date" id="ob-dob" value="{{ old('birth_date') }}" required placeholder="mm/dd/yyyy" inputmode="numeric" autocomplete="bday" maxlength="10" />
              <span class="ob-input-state" aria-hidden="true"></span>
            </div>
            <span class="ob-help" data-help="dob">Format: mm/dd/yyyy.</span>
          </label>

          <label class="ob-field ob-field-wide ob-section-secure">
            <span class="ob-lab">Full SSN <em>*</em> <span class="ob-section-pad">🔒 encrypted</span></span>
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
        </div>
      </fieldset>

      <!-- Section 2 · Mailing Address -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">02</span>
          <span class="ob-section-ttl">Mailing Address</span>
        </legend>

        <div class="ob-grid">
          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Street Address <em>*</em></span>
            <input type="text" name="street_address" value="{{ old('street_address') }}" required maxlength="255" placeholder="123 Main Street" autocomplete="address-line1" />
          </label>

          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Apt / Suite <span class="ob-opt">(optional)</span></span>
            <input type="text" name="address_line2" value="{{ old('address_line2') }}" maxlength="100" placeholder="Apt 4B" autocomplete="address-line2" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">City <em>*</em></span>
            <input type="text" name="city" value="{{ old('city') }}" required maxlength="100" placeholder="Your city" autocomplete="address-level2" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">State <em>*</em></span>
            <select name="state" required autocomplete="address-level1">
              <option value="">Select state</option>
              @foreach ($states as $abbr => $name)
                <option value="{{ $abbr }}" @selected(old('state')===$abbr)>{{ $abbr }} — {{ $name }}</option>
              @endforeach
            </select>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Zip Code <em>*</em></span>
            <input type="text" name="zip" value="{{ old('zip') }}" required maxlength="10" placeholder="12345" inputmode="numeric" autocomplete="postal-code" />
          </label>
        </div>
      </fieldset>

      <!-- Section 3 · Documents -->
      <fieldset class="ob-section ob-section-secure">
        <legend>
          <span class="ob-section-num">03</span>
          <span class="ob-section-ttl">Documents</span>
          <span class="ob-section-pad">🔒 encrypted upload</span>
        </legend>

        <p class="ob-section-intro">Upload clear photos or PDFs. These go straight to our secure processing partner — they are never stored on this website.</p>

        <div class="ob-grid">
          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Driver's License <em>*</em></span>
            <input type="file" name="drivers_license" id="ob-dl" class="ob-file-input" accept=".pdf,.jpg,.jpeg,.png,.webp" required />
            <span class="ob-help" data-help="drivers_license">PDF or image, up to 10 MB.</span>
          </label>

          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Social Security Card <span class="ob-opt">(optional)</span></span>
            <input type="file" name="ssn_card" id="ob-ssncard" class="ob-file-input" accept=".pdf,.jpg,.jpeg,.png,.webp" />
            <span class="ob-help" data-help="ssn_card">Providing this helps get stronger results on your file.</span>
          </label>

          <label class="ob-field ob-field-wide">
            <span class="ob-lab">Proof of Address <em>*</em></span>
            <input type="file" name="proof_of_address" id="ob-poa" class="ob-file-input" accept=".pdf,.jpg,.jpeg,.png,.webp" required />
            <span class="ob-help" data-help="proof_of_address">Utility bill, bank statement, or lease — up to 10 MB.</span>
          </label>
        </div>
      </fieldset>

      <!-- Section 4 · Credit Monitoring -->
      <fieldset class="ob-section">
        <legend>
          <span class="ob-section-num">04</span>
          <span class="ob-section-ttl">Credit Monitoring</span>
        </legend>

        <p class="ob-section-intro">We use <strong>myfreescore</strong> to monitor your progress. Enroll first, then enter the login you just created below.</p>

        <a href="https://app.myfreescorenow.com/enroll/B01C4681?s=MXxmYWxzZQ%3D%3D" target="_blank" rel="noopener" class="ob-enroll-btn">Get Credit Monitoring <span class="arr">→</span></a>

        <div class="ob-grid">
          <label class="ob-field">
            <span class="ob-lab">Credit Monitoring Provider</span>
            <input type="text" name="credit_monitoring_provider" value="myfreescore" readonly aria-readonly="true" class="ob-locked" tabindex="-1" />
            <span class="ob-help">Locked — monitoring is done through myfreescore.</span>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Credit Monitoring Email <em>*</em></span>
            <input type="email" name="credit_monitoring_email" id="ob-cm-email" value="{{ old('credit_monitoring_email') }}" required maxlength="255" placeholder="The email you enrolled with" autocomplete="off" />
          </label>

          <label class="ob-field">
            <span class="ob-lab">Credit Monitoring Password <em>*</em></span>
            <div class="ob-input-wrap">
              <input type="password" name="credit_monitoring_password" id="ob-cm-pass" required maxlength="255" placeholder="Your myfreescore password" autocomplete="off" />
              <button type="button" class="ob-ssn-toggle" id="ob-cm-toggle" aria-label="Show or hide password">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </label>

          <label class="ob-field">
            <span class="ob-lab">Security Question Answer <span class="ob-opt">(optional)</span></span>
            <input type="text" name="credit_monitoring_security_answer" value="{{ old('credit_monitoring_security_answer') }}" maxlength="255" placeholder="If you set one" autocomplete="off" />
          </label>
        </div>
      </fieldset>

      <div class="ob-submit-wrap">
        <button type="submit" class="ob-submit" id="ob-submit">
          <span class="ob-submit-label">Submit Securely</span>
          <span class="ob-submit-spinner" aria-hidden="true"></span>
          <span class="arr">→</span>
        </button>
        <p class="ob-encrypted-note">🔒 Encrypted submission · Your documents are stored privately.</p>
        <p class="ob-submit-fine">By submitting, you agree to our <a href="{{ route('legal.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a>, <a href="{{ route('legal.terms-of-service') }}" target="_blank" rel="noopener">Terms of Service</a>, and <a href="{{ route('legal.disclaimer') }}" target="_blank" rel="noopener">Disclaimer</a>.</p>
      </div>

      <div class="ob-badges">
        <div class="ob-badge"><span class="ico">🏆</span><div><strong>BBB Accredited</strong><small>Trusted business</small></div></div>
        <div class="ob-badge"><span class="ico">🔒</span><div><strong>Bank-grade security</strong><small>256-bit SSL</small></div></div>
        <div class="ob-badge"><span class="ico">✓</span><div><strong>FCRA Compliant</strong><small>Federally regulated</small></div></div>
      </div>
    </form>
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
      <p class="small">Questions? <a href="mailto:support@victorialovecredit.com">Contact us anytime.</a></p>
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
  const dobEl       = document.getElementById('ob-dob');
  const cmPassEl    = document.getElementById('ob-cm-pass');
  const cmToggle    = document.getElementById('ob-cm-toggle');
  const fileInputs  = [document.getElementById('ob-dl'), document.getElementById('ob-ssncard'), document.getElementById('ob-poa')].filter(Boolean);
  const MAX_FILE_BYTES = 10 * 1024 * 1024; // 10 MB

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

  /* ===== DOB mask: mm/dd/yyyy ===== */
  const formatDob = (raw) => {
    let d = raw.replace(/\D+/g, '').slice(0, 8);
    const mm = d.slice(0, 2);
    const dd = d.slice(2, 4);
    const yy = d.slice(4, 8);
    let out = mm;
    if (d.length >= 2) out = mm + '/' + dd;
    if (d.length >= 4) out = mm + '/' + dd + '/' + yy;
    return out;
  };
  const validateDob = (v) => {
    const m = (v || '').match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (!m) return false;
    const mm = +m[1], dd = +m[2], yy = +m[3];
    if (mm < 1 || mm > 12 || dd < 1 || dd > 31 || yy < 1900) return false;
    const dt = new Date(yy, mm - 1, dd);
    if (dt.getFullYear() !== yy || dt.getMonth() !== mm - 1 || dt.getDate() !== dd) return false;
    return dt < new Date(); // must be in the past
  };
  if (dobEl) {
    dobEl.addEventListener('input', () => {
      dobEl.value = formatDob(dobEl.value);
      setState(dobEl, validateDob(dobEl.value));
    });
    dobEl.addEventListener('blur', () => setState(dobEl, validateDob(dobEl.value)));
  }

  /* ===== Credit-monitoring password show/hide ===== */
  if (cmPassEl && cmToggle) {
    cmToggle.addEventListener('click', () => {
      const show = cmPassEl.type === 'password';
      cmPassEl.type = show ? 'text' : 'password';
      cmToggle.classList.toggle('on', show);
      cmPassEl.focus();
    });
  }

  /* ===== File size guard (10 MB) ===== */
  fileInputs.forEach(inp => {
    inp.addEventListener('change', () => {
      const f = inp.files && inp.files[0];
      if (f && f.size > MAX_FILE_BYTES) {
        inp.classList.add('is-invalid');
        const help = inp.parentElement.querySelector('.ob-help');
        if (help) { help.dataset.orig = help.dataset.orig || help.textContent; help.textContent = 'That file is over 10 MB — please choose a smaller file.'; help.classList.add('error'); }
        inp.value = '';
      } else {
        inp.classList.remove('is-invalid');
        const help = inp.parentElement.querySelector('.ob-help');
        if (help && help.dataset.orig) { help.textContent = help.dataset.orig; help.classList.remove('error'); }
      }
    });
  });

  /* ===== Set valid/invalid state visuals ===== */
  function setState(el, ok) {
    el.classList.toggle('is-valid', ok && el.value !== '');
    el.classList.toggle('is-invalid', !ok && el.value !== '');
  }

  /* Initial validation pass (for old() values) */
  setState(emailEl, validateEmail(emailEl.value));
  setState(phoneEl, validatePhone(phoneEl.value));
  if (dobEl && dobEl.value) setState(dobEl, validateDob(dobEl.value));

  /* ===== Submit: validate, push SSN raw, lock button ===== */
  form.addEventListener('submit', (e) => {
    // Push the raw SSN digits as the actual value
    ssnEl.value = ssnRaw;

    // Native + custom checks (required fields, incl. required file inputs)
    let firstInvalid = null;
    form.querySelectorAll('input[required], select[required]').forEach(el => {
      let isOk = el.checkValidity()
        && (el.id !== 'ob-email' || validateEmail(el.value))
        && (el.id !== 'ob-phone' || validatePhone(el.value))
        && (el.id !== 'ob-ssn'   || validateSsn())
        && (el.id !== 'ob-dob'   || validateDob(el.value));
      if (el.type === 'file') isOk = el.files && el.files.length > 0;
      if (!isOk && !firstInvalid) firstInvalid = el;
      if (!isOk) el.classList.add('is-invalid');
    });

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
