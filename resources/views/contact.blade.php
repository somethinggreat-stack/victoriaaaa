@extends('layouts.app')

@section('title', 'Contact | Victoria Love')
@section('description', 'Send a message and I personally respond to every inquiry within 24 hours.')
@section('bodyClass', 'page-contact')

@section('content')

<!-- ============ HERO ============ -->
<section class="ct-hero">
  <div class="container">
    <div class="ct-hero-text reveal">
      <span class="eyebrow"><span class="ct-eye-dot"></span> Replies within 24 hours</span>
      <h1>Let's <em class="serif gradient-text">talk.</em></h1>
      <p class="lede">Send a message and I'll respond personally — usually the same day.</p>
      <ul class="ct-hero-trust">
        <li><span>⏱</span>~24 hr response</li>
        <li><span>💬</span>No card · no pressure</li>
        <li><span>🏆</span>1,000+ clients served</li>
      </ul>
      <ul class="ct-hero-trust" style="margin-top:14px">
        <li><span>✉️</span><a href="mailto:support@victorialovecredit.com" style="color:inherit;text-decoration:none">support@victorialovecredit.com</a></li>
        <li><span>📍</span>18034 Ventura Blvd, Encino, CA 91316</li>
      </ul>
    </div>
  </div>
</section>

<!-- ============ FORM ============ -->
<section class="ct-body-section">
  <div class="container">

    @if (session('success'))
      <div class="ct-success reveal">
        <div class="ct-success-ico">✓</div>
        <h2>Got it, {{ session('contact_name') }}.</h2>
        <p>Your message is in. I'll personally reply within 24 hours — usually faster.</p>
      </div>
    @endif

    <div class="ct-grid">

      <!-- Victoria portrait -->
      <div class="ct-image-card reveal" aria-hidden="true">
        <img src="{{ asset('images/founderimage7.jpeg') }}" alt="" loading="lazy" decoding="async" />
        <div class="ct-image-caption">
          <span class="ct-image-eyebrow"><span>✦</span> Personally read</span>
          <h3>Victoria Love</h3>
          <p>Texas Realtor &amp; Credit Coach — every message lands directly with me.</p>
        </div>
      </div>

    <!-- Contact form -->
    <div class="ct-card ct-form-card reveal">
      <header class="ct-form-head">
        <h2>Send a <em class="serif">quick note.</em></h2>
        <p>The more detail you share, the better I can prep my reply.</p>
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

    </div><!-- /.ct-grid -->

  </div>
</section>

@endsection
