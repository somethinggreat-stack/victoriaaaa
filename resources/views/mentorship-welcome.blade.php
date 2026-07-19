@extends('layouts.app')

@section('title', "You're In — Become Your Own Boss Mentorship | Victoria Love")
@section('description', 'Your mentorship onboarding: join Skool, add the weekly Monday Zoom class, and get into the Telegram group.')
@section('bodyClass', 'page-mentor-welcome')

{{-- Gated page — never index it, and don't leak it via referrer --}}
@section('head_extra')
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="referrer" content="no-referrer">
@endsection

@section('content')

<style>
  .mw-hero { padding: 130px 0 40px; position: relative; }
  .mw-hero .container { max-width: 1080px; }
  .mw-hero-grid {
    display: grid; grid-template-columns: 1.15fr 0.85fr;
    gap: 50px; align-items: center;
  }
  .mw-badge {
    display: inline-flex; align-items: center; gap: 9px;
    background: #e6f6ec; border: 1px solid #c9eccd; color: #157a3d;
    font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
    padding: 8px 15px; border-radius: 100px; margin-bottom: 20px;
  }
  .mw-badge .tick {
    width: 18px; height: 18px; border-radius: 50%;
    background: #157a3d; color: #fff; display: grid; place-items: center;
    font-size: 10px; font-weight: 700;
  }
  .mw-hero h1 { font-size: clamp(2.2rem, 4.2vw, 3.4rem); margin-bottom: 16px; }
  .mw-hero .lede { font-size: 18px; max-width: 560px; }
  .mw-portrait {
    border-radius: var(--r-xl, 32px); overflow: hidden;
    box-shadow: 0 40px 80px -30px rgba(20,16,14,0.35);
    aspect-ratio: 4/5;
  }
  .mw-portrait img { width: 100%; height: 100%; object-fit: cover; object-position: center 15%; }

  /* Steps */
  .mw-steps-section { padding: 20px 0 100px; }
  .mw-steps-section .container { max-width: 1080px; }
  .mw-steps { display: flex; flex-direction: column; gap: 20px; }

  .mw-step {
    position: relative;
    background: #fff; border: 1px solid var(--line); border-radius: var(--r-lg, 26px);
    padding: 32px 34px;
    display: grid; grid-template-columns: 62px 1fr; gap: 24px;
    transition: transform .3s, box-shadow .3s, border-color .3s;
  }
  .mw-step:hover { transform: translateY(-4px); box-shadow: 0 30px 60px -28px rgba(20,16,14,0.18); border-color: var(--line-2); }
  .mw-step-num {
    width: 52px; height: 52px; border-radius: 16px;
    background: var(--grad-warm, linear-gradient(135deg,#e63179,#ff7eb3));
    color: #fff; display: grid; place-items: center;
    font-size: 21px; font-weight: 700;
    box-shadow: 0 12px 24px -10px rgba(230,49,121,0.6);
  }
  .mw-step h2 { font-size: 23px; margin: 0 0 8px; letter-spacing: -0.02em; }
  .mw-step p { font-size: 15px; margin-bottom: 18px; }
  .mw-step .mw-tag {
    display: inline-block; font-size: 11px; font-weight: 700;
    letter-spacing: 0.14em; text-transform: uppercase; color: var(--pink);
    margin-bottom: 10px;
  }
  .mw-cta {
    display: inline-flex; align-items: center; gap: 9px;
    background: var(--ink); color: #fff;
    padding: 14px 26px; border-radius: 100px;
    font-weight: 650; font-size: 14.5px;
    transition: background .2s, transform .2s, box-shadow .2s;
  }
  .mw-cta:hover { background: var(--pink); transform: translateY(-2px); box-shadow: 0 16px 30px -12px rgba(230,49,121,0.5); }
  .mw-cta.is-pink { background: var(--pink); box-shadow: 0 14px 28px -12px rgba(230,49,121,0.55); }
  .mw-cta.is-pink:hover { background: var(--ink); }
  .mw-cta-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
  .mw-cta-ghost {
    display: inline-flex; align-items: center; gap: 8px;
    border: 1.5px solid var(--line-2); color: var(--ink);
    padding: 13px 22px; border-radius: 100px; font-weight: 600; font-size: 14px;
    transition: border-color .2s, color .2s;
  }
  .mw-cta-ghost:hover { border-color: var(--pink); color: var(--pink); }

  /* Zoom details box */
  .mw-meta {
    background: var(--bg-2); border: 1px solid var(--line);
    border-radius: 14px; padding: 16px 18px; margin-bottom: 18px;
    display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px;
  }
  .mw-meta div .k {
    font-size: 10.5px; font-weight: 700; letter-spacing: 0.16em;
    text-transform: uppercase; color: var(--ink-3); margin-bottom: 4px;
  }
  .mw-meta div .v { font-size: 15px; font-weight: 700; color: var(--ink); font-variant-numeric: tabular-nums; }
  .mw-when {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--pink-soft); color: #8a1845;
    border: 1px solid rgba(230,49,121,0.2);
    padding: 8px 14px; border-radius: 100px;
    font-size: 13px; font-weight: 700; margin-bottom: 14px;
  }

  /* Footer help */
  .mw-help {
    margin-top: 34px; text-align: center;
    font-size: 14px; color: var(--ink-2);
  }
  .mw-help a { color: var(--pink); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }

  @media (max-width: 900px) {
    .mw-hero-grid { grid-template-columns: 1fr; gap: 34px; }
    .mw-portrait { max-width: 340px; margin: 0 auto; }
  }
  @media (max-width: 600px) {
    .mw-hero { padding-top: 110px; }
    .mw-step { grid-template-columns: 1fr; gap: 16px; padding: 26px 22px; }
    .mw-step-num { width: 44px; height: 44px; font-size: 18px; }
  }
</style>

<!-- ============ WELCOME HERO ============ -->
<section class="mw-hero">
  <div class="container">
    <div class="mw-hero-grid">
      <div class="reveal">
        <span class="mw-badge"><span class="tick">✓</span> Payment confirmed</span>
        <h1>
          @if($firstName)
            Welcome in, <em class="serif gradient-text">{{ $firstName }}</em>.
          @else
            You're <em class="serif gradient-text">in.</em>
          @endif
        </h1>
        <p class="lede">
          You just joined <strong>Become Your Own Boss</strong> — my 1:1 mentorship.
          Three quick steps below and you're fully set up. Do them now, it takes about five minutes.
        </p>
        @if($planLabel)
          <p class="small" style="margin-top:10px;color:var(--ink-3)">Plan: <strong>{{ $planLabel }}</strong></p>
        @endif
      </div>

      <div class="mw-portrait reveal reveal-d2">
        <img src="{{ asset('images/onboardingimagementoship.jpeg') }}" alt="Victoria Puente" loading="eager" decoding="async" />
      </div>
    </div>
  </div>
</section>

<!-- ============ 3 STEPS ============ -->
<section class="mw-steps-section">
  <div class="container">
    <div class="mw-steps">

      <!-- Step 1 · Skool -->
      <div class="mw-step reveal">
        <div class="mw-step-num">1</div>
        <div>
          <span class="mw-tag">Do this first</span>
          <h2>Join the Skool community</h2>
          <p>This is our home base — every lesson, template, SOP and replay lives here. Sign up with the same email you paid with so I can approve you fast.</p>
          <a href="{{ $skoolUrl }}" target="_blank" rel="noopener" class="mw-cta is-pink">Join Skool <span class="arr">→</span></a>
        </div>
      </div>

      <!-- Step 2 · Zoom -->
      <div class="mw-step reveal reveal-d2">
        <div class="mw-step-num">2</div>
        <div>
          <span class="mw-tag">Weekly live class</span>
          <h2>Add the weekly Zoom call</h2>
          <span class="mw-when">🗓 {{ $zoomWhen }}</span>
          <p>Our live mentorship class runs every week. Add it to your calendar once and it repeats automatically — then just show up.</p>

          <div class="mw-meta">
            <div><div class="k">Meeting ID</div><div class="v">{{ $zoomId }}</div></div>
            <div><div class="k">Passcode</div><div class="v">{{ $zoomPass }}</div></div>
            <div><div class="k">Dial in (US)</div><div class="v">{{ $zoomDialIn }}</div></div>
          </div>

          <div class="mw-cta-row">
            <a href="{{ $zoomUrl }}" target="_blank" rel="noopener" class="mw-cta">Join the Zoom class <span class="arr">→</span></a>
            <a href="{{ $zoomIcs }}" class="mw-cta-ghost">📅 Add to my calendar</a>
          </div>
        </div>
      </div>

      <!-- Step 3 · Telegram -->
      <div class="mw-step reveal reveal-d3">
        <div class="mw-step-num">3</div>
        <div>
          <span class="mw-tag">Stay connected</span>
          <h2>Get in the Telegram group</h2>
          <p>Day-to-day questions, wins, and quick answers between calls happen here. Download Telegram if you don't have it, then tap the link to join.</p>
          <a href="{{ $telegram }}" target="_blank" rel="noopener" class="mw-cta">Join Telegram group <span class="arr">→</span></a>
        </div>
      </div>

    </div>

    <p class="mw-help">
      Stuck on any step? <a href="{{ route('contact.show') }}">Message my team</a> or email
      <a href="mailto:support@victorialovecredit.com">support@victorialovecredit.com</a> — we'll get you sorted the same day.
    </p>
  </div>
</section>

@endsection
