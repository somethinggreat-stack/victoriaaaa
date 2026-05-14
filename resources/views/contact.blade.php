@extends('layouts.app')

@section('title', 'Contact & Book a Free Call | Victoria Love')
@section('description', 'Book a free 15-minute strategy call or send a message — I personally respond to every inquiry within 24 hours.')
@section('bodyClass', 'page-contact')

@section('content')

@php
  $calendlyUrl = rtrim((string) config('services.calendly.url', ''), '/');
  // Append Calendly query options (preserves any existing ?params on the URL)
  $calendlyEmbed = $calendlyUrl
    ? $calendlyUrl . (str_contains($calendlyUrl, '?') ? '&' : '?') . 'hide_gdpr_banner=1&primary_color=e63179'
    : null;
@endphp

<!-- ============ HERO ============ -->
<section class="ct-hero">
  <div class="container">
    <div class="ct-hero-text reveal">
      <span class="eyebrow"><span class="ct-eye-dot"></span> Replies within 24 hours</span>
      <h1>Let's <em class="serif gradient-text">talk.</em></h1>
      <p class="lede">Pick a time on the calendar for a free 15-minute strategy call, or send a message and I'll respond personally — usually the same day.</p>
      <ul class="ct-hero-trust">
        <li><span>⏱</span>~24 hr response</li>
        <li><span>💬</span>No card · no pressure</li>
        <li><span>🏆</span>1,000+ clients served</li>
      </ul>
    </div>
  </div>
</section>

<!-- ============ FORM + CALENDAR ============ -->
<section class="ct-body-section">
  <div class="container">

    @if (session('success'))
      <div class="ct-success reveal">
        <div class="ct-success-ico">✓</div>
        <h2>Got it, {{ session('contact_name') }}.</h2>
        <p>Your message is in. I'll personally reply within 24 hours — usually faster. If you also want to lock in a time, the calendar below is open.</p>
      </div>
    @endif

    <div class="ct-grid">

      <!-- LEFT — Contact form -->
      <div class="ct-card ct-form-card reveal">
        <header class="ct-form-head">
          <span class="ct-card-tag">01 · Send a message</span>
          <h2>Send a <em class="serif">quick note.</em></h2>
          <p>The more detail you share, the better I can prep for our call.</p>
        </header>

        @if ($errors->any())
          <div class="ct-alert error" role="alert">
            <strong>Please fix the highlighted fields:</strong>
            <ul>
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="contactForm" class="ct-form" method="POST" action="{{ route('contact.submit') }}" autocomplete="on" novalidate>
          @csrf

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Your name <em>*</em></span>
              <input type="text" name="name" required maxlength="120" value="{{ old('name') }}" placeholder="Jane Smith" autocomplete="name" />
            </label>

            <label class="ct-field">
              <span class="ct-lab">Email address <em>*</em></span>
              <input type="email" name="email" required maxlength="255" value="{{ old('email') }}" placeholder="you@email.com" autocomplete="email" />
            </label>
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Phone <small>(optional)</small></span>
              <input type="tel" name="phone" maxlength="30" value="{{ old('phone') }}" placeholder="(555) 123-4567" autocomplete="tel" />
            </label>

            <label class="ct-field">
              <span class="ct-lab">What's this about?</span>
              <select name="topic" autocomplete="off">
                <option value="">Pick a topic</option>
                @foreach (['Credit repair','DIY business + funding','Credit consultation','Mentorship / partnership','Press / collab','Something else'] as $t)
                  <option value="{{ $t }}" @selected(old('topic')===$t)>{{ $t }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <div class="ct-row">
            <label class="ct-field">
              <span class="ct-lab">Current credit score</span>
              <select name="score" autocomplete="off">
                <option value="">Not sure / haven't checked</option>
                @foreach (['Below 500','500–579 (Poor)','580–669 (Fair)','670–739 (Good)','740+ (Great)'] as $s)
                  <option value="{{ $s }}" @selected(old('score')===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </label>

            <label class="ct-field">
              <span class="ct-lab">When do you want to start?</span>
              <select name="timeline" autocomplete="off">
                <option value="">Pick a timeline</option>
                @foreach (['ASAP — ready today','Within 1–2 weeks','In the next month','Just exploring'] as $tl)
                  <option value="{{ $tl }}" @selected(old('timeline')===$tl)>{{ $tl }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <label class="ct-field">
            <span class="ct-lab">How did you hear about Victoria?</span>
            <select name="source" autocomplete="off">
              <option value="">Pick one</option>
              @foreach (['Instagram','TikTok','Google search','YouTube','Friend / referral','Podcast / press','Other'] as $sr)
                <option value="{{ $sr }}" @selected(old('source')===$sr)>{{ $sr }}</option>
              @endforeach
            </select>
          </label>

          <label class="ct-field">
            <span class="ct-lab">Your message <em>*</em></span>
            <textarea name="message" rows="5" required minlength="5" maxlength="4000" placeholder="Tell me about your file, your goal, or your timeline — anything that helps me show up prepared.">{{ old('message') }}</textarea>
          </label>

          <button type="submit" class="ct-submit">
            Send message <span class="arr">→</span>
          </button>

          <p class="ct-fine">By submitting, you agree to be contacted by email or phone. Your info stays private.</p>
        </form>
      </div>

      <!-- RIGHT — Calendar -->
      <div class="ct-card ct-cal-card reveal reveal-d2">
        <header class="ct-form-head">
          <span class="ct-card-tag">02 · Or book a time</span>
          <h2>Lock in a <em class="serif">free 15-min call.</em></h2>
        </header>

        @if ($calendlyEmbed)
          <div class="ct-cal-embed">
            <!-- Calendly inline widget -->
            <div class="calendly-inline-widget"
                 data-url="{{ $calendlyEmbed }}"
                 style="min-width:320px;width:100%;height:100%;min-height:700px;"></div>
            <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
          </div>
        @else
          <div class="ct-cal-placeholder">
            <div class="ct-cal-ico">📅</div>
            <strong>Calendar embed coming soon</strong>
            <p>Set <code>CALENDLY_URL</code> in your <code>.env</code> file to embed your scheduling page here.</p>
            <a href="mailto:info@victoriousopportunities.com" class="btn btn-pink">Email me instead <span class="arr">→</span></a>
          </div>
        @endif

      </div>

    </div>

  </div>
</section>

@endsection
