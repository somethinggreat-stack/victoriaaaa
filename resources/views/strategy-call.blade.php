@extends('layouts.app')

@section('title', 'Free Strategy Call | Victoria Love')
@section('description', 'Tell me about your file in 60 seconds and I will personally meet you on a free 15-minute strategy call.')
@section('bodyClass', 'page-strategy-call')

@section('content')

<!-- ============ HERO ============ -->
<section class="ct-hero">
  <div class="container">
    <div class="ct-hero-text reveal">
      <span class="eyebrow"><span class="ct-eye-dot"></span> Step 1 of 2 · Qualify first, book second</span>
      <h1>Your free <em class="serif gradient-text">strategy call.</em></h1>
      <p class="lede">Take 60 seconds to answer a few questions so I can prepare for our call. After this, you'll pick a time on my calendar.</p>
      <ul class="ct-hero-trust">
        <li><span>⏱</span>15-min Zoom call</li>
        <li><span>🎯</span>No card · no pitch</li>
        <li><span>🔒</span>Your info stays private</li>
      </ul>
    </div>
  </div>
</section>

<!-- ============ FORM ============ -->
<section class="ct-body-section">
  <div class="container">

    <div class="ct-grid">

      <!-- Victoria portrait -->
      <div class="ct-image-card reveal" aria-hidden="true">
        <img src="{{ asset('images/founderimage7.jpeg') }}" alt="" loading="lazy" decoding="async" />
        <div class="ct-image-caption">
          <span class="ct-image-eyebrow"><span>✦</span> Reviewed personally</span>
          <h3>Victoria Love</h3>
          <p>I read every form before the call so we don't waste your 15 minutes.</p>
        </div>
      </div>

      <!-- Qualification form -->
      <div class="ct-card ct-form-card reveal">
        <header class="ct-form-head">
          <h2>Tell me about your <em class="serif">file.</em></h2>
          <p>The clearer you are here, the more I can prep before we get on Zoom.</p>
        </header>

        @if ($errors->any())
          <div class="ct-alert error" role="alert">
            <strong>A couple of fields still need attention:</strong>
            <ul>
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="strategyCallForm" class="ct-form" method="POST" action="{{ route('strategy-call.submit') }}" autocomplete="on" novalidate>
          @csrf

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Your name <em>*</em></span>
              <input type="text" name="name" required maxlength="120" value="{{ old('name') }}" placeholder="Jane Smith" autocomplete="name" />
            </label>
            <label class="ct-field">
              <span class="ct-lab">Email <em>*</em></span>
              <input type="email" name="email" required maxlength="255" value="{{ old('email') }}" placeholder="you@email.com" autocomplete="email" />
            </label>
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Phone <em>*</em></span>
              <input type="tel" name="phone" required maxlength="30" value="{{ old('phone') }}" placeholder="(555) 123-4567" autocomplete="tel" />
            </label>
            <label class="ct-field">
              <span class="ct-lab">Best time to reach you</span>
              <input type="text" name="best_time" maxlength="120" value="{{ old('best_time') }}" placeholder="Weekday afternoons CT" />
            </label>
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Where are you with your credit?</span>
              <select name="situation" autocomplete="off">
                <option value="">Pick one</option>
                @foreach ([
                  'starting'        => 'Just starting — don\'t know where I stand',
                  'mid'             => 'In the middle of fixing it on my own',
                  'stuck'           => 'Stuck — tried things, nothing\'s moving',
                  'already_worked'  => 'Worked with a credit repair company before',
                ] as $val => $lab)
                  <option value="{{ $val }}" @selected(old('situation')===$val)>{{ $lab }}</option>
                @endforeach
              </select>
            </label>
            <label class="ct-field">
              <span class="ct-lab">Current credit score</span>
              <select name="score" autocomplete="off">
                <option value="">Not sure / haven't checked</option>
                @foreach (['Below 500','500–579 (Poor)','580–669 (Fair)','670–739 (Good)','740+ (Great)'] as $s)
                  <option value="{{ $s }}" @selected(old('score')===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">When do you want to start?</span>
              <select name="timeline" autocomplete="off">
                <option value="">Pick a timeline</option>
                @foreach (['ASAP — ready today','Within 1–2 weeks','In the next month','Just exploring'] as $tl)
                  <option value="{{ $tl }}" @selected(old('timeline')===$tl)>{{ $tl }}</option>
                @endforeach
              </select>
            </label>
            <label class="ct-field">
              <span class="ct-lab">Investment you're comfortable with</span>
              <select name="investment_range" autocomplete="off">
                <option value="">Pick a range</option>
                @foreach (['Under $300','$300–$600','$600–$1,000','$1,000+','Just exploring'] as $iv)
                  <option value="{{ $iv }}" @selected(old('investment_range')===$iv)>{{ $iv }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <label class="ct-field">
            <span class="ct-lab">What's your 90-day goal?</span>
            <textarea name="goal" rows="3" maxlength="2000" placeholder="Mortgage approval, business funding, buy a car, just clean things up — be specific so I can come ready.">{{ old('goal') }}</textarea>
          </label>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Have you tried credit repair before?</span>
              <select name="prior_repair" autocomplete="off">
                <option value="no"  @selected(old('prior_repair','no')==='no')>No — first time</option>
                <option value="yes" @selected(old('prior_repair')==='yes')>Yes — tell me below</option>
              </select>
            </label>
            <label class="ct-field">
              <span class="ct-lab">Who did you work with? <small>(optional)</small></span>
              <input type="text" name="prior_repair_notes" maxlength="2000" value="{{ old('prior_repair_notes') }}" placeholder="Company name, DIY method, etc." />
            </label>
          </div>

          <div class="ct-callout">
            <strong>Bring your credit monitoring login to the call.</strong>
            I can't help you on a 15-min call unless we can pull up your real reports together. Tell me which service you use and your username — <em>never your password</em>. You'll log in live during our Zoom.
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Monitoring service you use</span>
              <select name="monitoring_service" autocomplete="off">
                <option value="">Pick one</option>
                @foreach (['IdentityIQ','SmartCredit','MyScoreIQ','MyFICO','Experian (paid)','Credit Karma (free)','None yet','Other'] as $ms)
                  <option value="{{ $ms }}" @selected(old('monitoring_service')===$ms)>{{ $ms }}</option>
                @endforeach
              </select>
            </label>
            <label class="ct-field">
              <span class="ct-lab">Monitoring username / email</span>
              <input type="text" name="monitoring_username" maxlength="120" value="{{ old('monitoring_username') }}" placeholder="The email you log in with — never your password" autocomplete="off" />
            </label>
          </div>

          <label class="ct-check">
            <input type="checkbox" name="will_bring_login" value="1" @checked(old('will_bring_login')) required />
            <span><strong>I'll have my monitoring login open and ready</strong> when we get on Zoom. (Without it, the call gets rescheduled.)</span>
          </label>

          <label class="ct-check">
            <input type="checkbox" name="showup_confirmed" value="1" @checked(old('showup_confirmed')) required />
            <span><strong>I'll show up on time.</strong> If something comes up I'll reschedule at least 24 hours in advance — no ghosting.</span>
          </label>

          <button type="submit" class="ct-submit">
            Continue to booking <span class="arr">→</span>
          </button>

          <p class="ct-fine">After you submit, you'll be sent to my calendar to pick a time. By submitting you agree to be contacted by email or phone. Your info stays private.</p>
        </form>
      </div>

    </div><!-- /.ct-grid -->

  </div>
</section>

@endsection
