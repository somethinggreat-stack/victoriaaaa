<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Free 15-Min Phone Call · Victoria Love</title>
<meta name="description" content="Leave your number and pick a day &amp; time — Victoria will call you for a free 15-minute credit consultation.">
<meta name="robots" content="noindex,nofollow">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('images/companylogo.png') }}">

<style>
:root {
  --ink:   #15110f;
  --ink-2: #5a544f;
  --ink-3: #968f86;
  --bg:    #f7f3ed;
  --bg-2:  #efe8dd;
  --line:  rgba(20,16,14,0.08);
  --line-2:rgba(20,16,14,0.16);
  --pink:  #e63179;
  --pink-2:#ff7eb3;
  --pink-soft: #fdeaf2;
  --gold:  #c89a4a;
  --green: #22c55e;
  --red:   #ef4444;
}
*, *::before, *::after { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
  font-family: 'Manrope', sans-serif;
  background: var(--bg);
  color: var(--ink);
  font-size: 14.5px; line-height: 1.55;
  -webkit-font-smoothing: antialiased;
  min-height: 100vh;
}
.serif { font-family: 'Instrument Serif', serif; font-style: italic; }
a { color: inherit; text-decoration: none; }

/* === Top bar — minimal, only brand + back link === */
.sc-topbar {
  position: absolute; top: 0; left: 0; right: 0; z-index: 5;
  padding: 20px 32px;
  display: flex; align-items: center; justify-content: space-between;
}
.sc-brand { display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 700; }
.sc-brand-mark {
  width: 34px; height: 34px; border-radius: 50%; overflow: hidden;
  border: 2px solid var(--pink);
  box-shadow: 0 8px 16px -8px rgba(230,49,121,0.6);
}
.sc-brand-mark img { width: 100%; height: 100%; object-fit: cover; object-position: center 18%; display: block; }
.sc-brand-text strong { font-size: 13.5px; letter-spacing: -0.01em; display: block; color: #fff; }
.sc-brand-text small { font-size: 10.5px; color: rgba(255,255,255,0.5); letter-spacing: 0.1em; text-transform: uppercase; display: block; }
.sc-back {
  color: rgba(255,255,255,0.7); font-size: 12.5px; font-weight: 500;
  padding: 8px 14px; border-radius: 100px;
  border: 1px solid rgba(255,255,255,0.12);
  transition: background .2s, color .2s, border-color .2s;
}
.sc-back:hover { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.25); }

/* === Full split shell — dark left, form right === */
.sc-shell {
  display: grid;
  grid-template-columns: minmax(0, 420px) 1fr;
  min-height: 100vh;
}

.sc-left {
  position: relative;
  background:
    radial-gradient(70% 60% at 0% 0%, rgba(230,49,121,0.22), transparent 60%),
    radial-gradient(60% 60% at 100% 100%, rgba(200,154,74,0.14), transparent 60%),
    linear-gradient(160deg, #1f0e18 0%, #2c1622 50%, #14080d 100%);
  color: #fff;
  padding: 96px 44px 56px;
  display: flex; flex-direction: column; justify-content: space-between;
  overflow: hidden;
}
.sc-left::before {
  content: ""; position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, rgba(255,255,255,0.018) 0 2px, transparent 2px 14px);
  pointer-events: none;
}
.sc-left-body { position: relative; z-index: 1; }
.sc-step-pill {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 10.5px; font-weight: 700;
  letter-spacing: 0.16em; text-transform: uppercase;
  color: var(--pink-2);
  background: rgba(230,49,121,0.14);
  border: 1px solid rgba(230,49,121,0.35);
  padding: 7px 12px; border-radius: 100px;
  margin-bottom: 22px;
}
.sc-step-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--pink-2); box-shadow: 0 0 10px var(--pink-2); }

.sc-left h1 {
  font-size: 36px; font-weight: 600;
  margin: 0 0 14px;
  letter-spacing: -0.025em; line-height: 1.1;
  color: #fff;
}
.sc-left h1 .serif {
  background: linear-gradient(135deg, var(--pink-2), #ffd9a0);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.sc-left .lede {
  font-size: 15px; color: rgba(255,255,255,0.74);
  margin: 0 0 28px; line-height: 1.55;
  max-width: 360px;
}

.sc-bullets {
  list-style: none; padding: 0; margin: 0 0 32px;
  display: flex; flex-direction: column; gap: 12px;
}
.sc-bullets li {
  display: flex; align-items: flex-start; gap: 12px;
  font-size: 13.5px; color: rgba(255,255,255,0.86); line-height: 1.5;
}
.sc-bullets .ck {
  flex-shrink: 0;
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(34,197,94,0.18);
  color: #4ade80;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800;
  margin-top: 1px;
}

.sc-progress {
  margin-top: 14px;
  background: rgba(255,255,255,0.08);
  border-radius: 100px;
  height: 6px;
  overflow: hidden;
}
.sc-progress > span {
  display: block;
  height: 100%;
  width: 50%;
  background: linear-gradient(90deg, var(--pink), var(--pink-2));
  border-radius: 100px;
  box-shadow: 0 0 14px rgba(230,49,121,0.6);
}
.sc-progress-lab {
  display: flex; justify-content: space-between;
  margin: 8px 0 0;
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700;
  color: rgba(255,255,255,0.55);
}

.sc-victoria {
  position: relative; z-index: 1;
  display: flex; align-items: center; gap: 14px;
  padding-top: 28px;
  border-top: 1px solid rgba(255,255,255,0.08);
  margin-top: 32px;
}
.sc-victoria-img {
  width: 54px; height: 54px; border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--pink);
  flex-shrink: 0;
  box-shadow: 0 12px 24px -10px rgba(230,49,121,0.5);
}
.sc-victoria-img img { width: 100%; height: 100%; object-fit: cover; object-position: center 18%; display: block; }
.sc-victoria-text strong { display: block; font-size: 13.5px; color: #fff; }
.sc-victoria-text small  { display: block; font-size: 11.5px; color: rgba(255,255,255,0.55); margin-top: 2px; }

/* === Right side — the form itself === */
.sc-right {
  padding: 96px 56px 64px;
  display: flex; align-items: flex-start; justify-content: center;
  background: var(--bg);
}
.sc-form-wrap {
  width: 100%; max-width: 640px;
}

.sc-alert {
  margin-bottom: 22px;
  padding: 14px 18px;
  border-radius: 12px;
  font-size: 13.5px;
  background: #fff3f3;
  border: 1px solid #fbd6d6;
  color: #8b1414;
}
.sc-alert strong { display: block; margin-bottom: 6px; }
.sc-alert ul { margin: 4px 0 0; padding-left: 18px; }

.sc-form-head { margin-bottom: 28px; }
.sc-form-head h2 {
  font-size: 26px; font-weight: 600; letter-spacing: -0.02em;
  margin: 0 0 6px; color: var(--ink);
}
.sc-form-head h2 em { background: linear-gradient(135deg, var(--pink), var(--gold)); -webkit-background-clip: text; background-clip: text; color: transparent; }
.sc-form-head p { margin: 0; color: var(--ink-2); font-size: 13.5px; }

form.sc-form { display: flex; flex-direction: column; gap: 18px; }

.sc-section-lab {
  font-size: 10.5px; letter-spacing: 0.18em; text-transform: uppercase; font-weight: 800;
  color: var(--pink); margin: 18px 0 -4px;
}

.sc-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.sc-field { display: flex; flex-direction: column; gap: 6px; }
.sc-lab {
  font-size: 11.5px; font-weight: 700; color: var(--ink-2);
  letter-spacing: 0.08em; text-transform: uppercase;
}
.sc-lab em { color: var(--pink); font-style: normal; }
.sc-lab small { color: var(--ink-3); font-weight: 500; text-transform: none; letter-spacing: 0; margin-left: 4px; }

.sc-field input,
.sc-field select,
.sc-field textarea {
  width: 100%;
  padding: 14px 16px;
  font-size: 14.5px;
  font-family: inherit;
  color: var(--ink);
  background: #fff;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  transition: border-color .15s, box-shadow .15s, background .15s;
}
.sc-field input::placeholder, .sc-field textarea::placeholder { color: var(--ink-3); }
.sc-field input:focus,
.sc-field select:focus,
.sc-field textarea:focus {
  outline: none;
  border-color: var(--pink);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.sc-field textarea { resize: vertical; min-height: 110px; line-height: 1.55; }
.sc-field select {
  appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path d='M2 4l4 4 4-4' stroke='%2315110f' stroke-width='1.6' fill='none' stroke-linecap='round'/></svg>");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}

.sc-callout {
  background: linear-gradient(135deg, var(--pink-soft), #fff8fb);
  border: 1px solid var(--pink-soft);
  border-left: 3px solid var(--pink);
  border-radius: 12px;
  padding: 16px 18px;
  font-size: 13px; color: var(--ink-2);
  line-height: 1.6;
}
.sc-callout strong { color: var(--ink); display: block; margin-bottom: 4px; font-size: 13.5px; }
.sc-callout em { font-style: italic; color: var(--pink); font-weight: 600; }

.sc-check {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
  transition: border-color .15s, background .15s;
}
.sc-check:hover { border-color: var(--pink); }
.sc-check input[type="checkbox"] {
  margin-top: 3px;
  width: 18px; height: 18px;
  accent-color: var(--pink);
  flex-shrink: 0;
}
.sc-check span { font-size: 13.5px; color: var(--ink-2); line-height: 1.5; }
.sc-check span strong { color: var(--ink); font-weight: 700; }

.sc-submit {
  margin-top: 10px;
  padding: 16px 28px;
  background: var(--pink); color: #fff;
  border: none; border-radius: 100px;
  font-family: inherit; font-size: 15px; font-weight: 700;
  cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  box-shadow: 0 18px 36px -14px rgba(230,49,121,0.6);
  transition: background .15s, transform .15s, box-shadow .15s;
}
.sc-submit:hover { background: var(--ink); transform: translateY(-2px); box-shadow: 0 22px 44px -16px rgba(20,16,14,0.4); }
.sc-submit .arr { transition: transform .15s; }
.sc-submit:hover .arr { transform: translateX(4px); }

.sc-fine {
  font-size: 11.5px; color: var(--ink-3);
  margin: 6px 0 0; text-align: center;
}

/* === Tablet === */
@media (max-width: 960px) {
  .sc-shell { grid-template-columns: 1fr; min-height: 0; }
  .sc-left { padding: 76px 28px 36px; }
  .sc-left h1 { font-size: 28px; }
  .sc-left .lede { max-width: none; }
  .sc-right { padding: 36px 28px 56px; }
  .sc-bullets { margin-bottom: 22px; }
  .sc-victoria { margin-top: 22px; padding-top: 22px; }
}

/* === Phone === */
@media (max-width: 560px) {
  .sc-topbar { padding: 14px 18px; }
  .sc-brand-text small { display: none; }
  .sc-back { padding: 7px 12px; font-size: 12px; }
  .sc-left { padding: 70px 22px 30px; }
  .sc-left h1 { font-size: 24px; }
  .sc-right { padding: 28px 20px 48px; }
  .sc-row { grid-template-columns: 1fr; gap: 12px; }
  .sc-form-head h2 { font-size: 22px; }
}
</style>
</head>
<body>

<header class="sc-topbar">
  <a href="{{ url('/') }}" class="sc-brand">
    <span class="sc-brand-mark"><img src="{{ asset('images/founderimage4.jpeg') }}" alt=""></span>
    <span class="sc-brand-text">
      <strong>Victoria Love</strong>
      <small>Texas Realtor · Credit Coach</small>
    </span>
  </a>
  <a href="{{ url('/') }}" class="sc-back">← Back to site</a>
</header>

<div class="sc-shell">

  <!-- LEFT — promise + trust -->
  <aside class="sc-left">
    <div class="sc-left-body">
      <span class="sc-step-pill"><span class="dot"></span> Free 15-min phone call</span>
      <h1>Leave your number &amp; <span class="serif">I'll call you.</span></h1>
      <p class="lede">Tell me the best day and time, and I'll personally call your phone for a free 15-minute credit consultation. No Zoom, no card, no pressure.</p>

      <ul class="sc-bullets">
        <li><span class="ck">✓</span><span>A real 15-min phone call with Victoria</span></li>
        <li><span class="ck">✓</span><span>We go over your credit and your options</span></li>
        <li><span class="ck">✓</span><span>You leave with a clear next step — even if you never hire me</span></li>
        <li><span class="ck">✓</span><span>1,000+ files cleaned · +147 avg gain</span></li>
      </ul>
    </div>

    <div class="sc-victoria">
      <span class="sc-victoria-img"><img src="{{ asset('images/founderimage7.jpeg') }}" alt=""></span>
      <div class="sc-victoria-text">
        <strong>Victoria calls you personally</strong>
        <small>At the day &amp; time you pick.</small>
      </div>
    </div>
  </aside>

  <!-- RIGHT — the form -->
  <main class="sc-right">
    <div class="sc-form-wrap">

      <div class="sc-form-head">
        <h2>Request your <em>free call.</em></h2>
        <p>Leave your number and pick a day &amp; time — I'll call your phone personally. It goes straight to me.</p>
      </div>

      @if ($errors->any())
        <div class="sc-alert" role="alert">
          <strong>A couple of fields still need attention:</strong>
          <ul>
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="sc-callout" style="margin-bottom:22px;">
        <strong>📞 When I'm available</strong>
        Free 15-minute phone calls, <em>Monday–Friday, 11:00 AM – 3:00 PM Central.</em> Pick a slot below and I'll call you.
      </div>

      <form id="strategyCallForm" class="sc-form" method="POST" action="{{ route('strategy-call.submit') }}" autocomplete="on" novalidate>
        @csrf

        <div class="sc-section-lab">Your details</div>

        <div class="sc-row">
          <label class="sc-field">
            <span class="sc-lab">Your name <em>*</em></span>
            <input type="text" name="name" required maxlength="120" value="{{ old('name') }}" placeholder="Jane Smith" autocomplete="name" />
          </label>
          <label class="sc-field">
            <span class="sc-lab">Phone <em>*</em> <small>(I'll call this number)</small></span>
            <input type="tel" name="phone" required maxlength="30" value="{{ old('phone') }}" placeholder="(555) 123-4567" autocomplete="tel" />
          </label>
        </div>

        <label class="sc-field">
          <span class="sc-lab">Email <em>*</em></span>
          <input type="email" name="email" required maxlength="255" value="{{ old('email') }}" placeholder="you@email.com" autocomplete="email" />
        </label>

        <div class="sc-section-lab">When should I call you? <small style="text-transform:none;letter-spacing:0;color:var(--ink-3);font-weight:500">· Mon–Fri, 11am–3pm CT</small></div>

        <div class="sc-row">
          <label class="sc-field">
            <span class="sc-lab">Preferred day <em>*</em></span>
            <select name="preferred_day" required autocomplete="off">
              <option value="">Pick a day</option>
              @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday'] as $d)
                <option value="{{ $d }}" @selected(old('preferred_day')===$d)>{{ $d }}</option>
              @endforeach
            </select>
          </label>
          <label class="sc-field">
            <span class="sc-lab">Preferred time (CT) <em>*</em></span>
            <select name="preferred_time" required autocomplete="off">
              <option value="">Pick a time</option>
              @foreach (['11:00 AM','11:30 AM','12:00 PM','12:30 PM','1:00 PM','1:30 PM','2:00 PM','2:30 PM','3:00 PM'] as $t)
                <option value="{{ $t }}" @selected(old('preferred_time')===$t)>{{ $t }}</option>
              @endforeach
            </select>
          </label>
        </div>

        <label class="sc-field">
          <span class="sc-lab">What do you need help with? <small>(optional)</small></span>
          <textarea name="goal" rows="3" maxlength="2000" placeholder="A quick note on your situation or goal — totally optional.">{{ old('goal') }}</textarea>
        </label>

        <button type="submit" class="sc-submit">
          Request my free call <span class="arr">→</span>
        </button>

        <p class="sc-fine">By submitting you agree to be contacted by phone or email at the time you selected. Your info stays private.</p>
      </form>

    </div>
  </main>

</div>

</body>
</html>
