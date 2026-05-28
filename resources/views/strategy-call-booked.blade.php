<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pick your time · Victoria Love</title>
<meta name="description" content="Pick a 15-minute slot on Victoria's calendar.">
<meta name="robots" content="noindex,nofollow">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://assets.calendly.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('images/companylogo.png') }}">

<style>
:root {
  --ink:   #15110f; --ink-2: #5a544f; --ink-3: #968f86;
  --bg:    #f7f3ed; --bg-2: #efe8dd;
  --line:  rgba(20,16,14,0.08);
  --pink:  #e63179; --pink-2: #ff7eb3;
  --gold:  #c89a4a;
  --green: #22c55e;
}
*, *::before, *::after { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body { font-family: 'Manrope', sans-serif; background: var(--bg); color: var(--ink); font-size: 14.5px; line-height: 1.55; -webkit-font-smoothing: antialiased; min-height: 100vh; }
.serif { font-family: 'Instrument Serif', serif; font-style: italic; }
a { color: inherit; text-decoration: none; }

.sc-topbar { position: absolute; top: 0; left: 0; right: 0; z-index: 5; padding: 20px 32px; display: flex; align-items: center; justify-content: space-between; }
.sc-brand { display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 700; }
.sc-brand-mark { width: 34px; height: 34px; border-radius: 50%; overflow: hidden; border: 2px solid var(--pink); box-shadow: 0 8px 16px -8px rgba(230,49,121,0.6); }
.sc-brand-mark img { width: 100%; height: 100%; object-fit: cover; object-position: center 18%; display: block; }
.sc-brand-text strong { font-size: 13.5px; display: block; color: #fff; letter-spacing: -0.01em; }
.sc-brand-text small  { font-size: 10.5px; color: rgba(255,255,255,0.5); letter-spacing: 0.1em; text-transform: uppercase; display: block; }
.sc-back { color: rgba(255,255,255,0.7); font-size: 12.5px; font-weight: 500; padding: 8px 14px; border-radius: 100px; border: 1px solid rgba(255,255,255,0.12); transition: background .2s, color .2s, border-color .2s; }
.sc-back:hover { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.25); }

.sc-shell { display: grid; grid-template-columns: minmax(0, 420px) 1fr; min-height: 100vh; }

.sc-left {
  position: relative;
  background:
    radial-gradient(70% 60% at 0% 0%, rgba(34,197,94,0.18), transparent 60%),
    radial-gradient(60% 60% at 100% 100%, rgba(200,154,74,0.14), transparent 60%),
    linear-gradient(160deg, #0f1812 0%, #16221a 50%, #0a1310 100%);
  color: #fff;
  padding: 96px 44px 56px;
  display: flex; flex-direction: column; justify-content: space-between;
  overflow: hidden;
}
.sc-left::before { content: ""; position: absolute; inset: 0; background: repeating-linear-gradient(45deg, rgba(255,255,255,0.018) 0 2px, transparent 2px 14px); pointer-events: none; }
.sc-left-body { position: relative; z-index: 1; }

.sc-step-pill {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 10.5px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase;
  color: #4ade80;
  background: rgba(34,197,94,0.14);
  border: 1px solid rgba(34,197,94,0.35);
  padding: 7px 12px; border-radius: 100px;
  margin-bottom: 22px;
}
.sc-step-pill .ck { font-size: 12px; }

.sc-left h1 { font-size: 36px; font-weight: 600; margin: 0 0 14px; letter-spacing: -0.025em; line-height: 1.1; color: #fff; }
.sc-left h1 .serif { background: linear-gradient(135deg, var(--pink-2), #ffd9a0); -webkit-background-clip: text; background-clip: text; color: transparent; }
.sc-left .lede { font-size: 15px; color: rgba(255,255,255,0.74); margin: 0 0 28px; line-height: 1.55; max-width: 360px; }

.sc-bullets { list-style: none; padding: 0; margin: 0 0 32px; display: flex; flex-direction: column; gap: 12px; }
.sc-bullets li { display: flex; align-items: flex-start; gap: 12px; font-size: 13.5px; color: rgba(255,255,255,0.86); line-height: 1.5; }
.sc-bullets .ck { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; background: rgba(34,197,94,0.18); color: #4ade80; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; margin-top: 1px; }

.sc-progress { margin-top: 14px; background: rgba(255,255,255,0.08); border-radius: 100px; height: 6px; overflow: hidden; }
.sc-progress > span { display: block; height: 100%; width: 100%; background: linear-gradient(90deg, #22c55e, #4ade80); border-radius: 100px; box-shadow: 0 0 14px rgba(34,197,94,0.6); }
.sc-progress-lab { display: flex; justify-content: space-between; margin: 8px 0 0; font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; color: rgba(255,255,255,0.55); }

.sc-victoria { position: relative; z-index: 1; display: flex; align-items: center; gap: 14px; padding-top: 28px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 32px; }
.sc-victoria-img { width: 54px; height: 54px; border-radius: 50%; overflow: hidden; border: 2px solid var(--pink); flex-shrink: 0; box-shadow: 0 12px 24px -10px rgba(230,49,121,0.5); }
.sc-victoria-img img { width: 100%; height: 100%; object-fit: cover; object-position: center 18%; display: block; }
.sc-victoria-text strong { display: block; font-size: 13.5px; color: #fff; }
.sc-victoria-text small  { display: block; font-size: 11.5px; color: rgba(255,255,255,0.55); margin-top: 2px; }

.sc-right { padding: 96px 56px 64px; display: flex; align-items: flex-start; justify-content: center; background: var(--bg); }
.sc-cal-wrap { width: 100%; max-width: 760px; }
.sc-cal-head { margin-bottom: 18px; }
.sc-cal-head h2 { margin: 0 0 4px; font-size: 22px; font-weight: 600; letter-spacing: -0.02em; }
.sc-cal-head p { margin: 0; color: var(--ink-2); font-size: 13.5px; }
.sc-cal-card { background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 18px; box-shadow: 0 30px 60px -30px rgba(20,16,14,0.18); }
.sc-cal-foot { text-align: center; margin-top: 18px; font-size: 12.5px; color: var(--ink-3); }
.sc-cal-foot a { color: var(--pink); font-weight: 700; }

@media (max-width: 960px) {
  .sc-shell { grid-template-columns: 1fr; min-height: 0; }
  .sc-left { padding: 76px 28px 36px; }
  .sc-left h1 { font-size: 28px; }
  .sc-right { padding: 36px 28px 56px; }
}
@media (max-width: 560px) {
  .sc-topbar { padding: 14px 18px; }
  .sc-brand-text small { display: none; }
  .sc-back { padding: 7px 12px; font-size: 12px; }
  .sc-left { padding: 70px 22px 30px; }
  .sc-left h1 { font-size: 24px; }
  .sc-right { padding: 28px 20px 48px; }
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

  <aside class="sc-left">
    <div class="sc-left-body">
      <span class="sc-step-pill"><span class="ck">✓</span> Step 2 of 2 · Pick a time</span>
      <h1>Form received{{ $leadName ? ', '.e(explode(' ', $leadName)[0]) : '' }} — pick your <span class="serif">slot.</span></h1>
      <p class="lede">I'll see your answers before we hop on Zoom. Now grab any open 15-minute slot that works for you.</p>

      <ul class="sc-bullets">
        <li><span class="ck">✓</span><span>Your file is on my list — I'll prep before we meet</span></li>
        <li><span class="ck">✓</span><span>You'll get a Zoom link and reminders by email + text</span></li>
        <li><span class="ck">✓</span><span>Need to reschedule? Use the link in your confirmation</span></li>
      </ul>

      <div class="sc-progress" aria-hidden="true"><span></span></div>
      <p class="sc-progress-lab"><span>✓ Step 1 · Done</span><span>Step 2 · Pick a time</span></p>
    </div>

    <div class="sc-victoria">
      <span class="sc-victoria-img"><img src="{{ asset('images/founderimage7.jpeg') }}" alt=""></span>
      <div class="sc-victoria-text">
        <strong>See you on Zoom</strong>
        <small>— Victoria</small>
      </div>
    </div>
  </aside>

  <main class="sc-right">
    <div class="sc-cal-wrap">

      <div class="sc-cal-head">
        <h2>Pick a 15-minute window</h2>
        <p>Times below are in your local timezone.</p>
      </div>

      <div class="sc-cal-card">
        <div class="calendly-inline-widget"
             data-url="{{ $calendlyUrl }}?hide_event_type_details=0&hide_gdpr_banner=1"
             style="min-width:320px;height:720px;"></div>
      </div>

      <p class="sc-cal-foot">
        Calendar not loading? <a href="{{ $calendlyUrl }}" target="_blank" rel="noopener">Open it in a new tab →</a>
      </p>

    </div>
  </main>

</div>

<script src="https://assets.calendly.com/assets/external/widget.js" async></script>
</body>
</html>
