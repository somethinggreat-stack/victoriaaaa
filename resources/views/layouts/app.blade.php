<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title', 'Victoria Love — Fix Your Credit. Own Your Home. Build Wealth.')</title>
<meta name="description" content="@yield('description', 'Texas Realtor & Credit Coach. Raise your score 100+ points, unlock $100K in funding, and close on your first home in 90 days. Free 15-min strategy call.')" />
<link rel="icon" type="image/png" href="{{ asset('images/companylogo.png') }}" />
<link rel="apple-touch-icon" href="{{ asset('images/companylogo.png') }}" />

{{-- Resource hints for performance --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="dns-prefetch" href="https://assets.calendly.com">

{{-- Drop unused Manrope weights (only 400/500/600/700 are actually used) --}}
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" media="all">

@hasSection('lcp_preload')
  @yield('lcp_preload')
@else
  {{-- Preload homepage hero portrait by default for LCP --}}
  @if (request()->path() === '/' || request()->path() === '')
    <link rel="preload" as="image" href="{{ asset('images/founderimage7.jpeg') }}" fetchpriority="high">
  @endif
@endif

<style>
/* ===============================================================
   VICTORIA LOVE — v4 LIGHT, PREMIUM, CONVERSION-FOCUSED
   =============================================================== */

:root{
  --bg:        #faf7f2;          /* warm cream */
  --bg-2:      #f3ede4;          /* deeper cream */
  --bg-3:      #ffffff;          /* card surface */
  --bg-dark:   #150c10;          /* dramatic dark accent */

  --ink:       #15110f;          /* warm near-black */
  --ink-2:     #5a544f;          /* secondary text */
  --ink-3:     #968f86;          /* tertiary / labels */
  --line:      rgba(20,16,14,0.08);
  --line-2:    rgba(20,16,14,0.16);

  --pink:      #e63179;          /* brand */
  --pink-2:    #ff7eb3;
  --pink-soft: #fdeaf2;
  --pink-tint: #fff5f9;

  --gold:      #c89a4a;          /* brand */
  --gold-2:    #e6c481;
  --gold-soft: #faf0db;

  --grad-warm: linear-gradient(135deg, #e63179 0%, #ff7eb3 50%, #e6c481 100%);
  --grad-soft: linear-gradient(135deg, #fdeaf2 0%, #faf0db 100%);

  --r-xs: 8px;
  --r-sm: 14px;
  --r-md: 20px;
  --r-lg: 28px;
  --r-xl: 40px;

  --container: 1240px;
  --ease: cubic-bezier(.2, 1, .3, 1);
}

* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  font-weight: 400;
  background: var(--bg);
  color: var(--ink);
  line-height: 1.6;
  letter-spacing: -0.005em;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  overflow-x: hidden;
}
img { max-width: 100%; display: block; }
a { color: inherit; text-decoration: none; }
button { font-family: inherit; cursor: pointer; border: 0; background: none; color: inherit; }

::selection { background: var(--pink); color: #fff; }

/* ===== Soft warm background gradients (subtle) ===== */
body::before {
  content: ""; position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background:
    radial-gradient(60% 50% at 0% 0%, rgba(230,49,121,0.06), transparent 60%),
    radial-gradient(60% 50% at 100% 30%, rgba(200,154,74,0.05), transparent 60%);
}

/* ===== Typography ===== */
.serif { font-family: 'Instrument Serif', serif; font-style: italic; font-weight: 400; letter-spacing: -0.015em; }

h1, h2, h3, h4, h5 {
  font-family: 'Manrope', sans-serif;
  font-weight: 600;
  letter-spacing: -0.035em;
  line-height: 1.05;
  color: var(--ink);
}
h1 { font-size: clamp(2.6rem, 5.4vw, 4.6rem); font-weight: 500; line-height: 1; letter-spacing: -0.04em; }
h2 { font-size: clamp(2rem, 4vw, 3.4rem); font-weight: 500; line-height: 1.05; letter-spacing: -0.035em; }
h3 { font-size: clamp(1.3rem, 2vw, 1.6rem); font-weight: 600; }
h4 { font-size: 1.05rem; font-weight: 600; }

p  { color: var(--ink-2); font-size: 17px; line-height: 1.65; }
p.small { font-size: 15px; }

.eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-family: 'Manrope', sans-serif; font-size: 12px; font-weight: 600;
  letter-spacing: 0.18em; text-transform: uppercase; color: var(--pink);
}
.eyebrow::before {
  content: ""; width: 22px; height: 1px; background: var(--pink);
}

.gradient-text {
  background: var(--grad-warm);
  -webkit-background-clip: text; background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* ===== Layout ===== */
.container { max-width: var(--container); margin: 0 auto; padding: 0 28px; position: relative; z-index: 2; }
section { position: relative; padding: 110px 0; z-index: 2; }
.section-head { max-width: 720px; margin: 0 auto 56px; text-align: center; }
.section-head .eyebrow { margin-bottom: 18px; }
.section-head h2 { margin: 0 0 18px; }
.section-head p { max-width: 560px; margin: 0 auto; }

/* ===== Buttons ===== */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 10px;
  padding: 17px 28px; border-radius: 999px;
  font-weight: 600; font-size: 15px; letter-spacing: -0.005em;
  transition: transform .25s var(--ease), background .25s, color .25s, box-shadow .25s, border-color .25s;
  position: relative; overflow: hidden;
  border: 1px solid transparent;
  white-space: nowrap;
}
.btn-primary {
  background: var(--ink); color: #fff;
  box-shadow: 0 12px 30px -12px rgba(20,16,14,0.5);
}
.btn-primary:hover {
  background: var(--pink); color: #fff;
  box-shadow: 0 18px 40px -10px rgba(230,49,121,0.55);
  transform: translateY(-2px);
}
.btn-pink {
  background: var(--pink); color: #fff;
  box-shadow: 0 12px 30px -10px rgba(230,49,121,0.55);
}
.btn-pink:hover {
  background: var(--ink); color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 18px 40px -10px rgba(20,16,14,0.5);
}
.btn-ghost {
  border: 1.5px solid var(--ink); color: var(--ink); background: transparent;
}
.btn-ghost:hover { background: var(--ink); color: #fff; transform: translateY(-2px); }
.btn-ghost-light {
  border: 1.5px solid rgba(255,255,255,0.4); color: #fff; background: transparent;
}
.btn-ghost-light:hover { background: #fff; color: var(--ink); }
.btn .arr { transition: transform .25s; display: inline-flex; }
.btn:hover .arr { transform: translateX(4px); }

/* ===== NAV ===== */
.nav {
  position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
  z-index: 100; width: calc(100% - 32px); max-width: 1200px;
  display: flex; justify-content: space-between; align-items: center;
  padding: 8px 10px 8px 18px;
  border-radius: 100px;
  background: rgba(255,255,255,0.78);
  backdrop-filter: blur(20px) saturate(140%);
  -webkit-backdrop-filter: blur(20px) saturate(140%);
  border: 1px solid var(--line);
  box-shadow: 0 8px 30px -10px rgba(20,16,14,0.08);
  transition: padding .3s, top .3s, box-shadow .3s;
}
.nav.scrolled { padding: 6px 8px 6px 16px; box-shadow: 0 12px 36px -12px rgba(20,16,14,0.14); }
.logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 15px; letter-spacing: -0.02em; flex-shrink: 0; }
.logo img { height: 36px; width: auto; display: block; transition: transform .25s; }
.logo:hover img { transform: scale(1.05); }
.nav-links { display: flex; gap: 2px; }
.nav-links a {
  padding: 9px 16px; border-radius: 100px;
  font-size: 13.5px; font-weight: 500; color: var(--ink-2);
  transition: color .2s, background .2s;
}
.nav-links a:hover { color: var(--ink); background: rgba(20,16,14,0.05); }
.nav-cta {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 11px 20px; border-radius: 100px; font-size: 13.5px; font-weight: 600;
  background: var(--pink); color: #fff;
  transition: background .2s, transform .2s, box-shadow .2s;
  box-shadow: 0 8px 20px -8px rgba(230,49,121,0.55);
  flex-shrink: 0;
}
.nav-cta:hover { background: var(--ink); transform: translateY(-1px); box-shadow: 0 12px 24px -8px rgba(20,16,14,0.45); }
.nav-cta .arr { transition: transform .2s; }
.nav-cta:hover .arr { transform: translateX(3px); }

.menu-btn {
  display: none; width: 42px; height: 42px; border-radius: 50%;
  background: var(--ink); color: #fff; align-items: center; justify-content: center;
  flex-shrink: 0; padding: 0; cursor: pointer;
  transition: background .25s, transform .25s;
}
.menu-btn:hover { background: var(--pink); transform: scale(1.05); }
.menu-btn span { width: 16px; height: 1.5px; background: #fff; position: relative; transition: .3s; display: block; }
.menu-btn span::before, .menu-btn span::after { content:""; position:absolute; left:0; width:100%; height:1.5px; background:#fff; transition:.3s; }
.menu-btn span::before { top: -5px; } .menu-btn span::after { top: 5px; }
.menu-btn.open span { background: transparent; }
.menu-btn.open span::before { top: 0; transform: rotate(45deg); }
.menu-btn.open span::after { top: 0; transform: rotate(-45deg); }
@media (max-width: 1080px) { .nav-links, .nav-cta { display: none; } .menu-btn { display: flex; } }

.nav-mobile {
  position: fixed; inset: 0; z-index: 99;
  background: var(--bg);
  display: flex; flex-direction: column;
  align-items: stretch; justify-content: flex-start;
  gap: 2px;
  transform: translateY(-100%);
  transition: transform .5s cubic-bezier(.2,.7,.2,1);
  padding: 92px 24px 40px;
  padding-top: max(92px, calc(env(safe-area-inset-top, 0px) + 80px));
  padding-bottom: max(40px, env(safe-area-inset-bottom, 0px));
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}
.nav-mobile.open { transform: translateY(0); }
.nav-mobile a {
  font-size: 26px;
  font-weight: 500;
  color: var(--ink);
  letter-spacing: -0.02em;
  padding: 14px 4px;
  border-bottom: 1px solid var(--line);
  display: flex; align-items: center; justify-content: space-between;
}
.nav-mobile a::after {
  content: "→";
  font-size: 18px; color: var(--ink-3);
  transition: transform .2s, color .2s;
}
.nav-mobile a:hover::after,
.nav-mobile a:active::after { color: var(--pink); transform: translateX(4px); }
.nav-mobile a.cta {
  margin-top: 20px;
  background: var(--pink); color: #fff;
  padding: 18px 28px;
  border-radius: 100px;
  font-size: 17px; font-weight: 600;
  text-align: center;
  border: none;
  justify-content: center;
  box-shadow: 0 14px 30px -10px rgba(230,49,121,0.55);
}
.nav-mobile a.cta::after { color: #fff; }
.nav-mobile a.cta:hover::after { color: #fff; }

/* ===== MOBILE HEADER REFINEMENTS ===== */
@media (max-width: 700px) {
  .nav {
    top: 10px;
    width: calc(100% - 20px);
    padding: 6px 6px 6px 14px;
  }
  .nav.scrolled { top: 8px; padding: 5px 5px 5px 12px; }
  .logo img { height: 30px; }
  .menu-btn { width: 38px; height: 38px; }
  .menu-btn span { width: 14px; }
}
@media (max-width: 380px) {
  .nav { padding: 5px 5px 5px 12px; }
  .logo img { height: 26px; }
  .menu-btn { width: 36px; height: 36px; }
  .nav-mobile a { font-size: 22px; padding: 12px 4px; }
}

/* ===== HERO ===== */
.hero {
  padding: 140px 0 80px;
  position: relative;
  overflow: hidden;
}
.hero::before {
  content: ""; position: absolute; top: -100px; right: -200px;
  width: 700px; height: 700px; border-radius: 50%;
  background: radial-gradient(circle, rgba(230,49,121,0.10), transparent 70%);
  z-index: 0;
}
.hero::after {
  content: ""; position: absolute; bottom: -200px; left: -200px;
  width: 600px; height: 600px; border-radius: 50%;
  background: radial-gradient(circle, rgba(200,154,74,0.10), transparent 70%);
  z-index: 0;
}
.hero-grid {
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  gap: 80px;
  align-items: center;
}
.hero-text { min-width: 0; }
.hero-pill {
  display: inline-flex; align-items: center; gap: 12px;
  padding: 7px 16px 7px 7px; border-radius: 100px;
  background: #fff; border: 1px solid var(--line);
  font-size: 13px; color: var(--ink-2); margin-bottom: 30px;
  box-shadow: 0 4px 14px -6px rgba(20,16,14,0.08);
}
.hero-pill .dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #22c55e; box-shadow: 0 0 0 4px rgba(34,197,94,0.18);
  animation: pulse 1.8s infinite;
}
.hero-pill .star {
  display: inline-flex; align-items: center; gap: 5px;
  background: var(--ink); color: #fff;
  padding: 4px 9px; border-radius: 100px; font-size: 11px; font-weight: 600; letter-spacing: 0.04em;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

.hero h1 { margin-bottom: 26px; }
.hero h1 .pink { color: var(--pink); }
.hero h1 em.serif { color: var(--ink); }
.hero .lede {
  font-size: 19px; line-height: 1.55; max-width: 520px; margin-bottom: 36px;
}
.hero .lede strong { color: var(--ink); font-weight: 600; }
.hero-ctas { display: flex; gap: 12px; flex-wrap: wrap; }
.hero-meta {
  margin-top: 40px;
  display: flex; align-items: center; gap: 18px;
  flex-wrap: wrap;
}
.hero-meta .avs { display: flex; }
.hero-meta .avs img {
  width: 38px; height: 38px; border-radius: 50%; object-fit: cover; object-position: center 18%;
  border: 2px solid #fff; margin-left: -10px;
  box-shadow: 0 4px 10px -2px rgba(20,16,14,0.15);
}
.hero-meta .avs img:first-child { margin-left: 0; }
.hero-meta .avs-text strong { color: var(--ink); font-weight: 700; }
.hero-meta .avs-text { font-size: 13.5px; color: var(--ink-2); line-height: 1.35; }
.hero-meta .stars { color: var(--gold); letter-spacing: 2px; font-size: 13px; }

/* Hero portrait stage */
.hero-stage {
  position: relative;
  aspect-ratio: 4/5;
  max-width: 520px;
  margin-left: auto;
}
.hero-portrait {
  position: relative;
  width: 100%; height: 100%;
  border-radius: var(--r-xl);
  overflow: hidden;
  background: var(--bg-dark);
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.35),
              0 20px 40px -20px rgba(230,49,121,0.25);
  z-index: 2;
}
.hero-portrait img {
  width: 100%; height: 100%; object-fit: cover; object-position: center 12%;
  filter: contrast(1.04) saturate(1.05);
}
.hero-portrait::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(20,12,16,0.45) 100%);
}
.hero-portrait .hp-tag {
  position: absolute; top: 22px; left: 22px; z-index: 3;
  display: inline-flex; align-items: center; gap: 8px;
  padding: 7px 13px; border-radius: 100px;
  background: rgba(255,255,255,0.96); color: var(--ink);
  font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase;
  box-shadow: 0 6px 16px -4px rgba(0,0,0,0.2);
}
.hero-portrait .hp-tag::before {
  content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--pink);
}

/* Decorative shape behind portrait */
.hero-stage::before {
  content: ""; position: absolute;
  inset: -16px -16px 16px 16px;
  border-radius: var(--r-xl);
  background: var(--grad-warm);
  z-index: 1;
  opacity: 0.85;
  filter: blur(2px);
}

/* Floating result chips */
.fly {
  position: absolute; z-index: 5;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 18px;
  padding: 14px 18px;
  display: flex; align-items: center; gap: 12px;
  box-shadow: 0 20px 40px -10px rgba(20,16,14,0.18);
}
.fly.left {
  left: -38px; top: 18%;
  animation: floatA 5s ease-in-out infinite alternate;
}
.fly.right {
  right: -42px; bottom: 22%;
  animation: floatB 5.5s ease-in-out infinite alternate;
}
.fly .icon {
  width: 38px; height: 38px; border-radius: 12px;
  display: grid; place-items: center;
  font-size: 15px; font-weight: 700; color: #fff;
}
.fly.left .icon { background: var(--grad-warm); box-shadow: 0 8px 16px -4px rgba(230,49,121,0.4); }
.fly.right .icon { background: var(--ink); }
.fly .lab { font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px; }
.fly .val { font-size: 17px; font-weight: 700; color: var(--ink); letter-spacing: -0.01em; }
@keyframes floatA { 0%{transform:translateY(0) rotate(-1deg)} 100%{transform:translateY(-12px) rotate(1deg)} }
@keyframes floatB { 0%{transform:translateY(0) rotate(1deg)} 100%{transform:translateY(-10px) rotate(-1deg)} }

@media (max-width: 1100px) {
  .hero-grid { grid-template-columns: 1fr; gap: 60px; }
  .hero-stage { margin: 0 auto; }
  .hero-stage::before { inset: -10px -10px 10px 10px; }
  .fly.left { left: 10px; }
  .fly.right { right: 14px; }
}
@media (max-width: 600px) {
  .hero { padding: 100px 0 50px; }
  .hero h1 { font-size: 2.4rem; }
  .hero .lede { font-size: 17px; }
  .fly { padding: 10px 14px; border-radius: 14px; }
  .fly .icon { width: 32px; height: 32px; }
  .fly .val { font-size: 14px; }
  .fly .lab { font-size: 9.5px; }
  .hero-grid { gap: 40px; }
}
@media (max-width: 380px) {
  .hero { padding: 90px 0 40px; }
  .hero h1 { font-size: 2rem; line-height: 1.05; }
}

/* ===== TRUST BAR ===== */
.trust-bar {
  background: #fff;
  border-block: 1px solid var(--line);
  padding: 28px 0;
  position: relative; z-index: 3;
}
.trust-inner {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 50px;
  align-items: center;
}
.trust-label {
  font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--ink-3); font-weight: 600;
  max-width: 130px; line-height: 1.4;
}
.trust-list {
  display: flex; align-items: center; gap: 50px; flex-wrap: wrap;
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: clamp(20px, 1.7vw, 26px);
  color: var(--ink); opacity: 0.7;
}
.trust-list .sep {
  width: 5px; height: 5px; border-radius: 50%; background: var(--pink); flex-shrink: 0; opacity: 0.7;
}
@media (max-width: 700px) {
  .trust-inner { grid-template-columns: 1fr; gap: 16px; }
  .trust-list { gap: 22px; font-size: 18px; justify-content: center; }
}

/* ===== STATS STRIP ===== */
.stats-strip { padding: 80px 0 30px; }
.stats-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
}
.stat {
  background: #fff; border: 1px solid var(--line); border-radius: var(--r-md);
  padding: 28px 24px;
  transition: transform .3s, box-shadow .3s, border-color .3s;
}
.stat:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -16px rgba(20,16,14,0.12); border-color: var(--line-2); }
.stat .num {
  font-size: 44px; font-weight: 600; letter-spacing: -0.04em; line-height: 1;
  color: var(--ink);
  margin-bottom: 6px;
}
.stat.feat { background: var(--ink); color: #fff; border-color: var(--ink); }
.stat.feat .num { color: var(--pink-2); }
.stat.feat .lab { color: rgba(255,255,255,0.7); }
.stat .lab { font-size: 13px; color: var(--ink-2); }
@media (max-width: 800px) { .stats-grid { grid-template-columns: 1fr 1fr; } }

/* ===== PROMISE — pain → solution 3-card ===== */
.promise-section { padding: 110px 0; }
.promise-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.promise {
  position: relative;
  padding: 36px 30px;
  background: #fff; border: 1px solid var(--line); border-radius: var(--r-lg);
  transition: transform .3s, box-shadow .3s, border-color .3s;
}
.promise:hover {
  transform: translateY(-6px);
  box-shadow: 0 30px 60px -25px rgba(20,16,14,0.18);
  border-color: var(--line-2);
}
.promise .ico {
  width: 52px; height: 52px; border-radius: 14px;
  background: var(--pink-soft); color: var(--pink);
  display: grid; place-items: center; margin-bottom: 22px;
  font-size: 22px; font-weight: 700;
}
.promise:nth-child(2) .ico { background: var(--gold-soft); color: var(--gold); }
.promise h3 { font-size: 22px; font-weight: 600; margin-bottom: 10px; letter-spacing: -0.02em; }
.promise p { font-size: 15.5px; line-height: 1.6; }
@media (max-width: 900px) { .promise-grid { grid-template-columns: 1fr; } }

/* ===== PAIN POINTS — 8-card grid (4×2) that names the problem ===== */
.pain-section {
  padding: 110px 0;
  background: var(--bg-2);
  border-block: 1px solid var(--line);
  position: relative;
  overflow: hidden;
}
.pain-section::before {
  content: "";
  position: absolute; inset: 0;
  background:
    radial-gradient(60% 40% at 12% 8%, rgba(230,49,121,0.08), transparent 60%),
    radial-gradient(50% 35% at 92% 96%, rgba(200,154,74,0.07), transparent 60%);
  pointer-events: none;
}
.pain-section .container { position: relative; z-index: 1; }
.pain-section .section-head h2 .underline {
  position: relative;
  white-space: nowrap;
}
.pain-section .section-head h2 .underline::after {
  content: "";
  position: absolute; left: 0; right: 0; bottom: -4px; height: 8px;
  background: var(--pink-soft);
  z-index: -1; border-radius: 4px;
}
.pain-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
}
.pain {
  position: relative;
  padding: 28px 24px 24px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  display: flex; flex-direction: column;
  overflow: hidden;
  isolation: isolate;
  transition: transform .5s cubic-bezier(.2,.7,.2,1),
              box-shadow .55s cubic-bezier(.2,.7,.2,1),
              border-color .45s ease;
}
.pain > * { position: relative; z-index: 1; }
.pain::before {
  content: "";
  position: absolute;
  top: 28px; left: 24px;
  width: 60px; height: 60px;
  border-radius: 50%;
  background: radial-gradient(circle at center, #ff4d92 0%, var(--pink) 55%, #c41763 100%);
  transform: scale(0);
  transform-origin: center;
  transition: transform .9s cubic-bezier(.22,.85,.28,1);
  z-index: 0;
  pointer-events: none;
}
.pain:hover {
  transform: translateY(-8px);
  box-shadow: 0 30px 60px -18px rgba(230,49,121,0.55),
              0 12px 28px -12px rgba(20,16,14,0.18);
  border-color: var(--pink);
}
.pain:hover::before { transform: scale(22); }

.pain-ico {
  width: 52px; height: 52px; border-radius: 14px;
  background: var(--pink-soft); color: var(--pink);
  display: grid; place-items: center;
  font-size: 26px; margin-bottom: 18px;
  transition: transform .45s cubic-bezier(.2,.7,.2,1),
              background .5s ease,
              color .5s ease,
              box-shadow .5s ease;
}
.pain:nth-child(2n) .pain-ico { background: var(--gold-soft); color: var(--gold); }
.pain:hover .pain-ico {
  transform: rotate(-8deg) scale(1.1);
  background: rgba(255,255,255,0.18);
  color: #fff;
  box-shadow: 0 8px 22px -6px rgba(0,0,0,0.25), inset 0 0 0 1px rgba(255,255,255,0.25);
}

.pain h3 {
  font-size: 19px; font-weight: 600; letter-spacing: -0.02em;
  color: var(--ink); margin-bottom: 10px;
  transition: color .5s ease;
}
.pain p {
  font-size: 14.5px; line-height: 1.6; color: var(--ink-2);
  margin-bottom: 18px; flex: 1;
  transition: color .5s ease;
}
.pain:hover h3,
.pain:hover p { color: #fff; }

.pain-chip {
  display: inline-flex; align-items: center; gap: 6px;
  align-self: flex-start;
  font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
  padding: 7px 12px; border-radius: 100px;
  background: var(--pink-soft); color: var(--pink);
  transition: background .5s ease, color .5s ease;
}
.pain:hover .pain-chip { background: rgba(255,255,255,0.2); color: #fff; }
.pain-chip .ck {
  width: 14px; height: 14px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center;
  font-size: 9px; font-weight: 700;
  transition: background .5s ease, color .5s ease;
}
.pain:hover .pain-chip .ck { background: #fff; color: var(--pink); }
.pain-foot {
  text-align: center;
  margin-top: 50px;
  font-size: 16px; color: var(--ink-2);
}
.pain-foot strong { color: var(--ink); }
@media (max-width: 1100px) { .pain-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .pain-grid { grid-template-columns: 1fr; } }

/* ===== ABOUT — light story with portrait ===== */
.about-section { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.about-grid {
  display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 70px; align-items: center;
}
.about-portrait {
  position: relative;
  aspect-ratio: 4/5;
  border-radius: var(--r-xl);
  overflow: hidden;
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.3);
}
.about-portrait img {
  width: 100%; height: 100%; object-fit: cover; object-position: center 22%;
}
.about-portrait .badge {
  position: absolute; bottom: 22px; left: 22px; right: 22px;
  background: rgba(255,255,255,0.96);
  backdrop-filter: blur(10px);
  padding: 16px 20px;
  border-radius: 18px;
  display: flex; justify-content: space-between; align-items: center; gap: 16px;
}
.about-portrait .badge .lhs { display: flex; flex-direction: column; }
.about-portrait .badge .nm { font-size: 14px; font-weight: 700; color: var(--ink); letter-spacing: -0.01em; }
.about-portrait .badge .ttl { font-size: 11px; color: var(--ink-3); letter-spacing: 0.06em; }
.about-portrait .badge .stars { color: var(--gold); font-size: 13px; letter-spacing: 1.5px; }
.about-portrait .badge .num { font-size: 11px; color: var(--ink-2); margin-top: 2px; }

.about-text .eyebrow { margin-bottom: 18px; }
.about-text h2 { margin-bottom: 22px; }
.about-text p { margin-bottom: 16px; font-size: 17px; }
.about-text .signature {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 36px; color: var(--pink); line-height: 1; margin-top: 24px;
}
.about-bullets {
  display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
  margin-top: 30px;
}
.about-bullets li {
  list-style: none;
  display: flex; align-items: flex-start; gap: 12px;
  font-size: 14.5px; color: var(--ink); font-weight: 500;
}
.about-bullets li .ck {
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center;
  flex-shrink: 0; font-size: 12px;
  margin-top: 1px;
}
.about-cta { margin-top: 36px; display: flex; gap: 12px; flex-wrap: wrap; }

@media (max-width: 1000px) {
  .about-grid { grid-template-columns: 1fr; gap: 50px; }
  .about-portrait { max-width: 480px; margin: 0 auto; }
}

/* ===== 3-STEP PROCESS ===== */
.process-section { padding: 110px 0; }
.process-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
  margin-top: 20px;
}
.process-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.step {
  position: relative;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  overflow: hidden;
  transition: transform .35s, box-shadow .35s;
}
.step:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -25px rgba(20,16,14,0.18); }
.step-img {
  aspect-ratio: 4/3;
  background-size: cover; background-position: center;
  background-color: var(--bg-2);
  position: relative;
}
.step-img::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4) 100%);
}
.step-img .num {
  position: absolute; top: 20px; left: 20px; z-index: 2;
  display: inline-flex; align-items: center; gap: 8px;
  padding: 7px 13px 7px 7px; border-radius: 100px;
  background: rgba(255,255,255,0.96);
  font-size: 12px; font-weight: 600; letter-spacing: 0.06em; color: var(--ink);
}
.step-img .num span {
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center; font-size: 11px; font-weight: 700;
}
.step-img .day {
  position: absolute; bottom: 18px; right: 20px; z-index: 2;
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 38px; line-height: 0.9; color: #fff;
  text-shadow: 0 4px 16px rgba(0,0,0,0.4);
}
.step-body { padding: 28px 28px 30px; }
.step-body h3 { font-size: 22px; font-weight: 600; margin-bottom: 10px; letter-spacing: -0.02em; }
.step-body p { font-size: 15px; line-height: 1.6; }
@media (max-width: 1100px) { .process-grid, .process-grid.cols-3 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .process-grid, .process-grid.cols-3 { grid-template-columns: 1fr; } }
.step-img .day { font-size: clamp(28px, 2.4vw, 38px); }

/* ===== SERVICES — clean editorial cards ===== */
.services-section { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.svc-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.svc {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 36px 32px;
  transition: transform .35s, box-shadow .35s, border-color .35s;
  display: flex; flex-direction: column;
}
.svc:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -25px rgba(20,16,14,0.18); border-color: var(--line-2); }
.svc .ico {
  width: 54px; height: 54px; border-radius: 14px;
  background: var(--pink-soft); color: var(--pink);
  display: grid; place-items: center;
  font-size: 22px; font-weight: 700;
  margin-bottom: 20px;
  transition: transform .35s, background .35s;
}
.svc:nth-child(2) .ico { background: var(--gold-soft); color: var(--gold); }
.svc:hover .ico { transform: rotate(-6deg) scale(1.06); }
.svc .badge {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--pink); font-weight: 700;
  margin-bottom: 18px;
}
.svc .badge::before { content: ""; width: 22px; height: 1px; background: var(--pink); }
.svc h3 { font-size: 24px; font-weight: 600; letter-spacing: -0.025em; margin-bottom: 12px; }
.svc h3 .serif { color: var(--pink); }
.svc p { font-size: 15px; line-height: 1.6; margin-bottom: 22px; }
.svc ul {
  list-style: none; display: flex; flex-direction: column; gap: 10px;
  margin-bottom: 26px;
}
.svc ul li {
  display: flex; align-items: center; gap: 10px;
  font-size: 14.5px; color: var(--ink); font-weight: 500;
}
.svc ul li::before {
  content: "✓"; color: var(--pink); font-weight: 700; flex-shrink: 0; font-size: 13px;
}
.svc .cta {
  margin-top: auto;
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 14px; font-weight: 600; color: var(--ink);
  transition: color .25s, gap .25s;
}
.svc .cta:hover { color: var(--pink); gap: 10px; }
@media (max-width: 1000px) { .svc-grid { grid-template-columns: 1fr; } }

/* ===== TESTIMONIALS ===== */
.tests-section { padding: 110px 0; }
.tests-grid {
  display: grid; grid-template-columns: 1.3fr 1fr 1fr; gap: 16px;
}
.test {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 32px 28px;
  display: flex; flex-direction: column;
  transition: transform .3s, box-shadow .3s;
}
.test:hover { transform: translateY(-4px); box-shadow: 0 24px 48px -20px rgba(20,16,14,0.18); }
.test .stars { color: var(--gold); letter-spacing: 2px; font-size: 13px; margin-bottom: 16px; }
.test .quote {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 22px; line-height: 1.3; color: var(--ink); letter-spacing: -0.01em;
  margin-bottom: 22px;
  flex: 1;
}
.test .who {
  display: flex; align-items: center; gap: 12px;
  padding-top: 18px; border-top: 1px solid var(--line);
}
.test .av {
  width: 42px; height: 42px; border-radius: 50%;
  background: var(--grad-warm); color: #fff;
  display: grid; place-items: center; font-size: 13px; font-weight: 700;
  flex-shrink: 0;
}
.test .nm { font-size: 14px; font-weight: 600; color: var(--ink); }
.test .loc { font-size: 12px; color: var(--ink-3); }

.test.feat {
  background: var(--ink); color: #fff;
  border-color: var(--ink);
  background-image: radial-gradient(60% 40% at 100% 0%, rgba(230,49,121,0.25), transparent 60%);
  position: relative;
  overflow: hidden;
}
.test.feat .quote { color: #fff; font-size: 26px; }
.test.feat .nm { color: #fff; }
.test.feat .loc { color: rgba(255,255,255,0.55); }
.test.feat .who { border-top-color: rgba(255,255,255,0.12); }
.test.feat .stars { color: var(--gold-2); }
.test.feat .av { border: 2px solid #fff; overflow: hidden; }
.test.feat .av img { width: 100%; height: 100%; object-fit: cover; object-position: center 18%; }

@media (max-width: 1000px) { .tests-grid { grid-template-columns: 1fr 1fr; } .test.feat { grid-column: span 2; } }
@media (max-width: 700px)  { .tests-grid { grid-template-columns: 1fr; } .test.feat { grid-column: auto; } }

/* ===== TESTIMONIAL IMAGE GRID — 5×2 client screenshots ===== */
.tests-image-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  margin-top: 20px;
}
.test-card {
  position: relative;
  aspect-ratio: 4 / 5;
  width: 100%;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  overflow: hidden;
  background: var(--bg-2);
  padding: 0;
  cursor: zoom-in;
  font-family: inherit;
  transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
}
.test-card img {
  position: absolute; inset: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transition: transform .6s cubic-bezier(.2,.7,.2,1);
}
.test-card::after {
  content: "";
  position: absolute; inset: 0;
  background: linear-gradient(180deg, rgba(20,16,14,0) 55%, rgba(20,16,14,0.55) 100%);
  opacity: 0;
  transition: opacity .35s ease;
  pointer-events: none;
}
.test-card .badge {
  position: absolute; top: 12px; left: 12px; z-index: 2;
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.96);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  font-size: 10px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;
  color: var(--pink);
  padding: 6px 10px; border-radius: 100px;
  box-shadow: 0 6px 16px -8px rgba(20,16,14,0.25);
}
.test-card .badge::before {
  content: "★"; color: var(--gold);
  font-size: 11px; letter-spacing: 0;
}
.test-card .zoom {
  position: absolute; bottom: 12px; right: 12px; z-index: 2;
  width: 34px; height: 34px;
  border-radius: 50%;
  background: rgba(255,255,255,0.96);
  color: var(--ink);
  display: grid; place-items: center;
  font-size: 16px; font-weight: 600;
  opacity: 0; transform: translateY(6px);
  transition: opacity .3s, transform .3s;
}
.test-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 30px 60px -25px rgba(20,16,14,0.35);
  border-color: var(--line-2);
}
.test-card:hover img { transform: scale(1.06); }
.test-card:hover::after { opacity: 1; }
.test-card:hover .zoom { opacity: 1; transform: translateY(0); }
.test-card:focus-visible {
  outline: 2px solid var(--pink);
  outline-offset: 3px;
}

@media (max-width: 1200px) { .tests-image-grid { grid-template-columns: repeat(5, 1fr); gap: 14px; } }
@media (max-width: 1100px) { .tests-image-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 900px)  { .tests-image-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .tests-image-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } }

/* ===== SCORE REPORTS — 4×2 grid + mobile swipe carousel ===== */
.scores-block { margin-top: 90px; }
.scores-head {
  text-align: center;
  margin-bottom: 36px;
}
.scores-head .eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  margin-bottom: 14px;
}
.scores-head .eyebrow::before { display: none; }
.scores-head h3 {
  font-size: clamp(28px, 3.4vw, 42px);
  font-weight: 500;
  letter-spacing: -0.03em;
  color: var(--ink);
  margin-bottom: 10px;
}
.scores-sub {
  font-size: 15px;
  color: var(--ink-2);
  max-width: 520px;
  margin: 0 auto;
}
.scores-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
  align-items: start;
}
.score-card {
  display: block;
  width: 100%;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 10px 10px 0;
  cursor: zoom-in;
  font-family: inherit;
  text-align: left;
  overflow: hidden;
  box-shadow: 0 6px 18px -10px rgba(20,16,14,0.10);
  transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
}
.score-frame {
  width: 100%;
  border-radius: 14px;
  overflow: hidden;
  background: #fff;
  line-height: 0;
}
.score-card img {
  display: block;
  width: 100%;
  height: auto;
  object-fit: contain;
}
.score-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 12px 6px 14px;
}
.score-client {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--ink-3);
}
.score-gain {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 12px; font-weight: 700;
  color: #157a3d;
  background: #e6f6ec;
  border: 1px solid #c9eccd;
  padding: 5px 11px;
  border-radius: 100px;
  letter-spacing: 0.02em;
}
.score-gain::before {
  content: "↑";
  font-weight: 700;
  font-size: 11px;
}
.score-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 22px 44px -22px rgba(20,16,14,0.22);
  border-color: var(--line-2);
}
.score-card:focus-visible { outline: 2px solid var(--pink); outline-offset: 3px; }

@media (max-width: 1100px) { .scores-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; } }
@media (max-width: 600px) {
  .scores-block { margin-top: 70px; }
  /* Mobile: single vertical column, only the first 4 reports stacked one below the other */
  .scores-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
  }
  .scores-grid .score-card:nth-child(n+5) { display: none; }
  .score-card {
    width: 100%;
    max-width: 100%;
    margin: 0;
  }
}

/* ===== LIGHTBOX ===== */
.lightbox {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(20,16,14,0.92);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: none;
  align-items: center; justify-content: center;
  padding: 60px 24px;
  opacity: 0;
  transition: opacity .25s ease;
}
.lightbox.open { display: flex; opacity: 1; }
.lightbox img {
  max-width: min(92vw, 720px);
  max-height: 86vh;
  width: auto; height: auto;
  object-fit: contain;
  border-radius: 14px;
  box-shadow: 0 40px 80px -20px rgba(0,0,0,0.6);
  transform: scale(0.96);
  transition: transform .3s cubic-bezier(.2,.7,.2,1);
}
.lightbox.open img { transform: scale(1); }
.lightbox-close,
.lightbox-nav {
  position: absolute;
  background: rgba(255,255,255,0.12);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.18);
  cursor: pointer;
  display: grid; place-items: center;
  transition: background .2s, transform .2s;
  font-family: inherit;
}
.lightbox-close:hover,
.lightbox-nav:hover { background: rgba(255,255,255,0.22); transform: scale(1.05); }
.lightbox-close {
  top: 22px; right: 22px;
  width: 44px; height: 44px;
  border-radius: 50%;
  font-size: 24px; line-height: 1;
}
.lightbox-nav {
  top: 50%; transform: translateY(-50%);
  width: 52px; height: 52px;
  border-radius: 50%;
  font-size: 32px; line-height: 1;
}
.lightbox-nav:hover { transform: translateY(-50%) scale(1.05); }
.lightbox-nav.prev { left: 22px; }
.lightbox-nav.next { right: 22px; }
@media (max-width: 700px) {
  .lightbox-nav { width: 42px; height: 42px; font-size: 24px; }
  .lightbox-nav.prev { left: 10px; }
  .lightbox-nav.next { right: 10px; }
  .lightbox-close { top: 14px; right: 14px; width: 38px; height: 38px; font-size: 20px; }
}

/* ===== PRICING ===== */
.pricing-section { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.pricing-section .container { max-width: 1480px; }
.pricing-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
  align-items: stretch;
}
.pricing-grid-5 { grid-template-columns: repeat(5, 1fr); gap: 16px; }
.price {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 38px 32px 36px;
  display: flex; flex-direction: column;
  transition: transform .3s, box-shadow .3s, border-color .3s;
  position: relative;
}
/* When 5 cards share the row, scale type slightly so cards feel similar to 3-up */
.pricing-grid-5 .price { padding: 36px 28px 32px; }
.pricing-grid-5 .price .amt { font-size: 46px; }
.pricing-grid-5 .price .amt .p { font-size: 13px; }
.pricing-grid-5 .price .desc { font-size: 14px; margin-bottom: 22px; }
.pricing-grid-5 .price li { font-size: 14px; padding: 11px 0; gap: 10px; }
.pricing-grid-5 .price ul { margin-bottom: 26px; }
.pricing-grid-5 .price .strike { font-size: 12.5px; }
.pricing-grid-5 .price .name { font-size: 11.5px; margin-bottom: 12px; }
.pricing-grid-5 .price .btn { padding: 13px 18px; font-size: 13.5px; }
.price-tag-gold {
  background: linear-gradient(135deg, var(--gold), #b07d2f) !important;
  box-shadow: 0 8px 20px -6px rgba(200,154,74,0.55) !important;
}
.price:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -25px rgba(20,16,14,0.18); border-color: var(--line-2); }
.price.feat {
  background: var(--ink);
  border-color: var(--ink);
  color: #fff;
  transform: translateY(-12px);
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.4);
}
.price.feat:hover { transform: translateY(-18px); }
.price.feat .name { color: rgba(255,255,255,0.7); }
.price.feat .amt { color: #fff; }
.price.feat .desc { color: rgba(255,255,255,0.7); }
.price.feat li { color: #fff; border-color: rgba(255,255,255,0.1); }
.price.feat li::before { color: var(--pink-2); }
.price-tag {
  position: absolute; top: -1px; left: 50%; transform: translate(-50%, -50%);
  padding: 7px 16px; border-radius: 100px;
  background: var(--pink); color: #fff;
  font-size: 11px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase;
  box-shadow: 0 8px 20px -6px rgba(230,49,121,0.55);
}
.price .name { font-size: 12px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--ink-2); margin-bottom: 12px; font-weight: 600; }
.price .amt {
  font-size: 56px; font-weight: 600; letter-spacing: -0.04em; line-height: 1;
  color: var(--ink); margin-bottom: 6px;
  display: flex; align-items: baseline; gap: 6px;
}
.price .amt .p { font-size: 14px; color: var(--ink-2); font-weight: 400; }
.price .strike { color: var(--ink-3); text-decoration: line-through; font-size: 13px; margin-bottom: 8px; min-height: 18px; }
.price.feat .strike { color: rgba(255,255,255,0.45); }
.price .desc { font-size: 14.5px; color: var(--ink-2); margin-bottom: 24px; }
.price ul { list-style: none; margin-bottom: 28px; flex: 1; display: flex; flex-direction: column; gap: 0; }
.price li {
  font-size: 14.5px; padding: 12px 0;
  border-top: 1px solid var(--line);
  display: flex; align-items: center; gap: 10px;
  color: var(--ink);
}
.price li:first-child { border-top: 0; padding-top: 0; }
.price li::before {
  content: "✓"; color: var(--pink); font-weight: 700; flex-shrink: 0; font-size: 13px;
}
.price .btn { width: 100%; }
.price-meta {
  text-align: center; margin-top: 30px;
  font-size: 13px; color: var(--ink-3);
}
.price-meta strong { color: var(--ink); font-weight: 600; }

/* Mentorship — single hero price card */
.mentor-price {
  max-width: 640px; margin: 0 auto;
  background: linear-gradient(180deg, #1a0c14 0%, #0f0a0c 100%);
  background-image:
    radial-gradient(60% 80% at 100% 0%, rgba(230,49,121,0.32), transparent 60%),
    radial-gradient(45% 55% at 0% 100%, rgba(200,154,74,0.18), transparent 60%),
    linear-gradient(180deg, #1a0c14 0%, #0f0a0c 100%);
  color: #fff;
  border-radius: var(--r-xl);
  padding: 44px 44px 38px;
  box-shadow: 0 50px 100px -30px rgba(20,16,14,0.45),
              0 0 0 1px rgba(255,255,255,0.05) inset;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.mentor-price-tag {
  display: inline-block;
  background: var(--pink); color: #fff;
  font-size: 10.5px; letter-spacing: 0.18em; text-transform: uppercase; font-weight: 700;
  padding: 7px 14px; border-radius: 100px;
  margin-bottom: 22px;
  box-shadow: 0 12px 26px -8px rgba(230,49,121,0.55);
}
.mentor-price-amt {
  display: inline-flex; align-items: baseline; gap: 14px; flex-wrap: wrap; justify-content: center;
  margin-bottom: 14px;
}
.mentor-price-amt .strike {
  font-size: 22px; color: rgba(255,255,255,0.45);
  text-decoration: line-through; font-weight: 500;
}
.mentor-price-amt .now {
  font-size: 72px; font-weight: 700; letter-spacing: -0.04em;
  background: linear-gradient(135deg, #fff 0%, var(--pink-2) 100%);
  -webkit-background-clip: text; background-clip: text; color: transparent;
  line-height: 1;
}
.mentor-price-amt small {
  font-size: 13px; color: rgba(255,255,255,0.55);
  letter-spacing: 0.04em;
}
.mentor-price-desc {
  font-size: 16px; line-height: 1.55;
  color: rgba(255,255,255,0.75);
  max-width: 480px; margin: 0 auto 26px;
}
.mentor-price-list {
  list-style: none; padding: 0; margin: 0 0 28px;
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px 18px;
  text-align: left;
}
.mentor-price-list li {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; color: rgba(255,255,255,0.9);
}
.mentor-price-list .ck {
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(230,49,121,0.18); color: var(--pink-2);
  display: grid; place-items: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.mentor-price-btn {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--pink); color: #fff;
  padding: 16px 32px; border-radius: 100px;
  font-size: 15px; font-weight: 700;
  letter-spacing: 0.02em;
  box-shadow: 0 18px 36px -10px rgba(230,49,121,0.7);
  transition: background .2s, transform .2s, box-shadow .2s;
}
.mentor-price-btn:hover {
  background: #fff; color: var(--pink);
  transform: translateY(-2px);
  box-shadow: 0 24px 44px -10px rgba(230,49,121,0.5);
}
.mentor-price-fine {
  margin-top: 18px;
  font-size: 11.5px; color: rgba(255,255,255,0.45);
  letter-spacing: 0.02em;
}
@media (max-width: 700px) {
  .mentor-price { padding: 32px 24px 28px; border-radius: 24px; }
  .mentor-price-amt .now { font-size: 56px; }
  .mentor-price-amt .strike { font-size: 18px; }
  .mentor-price-list { grid-template-columns: 1fr; }
  .mentor-price-btn { padding: 14px 24px; font-size: 14px; }
}

@media (max-width: 1300px) {
  .pricing-grid-5 { grid-template-columns: repeat(3, 1fr); gap: 16px; max-width: 1100px; margin: 0 auto; }
  .pricing-grid-5 .price { padding: 38px 30px 34px; }
  .pricing-grid-5 .price .amt { font-size: 52px; }
}
@media (max-width: 1000px) {
  .pricing-grid { grid-template-columns: 1fr; max-width: 460px; margin: 0 auto; }
  .pricing-grid-5 { grid-template-columns: repeat(2, 1fr); max-width: 760px; }
  .pricing-grid-5 .price .amt { font-size: 44px; }
  .price.feat { transform: none; }
  .price.feat:hover { transform: translateY(-6px); }
}
@media (max-width: 640px) {
  .pricing-grid-5 { grid-template-columns: 1fr; max-width: 460px; }
  .pricing-grid-5 .price { padding: 34px 26px 30px; }
}

/* ===== EBOOKS LIBRARY — dark luxury section ===== */
.ebooks-section {
  padding: 110px 0;
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(60% 50% at 0% 0%, rgba(230,49,121,0.18), transparent 60%),
    radial-gradient(50% 40% at 100% 100%, rgba(200,154,74,0.12), transparent 60%),
    linear-gradient(180deg, #1a0c14 0%, #0f0a0c 100%);
  color: #fff;
  border-block: 1px solid rgba(255,255,255,0.06);
}
.ebooks-section::before {
  content: ""; position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, rgba(255,255,255,0.015) 0 2px, transparent 2px 14px);
  pointer-events: none;
}
.ebooks-section .container { position: relative; z-index: 1; }
.ebooks-section .section-head h2 { color: #fff; }
.ebooks-section .section-head h2 em.serif.gradient-text {
  background: linear-gradient(135deg, var(--pink-2), #ffd9a0);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.ebooks-section .section-head p { color: rgba(255,255,255,0.65); }
.ebooks-section .section-head .eyebrow { color: var(--pink-2); }
.ebooks-section .section-head .eyebrow::before { background: var(--pink-2); }

.ebooks-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
  align-items: start;
}
.ebook {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: var(--r-lg);
  padding: 22px 22px 24px;
  display: flex; flex-direction: column;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  transition: transform .35s, box-shadow .35s, border-color .35s, background .35s;
}
.ebook:hover {
  transform: translateY(-6px);
  box-shadow: 0 30px 60px -20px rgba(230,49,121,0.35);
  border-color: rgba(230,49,121,0.4);
  background: rgba(255,255,255,0.06);
}
.ebook-cover {
  margin-bottom: 18px;
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  line-height: 0;
  box-shadow: 0 20px 40px -16px rgba(0,0,0,0.6);
}
.ebook-cover img {
  display: block;
  width: 100%;
  height: auto;
  object-fit: contain;
  transition: transform .8s var(--ease);
}
.ebook:hover .ebook-cover img { transform: scale(1.03); }
.ebook h4 {
  font-size: 15px; font-weight: 600; line-height: 1.3;
  margin-bottom: 6px; letter-spacing: -0.01em;
  color: #fff;
}
.ebook .meta {
  display: flex; justify-content: space-between; align-items: baseline;
  margin-top: auto; padding-top: 14px;
  border-top: 1px solid rgba(255,255,255,0.08);
}
.ebook .ep {
  font-size: 24px; font-weight: 700;
  letter-spacing: -0.025em;
  background: linear-gradient(135deg, #fff 0%, var(--pink-2) 100%);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.ebook .ep small {
  font-size: 13px; color: rgba(255,255,255,0.5);
  font-weight: 500;
  -webkit-text-fill-color: rgba(255,255,255,0.5);
}
.ebook .buy {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 13px; font-weight: 600; color: var(--pink-2);
  transition: gap .25s, color .25s;
}
.ebook .buy:hover { gap: 9px; color: #fff; }
@media (max-width: 1000px) { .ebooks-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .ebooks-grid { grid-template-columns: 1fr; max-width: 360px; margin: 0 auto; } }

/* ===== POST-PURCHASE — 4 step cards + warning ===== */
.postpurchase-section {
  padding: 110px 0;
  background: var(--bg-2);
  border-block: 1px solid var(--line);
}
.pp-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
  align-items: stretch;
}
.pp-card {
  position: relative;
  padding: 28px 26px 26px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  display: flex; flex-direction: column;
  transition: transform .35s, box-shadow .35s, border-color .35s;
}
.pp-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 24px 48px -20px rgba(20,16,14,0.18);
  border-color: var(--line-2);
}
.pp-num {
  font-family: 'Instrument Serif', serif;
  font-style: italic;
  font-size: 56px;
  line-height: 0.9;
  color: var(--pink);
  letter-spacing: -0.04em;
  margin-bottom: 14px;
}
.pp-tag {
  display: inline-block;
  font-size: 9.5px; letter-spacing: 0.16em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 5px 10px; border-radius: 100px;
  margin-bottom: 10px;
  align-self: flex-start;
}
.pp-card h3 {
  font-size: 17px; font-weight: 600;
  letter-spacing: -0.02em;
  color: var(--ink); margin-bottom: 10px;
  line-height: 1.3;
}
.pp-card > p {
  font-size: 13.5px; line-height: 1.55;
  color: var(--ink-2);
  margin-bottom: 12px;
}
.pp-alert {
  background: var(--ink);
  color: #fff;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 10.5px; font-weight: 700;
  letter-spacing: 0.06em;
  margin: 8px 0 14px;
  text-align: center;
}
.pp-link {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 13px; font-weight: 600;
  color: var(--pink);
  margin-top: auto;
  transition: gap .2s, color .2s;
}
.pp-link:hover { gap: 9px; color: var(--ink); }

.pp-critical {
  background: linear-gradient(180deg, #fff5f9 0%, #fff 100%);
  border-color: var(--pink-soft);
}

.pp-sub {
  font-size: 10.5px; font-weight: 700;
  letter-spacing: 0.14em; text-transform: uppercase;
  color: var(--ink);
  margin: 10px 0 8px;
}
.pp-sub-small {
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--ink-3);
  margin: 12px 0 4px;
}
.pp-small {
  font-size: 12px;
  color: var(--ink-3);
  line-height: 1.5;
}
.pp-list {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 6px;
}
.pp-list li {
  position: relative;
  padding-left: 20px;
  font-size: 13px; line-height: 1.45;
  color: var(--ink-2);
}
.pp-list li::before {
  content: "✓";
  position: absolute; left: 0; top: 0;
  color: var(--pink); font-weight: 700;
  font-size: 12px;
}

/* Warning disclaimer */
.pp-warning {
  margin-top: 44px;
  position: relative;
  background: var(--ink);
  background-image: radial-gradient(60% 50% at 100% 100%, rgba(230,49,121,0.25), transparent 60%),
                    radial-gradient(40% 40% at 0% 0%, rgba(200,154,74,0.15), transparent 60%);
  color: #fff;
  border-radius: var(--r-lg);
  padding: 36px 40px;
  box-shadow: 0 30px 60px -20px rgba(20,16,14,0.4);
  overflow: hidden;
}
.pp-warning-head {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 14px;
}
.pp-warning-ico {
  display: grid; place-items: center;
  width: 36px; height: 36px;
  background: var(--pink);
  border-radius: 50%;
  font-size: 16px; font-weight: 700;
  box-shadow: 0 8px 18px -6px rgba(230,49,121,0.6);
}
.pp-warning-head strong {
  font-size: 13px; font-weight: 700;
  letter-spacing: 0.2em; text-transform: uppercase;
  color: var(--pink-2);
}
.pp-warning > p {
  font-size: 16px; color: rgba(255,255,255,0.78);
  margin-bottom: 18px;
  max-width: 640px;
}
.pp-warning ul {
  list-style: none; padding: 0; margin: 0 0 20px;
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
}
.pp-warning ul li {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; color: #fff;
  padding: 12px 14px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  font-weight: 500;
}
.pp-warning ul li span {
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center;
  font-size: 11px; font-weight: 700;
  flex-shrink: 0;
}
.pp-warning-foot {
  margin-top: 4px;
  padding-top: 18px;
  border-top: 1px solid rgba(255,255,255,0.12);
  font-size: 17px; font-weight: 700;
  color: var(--pink-2);
  letter-spacing: -0.01em;
}

@media (max-width: 1100px) { .pp-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) {
  .pp-grid { grid-template-columns: 1fr; }
  .pp-warning { padding: 28px 24px; }
  .pp-warning ul { grid-template-columns: 1fr; }
  .pp-warning > p { font-size: 15px; }
  .pp-warning-foot { font-size: 15px; }
  .pp-num { font-size: 48px; }
}

/* ===== BECOME YOUR OWN BOSS — Mentorship + Course + Community ===== */
.boss-section {
  padding: 110px 0;
  background: var(--ink);
  color: #fff;
  position: relative;
  overflow: hidden;
}
.boss-section::before {
  content: ""; position: absolute; inset: 0;
  background:
    radial-gradient(50% 40% at 0% 0%, rgba(230,49,121,0.18), transparent 60%),
    radial-gradient(50% 40% at 100% 100%, rgba(200,154,74,0.15), transparent 60%);
  pointer-events: none;
}
.boss-section .section-head h2 { color: #fff; }
.boss-section .section-head p { color: rgba(255,255,255,0.7); }
.boss-section .eyebrow { color: var(--pink-2); }
.boss-section .eyebrow::before { background: var(--pink-2); }

.boss-grid {
  display: grid; grid-template-columns: 1.1fr 1fr 1fr; gap: 16px;
  position: relative; z-index: 2;
}
.boss-card {
  background: rgba(255,255,255,0.04);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: var(--r-lg);
  padding: 36px 32px;
  display: flex; flex-direction: column;
  transition: transform .35s, border-color .35s, background .35s;
  position: relative;
}
.boss-card:hover {
  transform: translateY(-6px);
  border-color: rgba(255,61,142,0.4);
  background: rgba(255,255,255,0.06);
}
.boss-card.feat {
  background: linear-gradient(160deg, rgba(230,49,121,0.18), rgba(200,154,74,0.06));
  border-color: rgba(230,49,121,0.35);
}
.boss-card .label {
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--pink-2); font-weight: 700;
  margin-bottom: 16px;
  display: inline-flex; align-items: center; gap: 10px;
}
.boss-card .label::before { content: ""; width: 22px; height: 1px; background: var(--pink-2); }
.boss-card h3 { font-size: 24px; font-weight: 600; color: #fff; letter-spacing: -0.025em; margin-bottom: 12px; line-height: 1.15; }
.boss-card h3 .serif { color: var(--pink-2); }
.boss-card p { color: rgba(255,255,255,0.7); font-size: 14.5px; margin-bottom: 24px; line-height: 1.55; }

.tier-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 24px; }
.tier {
  padding: 18px 10px; text-align: center;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px;
  transition: background .25s, border-color .25s, transform .25s;
  cursor: pointer;
}
.tier:hover { background: rgba(230,49,121,0.12); border-color: var(--pink-2); transform: translateY(-3px); }
.tier .t-len { font-size: 10.5px; letter-spacing: 0.14em; text-transform: uppercase; color: rgba(255,255,255,0.5); }
.tier .t-price { font-size: 22px; font-weight: 700; color: #fff; margin-top: 6px; letter-spacing: -0.02em; }

.boss-price {
  display: flex; align-items: baseline; gap: 12px; margin: 14px 0 6px;
}
.boss-price .num {
  font-size: 56px; font-weight: 600; letter-spacing: -0.04em; line-height: 1;
  background: var(--grad-warm);
  -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
}
.boss-price .strike {
  color: rgba(255,255,255,0.4); text-decoration: line-through; font-size: 16px;
}
.boss-card .save {
  font-size: 11px; color: var(--gold-2); letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700;
  margin-bottom: 22px;
}
.boss-card .recurring {
  font-size: 12px; color: rgba(255,255,255,0.55); margin-bottom: 22px; letter-spacing: 0.05em;
}
.boss-card ul {
  list-style: none; display: flex; flex-direction: column; gap: 10px;
  margin-bottom: 26px;
}
.boss-card ul li {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; color: rgba(255,255,255,0.85);
}
.boss-card ul li::before {
  content: "✓"; color: var(--pink-2); font-weight: 700; font-size: 13px; flex-shrink: 0;
}
.boss-card .btn { width: 100%; margin-top: auto; }
.boss-card .btn-pink-glow {
  background: var(--pink); color: #fff;
  box-shadow: 0 12px 30px -10px rgba(230,49,121,0.6);
}
.boss-card .btn-pink-glow:hover { background: #fff; color: var(--ink); transform: translateY(-2px); }
.boss-card .btn-outline-light {
  border: 1.5px solid rgba(255,255,255,0.4); color: #fff; background: transparent;
}
.boss-card .btn-outline-light:hover { background: #fff; color: var(--ink); }

@media (max-width: 1000px) { .boss-grid { grid-template-columns: 1fr; } .tier { padding: 14px 8px; } }

/* ===== AUTHORITY · LUXURY CREDIT BRAND BLOCK ===== */
.authority {
  padding: 110px 0;
  background:
    radial-gradient(55% 40% at 0% 0%, rgba(230,49,121,0.06), transparent 65%),
    radial-gradient(45% 35% at 100% 100%, rgba(200,154,74,0.07), transparent 65%),
    var(--bg);
  position: relative;
  overflow: hidden;
}
.auth-grid {
  display: grid;
  grid-template-columns: 1fr 1.05fr;
  gap: 70px;
  align-items: center;
}
.auth-text .eyebrow { margin-bottom: 18px; }
.auth-text h2 { margin-bottom: 18px; }
.auth-text .lede { font-size: 17px; color: var(--ink-2); margin-bottom: 32px; line-height: 1.6; }

.auth-meta {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
  margin-bottom: 28px;
}
.meta-item {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 18px 20px 16px;
  transition: transform .3s, border-color .3s, box-shadow .3s;
}
.meta-item:hover { transform: translateY(-3px); border-color: var(--line-2); box-shadow: 0 16px 36px -16px rgba(20,16,14,0.15); }
.meta-num {
  display: block;
  font-size: 30px; font-weight: 600;
  letter-spacing: -0.03em; color: var(--ink);
  line-height: 1; margin-bottom: 6px;
}
.meta-lab { display: block; font-size: 12.5px; color: var(--ink-3); letter-spacing: 0.02em; }

.auth-trust {
  list-style: none;
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  margin-bottom: 24px;
}
.auth-trust li {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; color: var(--ink); font-weight: 500;
}
.auth-trust .ck {
  width: 20px; height: 20px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center;
  flex-shrink: 0; font-size: 10.5px; font-weight: 700;
}

.auth-strip {
  display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
  padding: 14px 18px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 100px;
  margin-bottom: 30px;
  box-shadow: 0 8px 24px -16px rgba(20,16,14,0.18);
}
.auth-strip-lead {
  font-size: 11px; font-weight: 700; letter-spacing: 0.18em;
  text-transform: uppercase; color: var(--ink-3);
  margin-right: 6px;
}
.auth-pill {
  display: inline-block;
  font-size: 12px; font-weight: 600; color: var(--ink);
  padding: 6px 12px;
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 100px;
}

.auth-ctas { display: flex; flex-wrap: wrap; gap: 12px; }

/* MOSAIC */
.auth-mosaic {
  position: relative;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: 84px;
  gap: 12px;
}
.auth-tile {
  position: relative; overflow: hidden;
  border-radius: var(--r-md);
  background: var(--bg-2);
  transition: transform .5s var(--ease), box-shadow .5s;
}
.auth-tile img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform 1s var(--ease);
}
.auth-tile::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.6) 100%);
  transition: opacity .35s;
}
.auth-tile:hover { transform: translateY(-4px); box-shadow: 0 30px 60px -22px rgba(20,16,14,0.3); }
.auth-tile:hover img { transform: scale(1.06); }

.auth-tile.m1 img { object-position: center 22%; }
.auth-tile.m2 img { object-position: center 25%; }
.auth-tile.m3 img { object-position: center 20%; }
.auth-tile.m4 img { object-position: center 22%; }

.auth-tile.m1 { grid-column: span 2; grid-row: span 5; }
.auth-tile.m2 { grid-column: span 2; grid-row: span 3; }
.auth-tile.m3 { grid-column: span 1; grid-row: span 2; }
.auth-tile.m4 { grid-column: span 1; grid-row: span 2; }

.m-cap {
  position: absolute; left: 14px; right: 14px; bottom: 14px; z-index: 2;
  color: #fff;
  display: flex; flex-direction: column; gap: 5px;
}
.m-tag {
  align-self: flex-start;
  font-size: 9.5px; letter-spacing: 0.18em; text-transform: uppercase;
  background: rgba(255,255,255,0.18);
  backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.28);
  padding: 4px 10px; border-radius: 100px;
  font-weight: 700;
}
.m-ttl {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 22px; line-height: 1; letter-spacing: -0.01em;
}
.m-sub { font-size: 12px; opacity: 0.85; }

/* Floating review card overlapping the mosaic */
.auth-floater {
  position: absolute;
  left: -18px; bottom: -22px; z-index: 4;
  background: #fff;
  padding: 16px 18px;
  border: 1px solid var(--line);
  border-radius: 18px;
  box-shadow: 0 26px 50px -20px rgba(20,16,14,0.3);
  max-width: 260px;
}
.fl-stars { color: var(--gold); letter-spacing: 2px; font-size: 12px; margin-bottom: 6px; }
.fl-quote {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 17px; line-height: 1.25; color: var(--ink); margin-bottom: 6px;
}
.fl-by { font-size: 11px; color: var(--ink-3); letter-spacing: 0.06em; }

@media (max-width: 1100px) {
  .auth-grid { grid-template-columns: 1fr; gap: 60px; }
  .auth-mosaic { max-width: 640px; margin: 0 auto; }
}
@media (max-width: 600px) {
  .auth-meta { grid-template-columns: 1fr 1fr; }
  .auth-trust { grid-template-columns: 1fr; }
  .meta-num { font-size: 24px; }
  .auth-mosaic { grid-auto-rows: 72px; }
  .m-ttl { font-size: 18px; }
  .auth-floater { left: 12px; right: 12px; bottom: -16px; max-width: none; }
}

/* ===== EDITORIAL GALLERY (legacy — kept for reuse) ===== */
.editorial { padding: 110px 0; }
.ed-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-auto-rows: 70px;
  gap: 14px;
}
.ed-tile {
  position: relative; overflow: hidden;
  border-radius: var(--r-md);
  background: var(--bg-2);
  cursor: pointer;
  transition: transform .5s var(--ease), box-shadow .5s;
}
.ed-tile img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform 1s var(--ease);
}
.ed-tile:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -20px rgba(20,16,14,0.25); }
.ed-tile:hover img { transform: scale(1.05); }
.ed-tile::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 55%, rgba(0,0,0,0.55) 100%);
  opacity: 0.55; transition: opacity .35s;
}
.ed-tile:hover::after { opacity: 0.85; }
.ed-tile .cap {
  position: absolute; bottom: 16px; left: 16px; right: 16px; z-index: 3;
  display: flex; justify-content: space-between; align-items: end; gap: 10px; color: #fff;
}
.ed-tile .cap .ttl {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 18px; line-height: 1; letter-spacing: -0.01em;
}
.ed-tile .cap .num {
  font-size: 10px; letter-spacing: 0.16em;
  border: 1px solid rgba(255,255,255,0.4); padding: 3px 8px;
  border-radius: 100px;
}
.ed-tile.t1 { grid-column: span 5; grid-row: span 7; }
.ed-tile.t2 { grid-column: span 4; grid-row: span 4; }
.ed-tile.t3 { grid-column: span 3; grid-row: span 4; }
.ed-tile.t4 { grid-column: span 4; grid-row: span 3; }
.ed-tile.t5 { grid-column: span 3; grid-row: span 3; }
.ed-tile.t6 { grid-column: span 4; grid-row: span 4; }
.ed-tile.t7 { grid-column: span 8; grid-row: span 4; }
.ed-tile.t1 img { object-position: center 30%; }
.ed-tile.t2 img { object-position: center 18%; }
.ed-tile.t3 img { object-position: center 22%; }
.ed-tile.t4 img { object-position: center 18%; }
.ed-tile.t5 img { object-position: center 22%; }
.ed-tile.t6 img { object-position: center 25%; }
.ed-tile.t7 img { object-position: center 20%; }
@media (max-width: 900px) {
  .ed-grid { grid-template-columns: repeat(6, 1fr); grid-auto-rows: 80px; }
  .ed-tile.t1 { grid-column: span 6; grid-row: span 5; }
  .ed-tile.t2 { grid-column: span 3; grid-row: span 3; }
  .ed-tile.t3 { grid-column: span 3; grid-row: span 3; }
  .ed-tile.t4 { grid-column: span 2; grid-row: span 2; }
  .ed-tile.t5 { grid-column: span 2; grid-row: span 2; }
  .ed-tile.t6 { grid-column: span 2; grid-row: span 2; }
  .ed-tile.t7 { grid-column: span 6; grid-row: span 4; }
}

/* ===== FAQ ===== */
.faq-section { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.faq-wrap {
  display: grid; grid-template-columns: 1fr 1.4fr; gap: 60px; align-items: start;
}
.faq-side h2 { margin-bottom: 18px; }
.faq-side .ctas { margin-top: 28px; display: flex; flex-direction: column; gap: 10px; }
.faq-list { display: flex; flex-direction: column; gap: 10px; }
.faq-item {
  background: #fff; border: 1px solid var(--line); border-radius: 18px;
  overflow: hidden;
  transition: border-color .25s;
}
.faq-item:hover { border-color: var(--line-2); }
.faq-q {
  padding: 22px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px;
  cursor: pointer;
  font-size: 16.5px; font-weight: 600; color: var(--ink); letter-spacing: -0.01em;
}
.faq-q .icon {
  width: 30px; height: 30px; border-radius: 50%; background: var(--bg-2);
  display: grid; place-items: center; flex-shrink: 0;
  transition: background .2s, transform .3s;
  font-size: 18px; color: var(--ink);
}
.faq-item.open .faq-q .icon { background: var(--pink); color: #fff; transform: rotate(45deg); }
.faq-a { max-height: 0; overflow: hidden; transition: max-height .35s var(--ease); }
.faq-a-inner { padding: 0 24px 22px; font-size: 15px; color: var(--ink-2); line-height: 1.6; }
.faq-item.open .faq-a { max-height: 280px; }

@media (max-width: 900px) { .faq-wrap { grid-template-columns: 1fr; gap: 30px; } }

/* ===== FINAL CTA ===== */
.cta-section { padding: 100px 0 130px; }
.cta-card {
  position: relative;
  border-radius: var(--r-xl);
  background:
    radial-gradient(60% 80% at 100% 0%, rgba(230,49,121,0.5), transparent 60%),
    radial-gradient(50% 60% at 0% 100%, rgba(200,154,74,0.35), transparent 60%),
    linear-gradient(180deg, #1a0c14 0%, #0f0a0c 100%);
  overflow: hidden;
  display: grid; grid-template-columns: 1.2fr 1fr;
  align-items: stretch;
  min-height: 460px;
  box-shadow: 0 50px 100px -30px rgba(20,16,14,0.4);
}
.cta-text {
  padding: 70px 60px;
  display: flex; flex-direction: column; justify-content: center;
  color: #fff;
}
.cta-text .eyebrow { color: var(--pink-2); margin-bottom: 16px; }
.cta-text .eyebrow::before { background: var(--pink-2); }
.cta-text h2 {
  color: #fff;
  font-size: clamp(2.2rem, 4.5vw, 3.6rem); margin-bottom: 18px;
}
.cta-text p { color: rgba(255,255,255,0.7); font-size: 17px; margin-bottom: 30px; max-width: 440px; }
.cta-text .ctas { display: flex; gap: 12px; flex-wrap: wrap; }
.cta-text .stamp {
  margin-top: 38px; padding-top: 28px; border-top: 1px solid rgba(255,255,255,0.12);
  display: flex; align-items: center; gap: 14px;
}
.cta-text .stamp img {
  width: 48px; height: 48px; border-radius: 50%; object-fit: cover; object-position: center 20%;
  border: 2px solid var(--pink);
}
.cta-text .stamp .nm { font-size: 14px; font-weight: 600; color: #fff; }
.cta-text .stamp .ttl { font-size: 12px; color: rgba(255,255,255,0.55); }

.cta-image {
  position: relative; overflow: hidden;
}
.cta-image img {
  width: 100%; height: 100%; object-fit: cover; object-position: center 18%;
}
.cta-image::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(90deg, rgba(15,10,12,0.35) 0%, transparent 30%);
}
@media (max-width: 900px) {
  .cta-card { grid-template-columns: 1fr; min-height: auto; }
  .cta-text { padding: 50px 32px; }
  .cta-image { aspect-ratio: 4/3; }
}

/* ===== FOOTER ===== */
footer { padding: 70px 0 40px; background: var(--bg); border-top: 1px solid var(--line); position: relative; z-index: 2; }
.footer-top { display: grid; grid-template-columns: 1.4fr repeat(4, 1fr); gap: 50px; margin-bottom: 50px; }
.footer-brand img { height: 48px; width: auto; margin-bottom: 18px; }
.footer-brand p { font-size: 14.5px; max-width: 320px; }
.footer-col h5 { font-size: 12px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--ink); margin-bottom: 18px; font-weight: 700; }
.footer-col a { display: block; padding: 6px 0; color: var(--ink-2); font-size: 14px; transition: color .25s; }
.footer-col a:hover { color: var(--pink); }
.footer-bottom {
  display: flex; justify-content: space-between; align-items: center; gap: 20px;
  flex-wrap: wrap; padding-top: 26px; border-top: 1px solid var(--line);
  font-size: 13px; color: var(--ink-3);
}
.socials { display: flex; gap: 8px; }
.socials a {
  width: 36px; height: 36px; border-radius: 50%;
  border: 1px solid transparent;
  display: grid; place-items: center;
  color: #fff;
  transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
}
/* Instagram — official brand gradient */
.socials a[aria-label="Instagram"] {
  background: radial-gradient(circle at 30% 110%,
    #ffdb5c 0%,
    #ff9a3c 20%,
    #ff5a55 35%,
    #d63a8a 55%,
    #8e3acb 75%,
    #4f5bd5 100%);
  box-shadow: 0 6px 14px -6px rgba(214,58,138,0.55);
}
/* TikTok — official black brand */
.socials a[aria-label="TikTok"] {
  background: #000;
  box-shadow: 0 6px 14px -6px rgba(0,0,0,0.55);
}
/* Facebook — official brand blue */
.socials a[aria-label="Facebook"] {
  background: #1877F2;
  box-shadow: 0 6px 14px -6px rgba(24,119,242,0.55);
}
.socials a:hover {
  transform: translateY(-3px);
  filter: brightness(1.08) saturate(1.1);
  box-shadow: 0 12px 22px -8px rgba(20,16,14,0.35);
}
@media (max-width: 900px) { .footer-top { grid-template-columns: 1fr 1fr; } }
@media (max-width: 540px) { .footer-top { grid-template-columns: 1fr; gap: 32px; } }

/* ===== FUNDING QUALIFICATION — multi-step form ===== */
.fund-qual {
  padding: 100px 0;
  background: var(--bg-2);
  border-block: 1px solid var(--line);
  position: relative;
  overflow: hidden;
}
.fund-qual::before {
  content: ""; position: absolute; inset: 0;
  background:
    radial-gradient(60% 50% at 0% 0%, rgba(230,49,121,0.08), transparent 60%),
    radial-gradient(50% 40% at 100% 100%, rgba(200,154,74,0.08), transparent 60%);
  pointer-events: none;
}
.fund-qual .container { position: relative; z-index: 1; }
.fund-card {
  max-width: 760px; margin: 0 auto;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 42px 44px 36px;
  box-shadow:
    0 40px 80px -25px rgba(20,16,14,0.18),
    0 0 0 1px rgba(20,16,14,0.02);
}

.fund-header { text-align: center; margin-bottom: 26px; }
.fund-badge {
  display: inline-block;
  font-size: 10.5px; letter-spacing: 0.16em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 6px 14px; border-radius: 100px;
  margin-bottom: 16px;
}
.fund-header h2 {
  font-size: clamp(1.7rem, 3.2vw, 2.4rem);
  margin-bottom: 8px;
  letter-spacing: -0.025em;
}
.fund-header p {
  font-size: 14.5px; color: var(--ink-2);
  max-width: 520px; margin: 0 auto;
  line-height: 1.55;
}

.fund-progress {
  display: flex; align-items: center; gap: 16px;
  margin-bottom: 28px;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--line);
}
.fund-progress-bar {
  flex: 1;
  height: 6px; border-radius: 100px;
  background: var(--bg-2);
  overflow: hidden;
}
.fund-progress-bar > span {
  display: block; height: 100%;
  width: 11%;
  background: linear-gradient(90deg, var(--pink), #ff7eb3);
  border-radius: 100px;
  transition: width .45s cubic-bezier(.2,.7,.2,1);
}
.fund-step-count {
  font-size: 12.5px; font-weight: 600; color: var(--ink-3);
  letter-spacing: 0.04em;
  flex-shrink: 0;
}
.fund-step-count strong { color: var(--ink); font-weight: 700; }

.fund-step { display: none; }
.fund-step.active {
  display: block;
  animation: fundFade .4s cubic-bezier(.2,.7,.2,1);
}
@keyframes fundFade {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.fund-step-tag {
  display: inline-block;
  font-size: 10.5px; letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--pink); font-weight: 700;
  margin-bottom: 12px;
}
.fund-step h3 {
  font-size: 22px; font-weight: 600;
  letter-spacing: -0.02em;
  margin-bottom: 8px;
  line-height: 1.3;
  color: var(--ink);
}
.fund-step-sub {
  font-size: 14px; color: var(--ink-2);
  margin-bottom: 18px;
  line-height: 1.55;
}
.fund-step-question {
  font-size: 14.5px; color: var(--ink);
  font-weight: 600;
  margin: 12px 0 14px;
}

.fund-options { display: flex; flex-direction: column; gap: 10px; }
.fund-options label {
  position: relative;
  display: flex; align-items: center;
  padding: 16px 18px;
  background: #fff;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  cursor: pointer;
  transition: border-color .2s, background .2s, transform .2s;
  user-select: none;
}
.fund-options label:hover {
  border-color: var(--pink);
  background: var(--pink-tint);
  transform: translateX(2px);
}
.fund-options label.is-checked {
  border-color: var(--pink);
  background: var(--pink-soft);
  box-shadow: inset 0 0 0 1px var(--pink);
}
.fund-options input { position: absolute; opacity: 0; pointer-events: none; }
.fund-options label > span {
  display: flex; align-items: center; gap: 14px;
  font-size: 14.5px; color: var(--ink); font-weight: 500;
  flex: 1;
  line-height: 1.45;
}
.fund-options label > span small {
  color: var(--ink-3); font-weight: 400; font-size: 12.5px;
  display: inline;
}
.fund-options .opt-letter {
  display: grid; place-items: center;
  width: 30px; height: 30px;
  border-radius: 50%;
  background: var(--bg-2);
  color: var(--ink-3);
  font-size: 12.5px; font-weight: 700;
  flex-shrink: 0;
  transition: background .2s, color .2s, box-shadow .2s;
}
.fund-options label.is-checked .opt-letter {
  background: var(--pink); color: #fff;
  box-shadow: 0 6px 14px -6px rgba(230,49,121,0.55);
}
.fund-options-multi label.is-checked .opt-letter::after { content: "✓"; }
.fund-options-multi label.is-checked .opt-letter { font-size: 0; }
.fund-options-multi label.is-checked .opt-letter::after { font-size: 14px; }

/* Step 9 — Contact fields */
.fund-success-box {
  background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
  border: 1px solid #c9eccd;
  border-left: 4px solid #22c55e;
  border-radius: 16px;
  padding: 22px 26px;
  margin-bottom: 26px;
  box-shadow: 0 14px 28px -16px rgba(34,197,94,0.25);
}
.fund-success-box strong {
  display: block; font-size: 17px;
  color: var(--ink); font-weight: 700;
  margin-bottom: 8px;
  letter-spacing: -0.01em;
}
.fund-success-box p {
  font-size: 13.5px; color: var(--ink-2); line-height: 1.55;
  margin-bottom: 8px;
}
.fund-success-box p:last-child { margin-bottom: 0; }

.fund-fields { display: flex; flex-direction: column; gap: 14px; }
.fund-fields .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.fund-field { display: flex; flex-direction: column; gap: 6px; }
.fund-field .lab {
  font-size: 11px; font-weight: 700;
  color: var(--ink-2);
  letter-spacing: 0.08em; text-transform: uppercase;
}
.fund-field .lab em { color: var(--pink); font-style: normal; font-weight: 700; }
.fund-field input {
  padding: 13px 14px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  font-size: 14.5px; font-family: inherit;
  background: #fff; color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
  width: 100%;
}
.fund-field input::placeholder { color: var(--ink-3); }
.fund-field input:focus {
  outline: none; border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.fund-field .ph-wrap { position: relative; }
.fund-field .ph-prefix {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  font-size: 14px; font-weight: 600; color: var(--ink); pointer-events: none;
}
.fund-field .ph-wrap input { padding-left: 72px; }

/* Nav */
.fund-nav {
  display: flex; justify-content: space-between; align-items: center; gap: 12px;
  margin-top: 28px;
  padding-top: 22px;
  border-top: 1px solid var(--line);
}
.fund-back {
  padding: 11px 18px;
  background: transparent; color: var(--ink-3);
  border: none; border-radius: 100px;
  font-family: inherit;
  font-size: 13.5px; font-weight: 600;
  cursor: pointer;
  transition: color .2s, background .2s;
}
.fund-back:hover { color: var(--ink); background: var(--bg-2); }

.fund-next, .fund-submit {
  padding: 14px 26px;
  background: var(--pink); color: #fff;
  border: none; border-radius: 100px;
  font-family: inherit;
  font-size: 14px; font-weight: 600;
  cursor: pointer;
  display: inline-flex; align-items: center; gap: 8px;
  box-shadow: 0 14px 28px -10px rgba(230,49,121,0.55);
  transition: background .2s, transform .2s, box-shadow .2s, opacity .2s;
  margin-left: auto;
}
.fund-next .arr, .fund-submit .arr { transition: transform .2s; }
.fund-next:hover:not(:disabled), .fund-submit:hover:not(:disabled) {
  background: var(--ink); transform: translateY(-2px);
  box-shadow: 0 18px 32px -10px rgba(20,16,14,0.4);
}
.fund-next:hover:not(:disabled) .arr,
.fund-submit:hover:not(:disabled) .arr { transform: translateX(4px); }
.fund-next:disabled, .fund-submit:disabled {
  opacity: 0.4; cursor: not-allowed; box-shadow: none;
}

/* Final success */
.fund-final-success {
  text-align: center;
  padding: 40px 20px 20px;
}
.fund-final-success .ico {
  width: 76px; height: 76px; border-radius: 50%;
  background: linear-gradient(135deg, var(--pink), #ff7eb3);
  color: #fff;
  display: grid; place-items: center;
  font-size: 38px; font-weight: 700;
  margin: 0 auto 20px;
  box-shadow: 0 22px 40px -12px rgba(230,49,121,0.6);
  animation: leadPop .6s cubic-bezier(.2,1.4,.4,1);
}
.fund-final-success h3 {
  font-size: 26px; font-weight: 600; letter-spacing: -0.02em;
  margin-bottom: 12px;
}
.fund-final-success p {
  font-size: 15px; color: var(--ink-2);
  max-width: 460px; margin: 0 auto;
  line-height: 1.6;
}

@media (max-width: 700px) {
  .fund-qual { padding: 70px 0; }
  .fund-card { padding: 30px 22px 26px; }
  .fund-step h3 { font-size: 18px; }
  .fund-options label { padding: 14px 14px; }
  .fund-options label > span { gap: 12px; font-size: 14px; }
  .fund-options .opt-letter { width: 28px; height: 28px; font-size: 12px; }
  .fund-fields .row { grid-template-columns: 1fr; }
  .fund-success-box { padding: 18px 20px; }
  .fund-nav { padding-top: 18px; margin-top: 22px; }
  .fund-next, .fund-submit { padding: 12px 20px; font-size: 13.5px; }
}

/* ===== CONTACT PAGE — form + Calendly ===== */
.ct-hero {
  padding: 140px 0 30px;
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(60% 50% at 0% 0%, rgba(230,49,121,0.10), transparent 65%),
    radial-gradient(50% 40% at 100% 100%, rgba(200,154,74,0.08), transparent 65%),
    linear-gradient(180deg, var(--bg) 0%, #fff 100%);
}
.ct-hero-text {
  max-width: 720px;
  margin: 0 auto;
  text-align: center;
}
.ct-hero-text .eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  margin-bottom: 16px;
}
.ct-hero-text .eyebrow::before { display: none; }
.ct-eye-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 4px rgba(34,197,94,0.18);
  animation: pulse 1.8s infinite;
}
.ct-hero-text h1 {
  font-size: clamp(2.4rem, 5vw, 4.2rem);
  letter-spacing: -0.035em;
  margin-bottom: 18px;
}
.ct-hero-text .lede {
  max-width: 560px;
  margin: 0 auto 24px;
  font-size: 17.5px; line-height: 1.6;
  color: var(--ink-2);
}
.ct-hero-trust {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-wrap: wrap; gap: 10px;
  justify-content: center;
}
.ct-hero-trust li {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff;
  border: 1px solid var(--line);
  padding: 8px 14px;
  border-radius: 100px;
  font-size: 12.5px; font-weight: 600; color: var(--ink-2);
  box-shadow: 0 6px 14px -10px rgba(20,16,14,0.2);
}
.ct-hero-trust li span { font-size: 14px; }

/* Body — form + calendar grid */
.ct-body-section { padding: 30px 0 100px; }
.ct-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  align-items: stretch;
}
.ct-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 32px 32px 28px;
  box-shadow:
    0 30px 60px -30px rgba(20,16,14,0.18),
    0 0 0 1px rgba(20,16,14,0.02);
  display: flex;
  flex-direction: column;
  height: 100%;
}
.ct-form-card .ct-form { flex: 1; display: flex; flex-direction: column; }
.ct-form-card .ct-submit { margin-top: auto; }
.ct-cal-card .ct-cal-embed { flex: 1; min-height: 700px; display: flex; }
.ct-cal-card .ct-cal-embed > .calendly-inline-widget,
.ct-cal-card .ct-cal-embed > iframe { flex: 1; width: 100%; }
.ct-form-head { margin-bottom: 20px; }
.ct-card-tag {
  display: inline-block;
  font-size: 10.5px; letter-spacing: 0.16em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 5px 10px; border-radius: 100px;
  margin-bottom: 12px;
}
.ct-form-head h2 {
  font-size: 26px; font-weight: 600;
  letter-spacing: -0.025em;
  margin: 0 0 8px;
}
.ct-form-head p { font-size: 13.5px; color: var(--ink-2); margin: 0; line-height: 1.55; }

/* Form */
.ct-alert {
  margin-bottom: 18px;
  padding: 14px 18px;
  border-radius: 12px;
  font-size: 13.5px;
}
.ct-alert.error {
  background: #fff3f3;
  border: 1px solid #fbd6d6;
  color: #8b1414;
}
.ct-alert ul { margin: 4px 0 0; padding-left: 18px; }

.ct-form { display: flex; flex-direction: column; gap: 14px; }
.ct-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ct-field { display: flex; flex-direction: column; gap: 6px; }
.ct-lab {
  font-size: 11px; font-weight: 700;
  color: var(--ink-2);
  letter-spacing: 0.08em; text-transform: uppercase;
}
.ct-lab em { color: var(--pink); font-weight: 700; font-style: normal; }
.ct-lab small { color: var(--ink-3); font-weight: 500; text-transform: none; letter-spacing: 0; margin-left: 4px; }

.ct-field input,
.ct-field select,
.ct-field textarea {
  width: 100%;
  padding: 13px 14px;
  font-size: 14.5px;
  font-family: inherit;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
}
.ct-field input::placeholder,
.ct-field textarea::placeholder { color: var(--ink-3); }
.ct-field input:focus,
.ct-field select:focus,
.ct-field textarea:focus {
  outline: none;
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.ct-field textarea { resize: vertical; min-height: 130px; line-height: 1.55; }
.ct-field select {
  appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path d='M2 4l4 4 4-4' stroke='%2315110f' stroke-width='1.6' fill='none' stroke-linecap='round'/></svg>");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}

.ct-submit {
  margin-top: 10px;
  align-self: stretch;
  padding: 15px 28px;
  background: var(--pink); color: #fff;
  border: none; border-radius: 100px;
  font-family: inherit;
  font-size: 14.5px; font-weight: 600;
  cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  box-shadow: 0 16px 30px -12px rgba(230,49,121,0.55);
  transition: background .2s, transform .2s, box-shadow .2s;
}
.ct-submit:hover {
  background: var(--ink); transform: translateY(-2px);
  box-shadow: 0 20px 36px -12px rgba(20,16,14,0.4);
}
.ct-submit .arr { transition: transform .2s; }
.ct-submit:hover .arr { transform: translateX(4px); }
.ct-fine {
  font-size: 11.5px; color: var(--ink-3);
  margin: 8px 0 0;
}

/* Calendar */
.ct-cal-embed {
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid var(--line);
  background: var(--bg-2);
}
.ct-cal-embed iframe {
  display: block; width: 100%;
  border: 0;
}
.ct-cal-placeholder {
  border-radius: 14px;
  background: linear-gradient(180deg, var(--bg-2) 0%, #fff 100%);
  border: 1px dashed var(--line-2);
  padding: 50px 30px;
  text-align: center;
}
.ct-cal-ico { font-size: 44px; margin-bottom: 12px; }
.ct-cal-placeholder strong { display: block; font-size: 17px; color: var(--ink); margin-bottom: 6px; }
.ct-cal-placeholder p { font-size: 14px; color: var(--ink-2); margin-bottom: 20px; }
.ct-cal-placeholder code {
  background: var(--bg-2); padding: 2px 6px; border-radius: 4px;
  font-size: 12px; font-family: monospace;
}

.ct-cal-foot {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
  margin-top: 18px;
}
.ct-cal-foot-item {
  padding: 12px 14px;
  background: var(--bg-2);
  border-radius: 12px;
}
.ct-cal-foot-item strong {
  display: block; font-size: 13px; color: var(--ink); font-weight: 700;
  margin-bottom: 2px;
}
.ct-cal-foot-item span { font-size: 11.5px; color: var(--ink-3); }

/* Direct contact strip */
.ct-direct {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 30px;
}
.ct-direct-item {
  display: flex; align-items: center; gap: 12px;
  padding: 16px 18px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 14px;
  transition: border-color .2s, transform .2s;
}
.ct-direct-item:hover { border-color: var(--pink-soft); transform: translateY(-2px); }
.ct-direct-ico { font-size: 20px; flex-shrink: 0; }
.ct-direct-item strong { display: block; font-size: 11.5px; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 2px; }
.ct-direct-item a,
.ct-direct-item span { font-size: 14px; color: var(--ink); font-weight: 600; }
.ct-direct-item a:hover { color: var(--pink); }

/* Success message */
.ct-success {
  display: flex; align-items: center; gap: 18px;
  background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
  border: 1px solid #c9eccd;
  border-left: 4px solid #22c55e;
  border-radius: 16px;
  padding: 22px 26px;
  margin-bottom: 24px;
}
.ct-success-ico {
  width: 44px; height: 44px; border-radius: 50%;
  background: #22c55e; color: #fff;
  display: grid; place-items: center;
  font-size: 20px; font-weight: 700;
  flex-shrink: 0;
}
.ct-success h2 {
  margin: 0 0 4px; font-size: 18px; font-weight: 700;
  letter-spacing: -0.01em;
}
.ct-success p { margin: 0; font-size: 13.5px; color: var(--ink-2); line-height: 1.55; }

@media (max-width: 1000px) {
  .ct-grid { grid-template-columns: 1fr; }
  .ct-direct { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
  .ct-card { padding: 26px 22px 22px; }
  .ct-row { grid-template-columns: 1fr; }
  .ct-cal-foot { grid-template-columns: 1fr; }
}

/* ===== ONBOARDING PAGE — post-payment premium form ===== */
.ob-hero {
  position: relative;
  padding: 140px 0 60px;
  overflow: hidden;
  background:
    radial-gradient(70% 60% at 0% 0%, rgba(230,49,121,0.12), transparent 65%),
    radial-gradient(60% 50% at 100% 100%, rgba(200,154,74,0.10), transparent 65%),
    linear-gradient(180deg, var(--bg) 0%, #fff 100%);
}
.ob-hero::before,
.ob-hero::after {
  content: ""; position: absolute; pointer-events: none; z-index: 0;
}
.ob-hero::before {
  top: -120px; right: -160px;
  width: 540px; height: 540px; border-radius: 50%;
  background: radial-gradient(circle, rgba(230,49,121,0.16), transparent 70%);
  filter: blur(6px);
}
.ob-hero::after {
  bottom: -180px; left: -160px;
  width: 460px; height: 460px; border-radius: 50%;
  background: radial-gradient(circle, rgba(200,154,74,0.12), transparent 70%);
  filter: blur(6px);
}
.ob-hero .container { position: relative; z-index: 1; }

.ob-hero-grid {
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  gap: 60px;
  align-items: center;
}
.ob-hero-text .eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  margin-bottom: 18px;
}
.ob-hero-text .eyebrow::before { display: none; }
.ob-eye-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 4px rgba(34,197,94,0.18);
  animation: pulse 1.8s infinite;
}
.ob-hero-text h1 {
  font-size: clamp(2rem, 4vw, 3.4rem);
  font-weight: 600;
  line-height: 1.04;
  letter-spacing: -0.035em;
  margin-bottom: 20px;
}
.ob-hero-text .lede {
  max-width: 520px;
  font-size: 17px; line-height: 1.6;
  color: var(--ink-2);
  margin-bottom: 30px;
}
.ob-hero-trust {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-wrap: wrap; gap: 12px;
}
.ob-hero-trust li {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff;
  border: 1px solid var(--line);
  padding: 8px 14px;
  border-radius: 100px;
  font-size: 12.5px; font-weight: 600; color: var(--ink-2);
  box-shadow: 0 6px 14px -10px rgba(20,16,14,0.2);
}
.ob-hero-trust li span { font-size: 14px; }

/* Hero video card */
.ob-hero-video { display: flex; flex-direction: column; gap: 14px; }
.ob-video-thumb {
  position: relative;
  aspect-ratio: 16/10;
  border-radius: var(--r-lg);
  overflow: hidden;
  background:
    radial-gradient(70% 80% at 50% 60%, rgba(230,49,121,0.5), transparent 70%),
    linear-gradient(135deg, #1f0e18 0%, #2c1622 40%, #0f0a0c 100%);
  display: grid; place-items: center;
  cursor: pointer;
  box-shadow:
    0 50px 90px -30px rgba(20,16,14,0.55),
    0 0 0 1px rgba(255,255,255,0.04) inset;
  transition: transform .45s cubic-bezier(.2,.7,.2,1), box-shadow .45s;
}
.ob-video-thumb::before {
  content: "";
  position: absolute; inset: 0;
  background:
    repeating-linear-gradient(
      45deg,
      rgba(255,255,255,0.015) 0 2px,
      transparent 2px 14px
    );
  pointer-events: none;
}
.ob-video-thumb:hover {
  transform: translateY(-4px);
  box-shadow: 0 60px 100px -28px rgba(20,16,14,0.6), 0 0 0 1px rgba(230,49,121,0.25) inset;
}
.ob-play {
  position: relative; z-index: 2;
  width: 84px; height: 84px; border-radius: 50%;
  background: #fff;
  color: var(--pink);
  display: grid; place-items: center;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.45), 0 0 0 6px rgba(255,255,255,0.12);
  transition: transform .35s cubic-bezier(.2,.7,.2,1), box-shadow .35s;
}
.ob-play svg { translate: 2px 0; }
.ob-video-thumb:hover .ob-play {
  transform: scale(1.1);
  box-shadow: 0 26px 50px -10px rgba(230,49,121,0.5), 0 0 0 8px rgba(255,255,255,0.18);
}
.ob-video-tag {
  position: absolute; top: 18px; left: 18px; z-index: 2;
  font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase;
  font-weight: 700; color: #fff;
  padding: 6px 12px; border-radius: 100px;
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.18);
}
.ob-video-meta {
  position: absolute; left: 22px; right: 22px; bottom: 20px; z-index: 2;
  color: #fff;
  display: flex; align-items: end; justify-content: space-between; gap: 14px;
}
.ob-video-title {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 22px; line-height: 1.1;
  letter-spacing: -0.01em;
  max-width: 80%;
}
.ob-video-duration {
  font-size: 11px; font-weight: 600; letter-spacing: 0.06em;
  background: rgba(0,0,0,0.55); color: #fff;
  padding: 4px 9px; border-radius: 6px;
  backdrop-filter: blur(6px);
}
.ob-video-note {
  text-align: center;
  font-size: 13px; color: var(--ink-3);
  letter-spacing: 0.02em;
}
.ob-video-note strong {
  color: var(--pink);
  font-weight: 700;
}

/* ===== Must Watch banner ===== */
.ob-must-watch {
  display: inline-flex;
  align-self: flex-start;
  align-items: center;
  gap: 10px;
  padding: 8px 18px 8px 14px;
  border-radius: 100px;
  background: linear-gradient(135deg, #e63179 0%, #b81f5d 100%);
  color: #fff;
  font-size: 11.5px;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  box-shadow:
    0 18px 32px -16px rgba(230,49,121,0.7),
    0 0 0 1px rgba(255,255,255,0.08) inset;
  animation: obMwFloat 3.6s ease-in-out infinite;
  position: relative;
  z-index: 2;
}
.ob-must-watch::before {
  content: "";
  position: absolute; inset: -2px;
  border-radius: inherit;
  background: linear-gradient(135deg, rgba(230,49,121,0.55), rgba(200,154,74,0.35));
  filter: blur(14px);
  z-index: -1;
  opacity: 0.7;
}
.ob-mw-pulse {
  width: 9px; height: 9px; border-radius: 50%;
  background: #fff;
  box-shadow: 0 0 0 0 rgba(255,255,255,0.75);
  animation: obMwDot 1.6s infinite;
  flex-shrink: 0;
}
.ob-mw-text { line-height: 1; }
@keyframes obMwDot {
  0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.75); }
  70%  { box-shadow: 0 0 0 10px rgba(255,255,255,0); }
  100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
}
@keyframes obMwFloat {
  0%,100% { transform: translateY(0); }
  50%     { transform: translateY(-3px); }
}

/* ===== Video card (real <video>) ===== */
.ob-video-card {
  position: relative;
  aspect-ratio: 16/10;
  border-radius: var(--r-lg);
  overflow: hidden;
  background:
    radial-gradient(70% 80% at 50% 60%, rgba(230,49,121,0.5), transparent 70%),
    linear-gradient(135deg, #1f0e18 0%, #2c1622 40%, #0f0a0c 100%);
  box-shadow:
    0 50px 90px -30px rgba(20,16,14,0.55),
    0 0 0 1px rgba(255,255,255,0.04) inset;
  transition: transform .45s cubic-bezier(.2,.7,.2,1), box-shadow .45s;
}
.ob-video-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 60px 100px -28px rgba(20,16,14,0.6),
    0 0 0 1px rgba(230,49,121,0.28) inset;
}
.ob-video-el {
  position: absolute; inset: 0;
  display: block;
  width: 100%; height: 100%;
  object-fit: cover;
  background: #0f0a0c;
  border: 0;
}
.ob-video-overlay {
  position: absolute; inset: 0; z-index: 3;
  display: block;
  width: 100%; height: 100%;
  border: 0; padding: 0; margin: 0;
  color: #fff;
  cursor: pointer;
  font: inherit;
  text-align: left;
  background:
    linear-gradient(180deg, rgba(0,0,0,0.15) 0%, transparent 35%, transparent 55%, rgba(0,0,0,0.6) 100%),
    radial-gradient(60% 70% at 50% 55%, rgba(230,49,121,0.35), transparent 70%);
  transition: opacity .4s ease, visibility .4s;
}
.ob-video-overlay::before {
  content: "";
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    45deg,
    rgba(255,255,255,0.02) 0 2px,
    transparent 2px 14px
  );
  pointer-events: none;
}
.ob-video-overlay .ob-play {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
}
.ob-video-overlay:hover .ob-play {
  transform: translate(-50%, -50%) scale(1.1);
  box-shadow: 0 26px 50px -10px rgba(230,49,121,0.55), 0 0 0 8px rgba(255,255,255,0.18);
}
.ob-video-card.is-playing .ob-video-overlay {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}
.ob-video-el::-webkit-media-controls-panel { background: linear-gradient(180deg, transparent, rgba(0,0,0,0.6)); }

/* Form section */
.ob-form-section {
  padding: 50px 0 100px;
  background: var(--bg);
}
.ob-form-wrap { position: relative; }

.ob-callout {
  display: flex; align-items: flex-start; gap: 14px;
  max-width: 820px; margin: 0 auto 26px;
  padding: 20px 24px;
  background: linear-gradient(135deg, var(--pink-tint) 0%, #fff 100%);
  border: 1px solid var(--pink-soft);
  border-left: 4px solid var(--pink);
  border-radius: 16px;
  box-shadow: 0 10px 24px -16px rgba(230,49,121,0.25);
}
.ob-callout-ico {
  display: grid; place-items: center;
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--pink); color: #fff;
  font-size: 16px; flex-shrink: 0;
  box-shadow: 0 8px 18px -8px rgba(230,49,121,0.55);
}
.ob-callout strong {
  display: block; color: var(--ink); font-weight: 700;
  font-size: 15.5px; margin-bottom: 4px;
  letter-spacing: -0.01em;
}
.ob-callout p { font-size: 13.5px; color: var(--ink-2); margin: 0; line-height: 1.55; }

.ob-alert {
  max-width: 820px; margin: 0 auto 20px;
  padding: 18px 22px;
  border-radius: 14px;
  font-size: 14px;
}
.ob-alert.error {
  background: #fff3f3;
  border: 1px solid #fbd6d6;
  color: #8b1414;
}
.ob-alert strong { display: block; margin-bottom: 6px; }
.ob-alert ul { margin: 4px 0 0; padding-left: 20px; }
.ob-alert ul li { line-height: 1.5; }
.ob-alert p { margin: 0; }

.ob-form {
  max-width: 820px; margin: 0 auto;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-lg);
  padding: 42px 42px 36px;
  box-shadow:
    0 40px 80px -30px rgba(20,16,14,0.2),
    0 0 0 1px rgba(20,16,14,0.02);
}
.ob-form-head {
  display: flex; justify-content: space-between; align-items: end; gap: 16px;
  margin-bottom: 30px;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--line);
}
.ob-form-head .eyebrow { margin-bottom: 12px; }
.ob-form-head h2 {
  font-size: 28px; font-weight: 600;
  letter-spacing: -0.025em;
  margin: 0;
}
.ob-req {
  font-size: 12px; color: var(--ink-3);
  font-style: italic;
  flex-shrink: 0;
}
.ob-req em { color: var(--pink); font-style: normal; font-weight: 700; }

/* Sections inside the form */
.ob-section {
  border: 0; padding: 24px 0 6px; margin: 0;
  position: relative;
}
.ob-section + .ob-section { border-top: 1px solid var(--line); }
.ob-section legend {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 16px;
  padding: 0;
  width: 100%;
}
.ob-section-num {
  display: grid; place-items: center;
  width: 28px; height: 28px;
  border-radius: 50%;
  background: var(--ink); color: #fff;
  font-size: 11px; font-weight: 700; letter-spacing: 0.04em;
}
.ob-section-ttl {
  font-size: 18px; font-weight: 600;
  color: var(--ink);
  letter-spacing: -0.015em;
}
.ob-section-pad {
  margin-left: auto;
  font-size: 10.5px; letter-spacing: 0.14em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 5px 10px; border-radius: 100px;
}
.ob-section-secure { background: linear-gradient(180deg, rgba(253,234,242,0.18) 0%, transparent 60%); border-radius: 14px; padding-left: 14px; padding-right: 14px; }
.ob-section-intro {
  font-size: 13px; color: var(--ink-2);
  margin: -6px 0 14px;
  line-height: 1.55;
}

.ob-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
}
.ob-field { display: flex; flex-direction: column; gap: 6px; }
.ob-field-wide { grid-column: 1 / -1; }
.ob-lab {
  font-size: 11.5px; font-weight: 700;
  color: var(--ink-2);
  letter-spacing: 0.06em; text-transform: uppercase;
}
.ob-lab em {
  color: var(--pink); font-weight: 700; font-style: normal;
}
.ob-help {
  font-size: 11.5px; color: var(--ink-3);
  margin-top: 2px;
}

.ob-input-wrap {
  position: relative;
  display: flex; align-items: stretch;
}
.ob-input-wrap input { flex: 1; }
.ob-input-state {
  position: absolute; top: 50%; right: 14px;
  transform: translateY(-50%);
  width: 18px; height: 18px;
  border-radius: 50%;
  display: grid; place-items: center;
  font-size: 10px; font-weight: 700;
  opacity: 0;
  transition: opacity .25s;
  pointer-events: none;
}
.ob-field input.is-valid ~ .ob-input-state,
.ob-field input.is-valid + .ob-input-state {
  opacity: 1;
  background: #22c55e;
  color: #fff;
}
.ob-field input.is-valid ~ .ob-input-state::before,
.ob-field input.is-valid + .ob-input-state::before { content: "✓"; }
.ob-field input.is-invalid ~ .ob-input-state,
.ob-field input.is-invalid + .ob-input-state {
  opacity: 1;
  background: #ef4444;
  color: #fff;
}
.ob-field input.is-invalid ~ .ob-input-state::before,
.ob-field input.is-invalid + .ob-input-state::before { content: "!"; }

.ob-field input,
.ob-field select {
  width: 100%;
  padding: 14px 16px;
  font-size: 14.5px;
  font-family: inherit;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s, background .2s;
}
.ob-field input::placeholder { color: var(--ink-3); }
.ob-field input:focus,
.ob-field select:focus {
  outline: none;
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.ob-field input.is-valid {
  border-color: #22c55e;
  background: #f6fef9;
}
.ob-field input.is-invalid {
  border-color: #ef4444;
  background: #fffafa;
}
.ob-input-wrap input.is-valid,
.ob-input-wrap input.is-invalid {
  padding-right: 42px;
}
.ob-field select {
  appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path d='M2 4l4 4 4-4' stroke='%2315110f' stroke-width='1.6' fill='none' stroke-linecap='round'/></svg>");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 38px;
}
.ob-field select.is-valid { border-color: #22c55e; }

/* Phone prefix */
.ob-phone-wrap input { padding-left: 76px; }
.ob-phone-prefix {
  position: absolute; left: 14px; top: 50%;
  transform: translateY(-50%);
  font-size: 14px; font-weight: 600;
  color: var(--ink);
  letter-spacing: 0.02em;
  pointer-events: none;
}

/* SSN toggle */
.ob-ssn-toggle {
  position: absolute; right: 8px; top: 50%;
  transform: translateY(-50%);
  width: 32px; height: 32px;
  border-radius: 8px;
  background: transparent;
  color: var(--ink-3);
  border: none; cursor: pointer;
  display: grid; place-items: center;
  transition: color .2s, background .2s;
}
.ob-ssn-toggle:hover { color: var(--pink); background: var(--pink-soft); }
.ob-ssn-toggle.on { color: var(--pink); }
.ob-input-wrap input#ob-ssn { padding-right: 50px; letter-spacing: 0.08em; }

/* DOB three-select */
.ob-dob {
  display: grid; grid-template-columns: 1.4fr 0.8fr 1fr; gap: 8px;
}
.ob-dob select { padding: 14px 12px; }
.ob-dob select.is-valid { border-color: #22c55e; }

/* Submit */
.ob-submit-wrap { margin-top: 32px; text-align: center; }
.ob-submit {
  position: relative;
  width: 100%; max-width: 420px;
  padding: 18px 28px;
  background: var(--pink); color: #fff;
  border: none; border-radius: 100px;
  font-family: inherit;
  font-size: 15.5px; font-weight: 600;
  letter-spacing: 0.01em;
  cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 20px 40px -12px rgba(230,49,121,0.6);
  transition: background .2s, transform .2s, box-shadow .2s, opacity .2s;
  overflow: hidden;
}
.ob-submit:hover:not(:disabled) {
  background: var(--ink);
  transform: translateY(-2px);
  box-shadow: 0 24px 44px -12px rgba(20,16,14,0.4);
}
.ob-submit:disabled { opacity: 0.85; cursor: progress; }
.ob-submit .arr { transition: transform .2s; }
.ob-submit:hover:not(:disabled) .arr { transform: translateX(4px); }
.ob-submit-spinner {
  display: none;
  width: 18px; height: 18px;
  border: 2px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: obSpin .7s linear infinite;
}
.ob-submit.is-loading .ob-submit-label,
.ob-submit.is-loading .arr { opacity: 0.5; }
.ob-submit.is-loading .ob-submit-spinner { display: inline-block; }
@keyframes obSpin { to { transform: rotate(360deg); } }
.ob-submit-fine {
  font-size: 11.5px; color: var(--ink-3);
  margin-top: 14px;
  letter-spacing: 0.01em;
}
.ob-submit-fine a { color: var(--pink); font-weight: 600; }

/* Trust badges */
.ob-badges {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 30px;
  padding-top: 26px;
  border-top: 1px solid var(--line);
}
.ob-badge {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px;
  background: var(--bg-2);
  border-radius: 12px;
}
.ob-badge .ico {
  width: 36px; height: 36px;
  display: grid; place-items: center;
  font-size: 16px;
  background: #fff;
  border-radius: 10px;
  flex-shrink: 0;
  box-shadow: 0 4px 10px -4px rgba(20,16,14,0.18);
}
.ob-badge strong { display: block; font-size: 12.5px; color: var(--ink); font-weight: 700; }
.ob-badge small  { display: block; font-size: 11px; color: var(--ink-3); margin-top: 1px; }

/* Success state */
.ob-hero-success { padding: 160px 0 100px; }
.ob-success {
  max-width: 680px; margin: 0 auto;
  text-align: center;
}
.ob-success-eyebrow {
  display: inline-block;
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--pink); font-weight: 700;
  margin-bottom: 14px;
}
.ob-success-ico {
  width: 96px; height: 96px; border-radius: 50%;
  background: linear-gradient(135deg, var(--pink), #ff7eb3);
  color: #fff;
  display: grid; place-items: center;
  font-size: 44px; font-weight: 700;
  margin: 0 auto 24px;
  box-shadow: 0 26px 50px -12px rgba(230,49,121,0.6);
  animation: leadPop .6s cubic-bezier(.2,1.4,.4,1);
}
.ob-success h1 {
  font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 600;
  letter-spacing: -0.025em;
  margin-bottom: 18px;
}
.ob-success .lede {
  font-size: 17px; color: var(--ink-2); line-height: 1.6;
  max-width: 540px; margin: 0 auto 32px;
}
.ob-success-next {
  text-align: left;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 18px;
  padding: 24px 28px;
  max-width: 560px; margin: 0 auto 28px;
  box-shadow: 0 20px 40px -20px rgba(20,16,14,0.18);
}
.ob-success-next-head {
  font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase;
  font-weight: 700; color: var(--ink-3); margin-bottom: 14px;
}
.ob-success-next ol {
  list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 10px;
}
.ob-success-next ol li {
  display: flex; align-items: flex-start; gap: 12px;
  font-size: 14.5px; color: var(--ink); line-height: 1.5;
}
.ob-success-next ol li span {
  flex-shrink: 0;
  display: grid; place-items: center;
  width: 24px; height: 24px;
  border-radius: 50%;
  background: var(--pink); color: #fff;
  font-size: 11px; font-weight: 700;
}
.ob-success-ctas { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }

/* Steps section */
.ob-steps-section {
  padding: 100px 0;
  background: var(--bg-2);
  border-block: 1px solid var(--line);
}
.ob-steps {
  display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px;
}
.ob-step {
  position: relative;
  padding: 26px 22px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  transition: transform .35s, box-shadow .35s, border-color .35s;
}
.ob-step:hover {
  transform: translateY(-4px);
  box-shadow: 0 24px 48px -20px rgba(20,16,14,0.18);
  border-color: var(--line-2);
}
.ob-step-critical {
  background: linear-gradient(180deg, #fff5f9 0%, #fff 100%);
  border-color: var(--pink-soft);
}
.ob-step-num {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 44px;
  line-height: 1;
  color: var(--pink);
  letter-spacing: -0.04em;
  margin-bottom: 14px;
}
.ob-step-tag {
  display: inline-block;
  font-size: 9.5px; letter-spacing: 0.14em; text-transform: uppercase;
  font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 4px 10px; border-radius: 100px;
  margin-bottom: 10px;
}
.ob-step h3 {
  font-size: 16px; font-weight: 600;
  margin-bottom: 8px; color: var(--ink);
  letter-spacing: -0.01em; line-height: 1.3;
}
.ob-step p {
  font-size: 13px; line-height: 1.55;
  color: var(--ink-2); margin-bottom: 8px;
}
.ob-step-note {
  display: block; margin-top: 8px;
  font-size: 11.5px; color: var(--ink-3); font-style: italic;
}
.ob-list { list-style: none; padding: 0; margin: 6px 0 0; display: flex; flex-direction: column; gap: 5px; }
.ob-list li {
  position: relative; padding-left: 18px;
  font-size: 12.5px; line-height: 1.45; color: var(--ink-2);
}
.ob-list li::before {
  content: "✓"; position: absolute; left: 0; top: 0;
  color: var(--pink); font-weight: 700; font-size: 12px;
}
.ob-step-cta { margin-top: 14px; font-size: 13px; padding: 10px 18px; }

/* Testimonials */
.ob-tests-section { padding: 100px 0; }
.ob-tests {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
  margin-bottom: 40px;
}
.ob-test {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 28px 26px;
  transition: transform .35s, box-shadow .35s;
}
.ob-test:hover { transform: translateY(-4px); box-shadow: 0 24px 48px -20px rgba(20,16,14,0.18); }
.ob-test .stars { color: var(--gold); letter-spacing: 2px; font-size: 13px; margin-bottom: 14px; }
.ob-test h4 {
  font-size: 17px; font-weight: 600; line-height: 1.35;
  letter-spacing: -0.01em;
  color: var(--ink); margin-bottom: 12px;
}
.ob-test > p {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 17px; line-height: 1.45;
  color: var(--ink-2); margin-bottom: 20px;
}
.ob-test-who {
  display: flex; align-items: center; gap: 12px;
  padding-top: 16px; border-top: 1px solid var(--line);
}
.ob-test-who .av {
  width: 38px; height: 38px; border-radius: 50%;
  background: var(--grad-warm, var(--pink)); color: #fff;
  display: grid; place-items: center;
  font-size: 13px; font-weight: 700;
}
.ob-test-who strong { display: block; font-size: 14px; color: var(--ink); }
.ob-test-who small { display: block; font-size: 12px; color: var(--ink-3); }

.ob-footer-trust {
  text-align: center;
  padding-top: 30px;
  border-top: 1px solid var(--line);
}
.ob-footer-trust p { font-size: 14px; color: var(--ink-2); margin-bottom: 6px; }
.ob-footer-trust .small { font-size: 12.5px; color: var(--ink-3); }
.ob-footer-trust a { color: var(--pink); font-weight: 600; }

/* Responsive */
@media (max-width: 1100px) {
  .ob-hero-grid { grid-template-columns: 1fr; gap: 40px; }
  .ob-steps     { grid-template-columns: repeat(2, 1fr); }
  .ob-tests     { grid-template-columns: 1fr; }
}
@media (max-width: 700px) {
  .ob-hero { padding: 120px 0 40px; }
  .ob-form { padding: 28px 22px; }
  .ob-form-head { flex-direction: column; align-items: flex-start; gap: 8px; }
  .ob-grid { grid-template-columns: 1fr; }
  .ob-section { padding: 20px 0 4px; }
  .ob-section-secure { padding-left: 4px; padding-right: 4px; }
  .ob-badges { grid-template-columns: 1fr; }
  .ob-steps  { grid-template-columns: 1fr; }
  .ob-video-title { font-size: 18px; }
  .ob-play { width: 64px; height: 64px; }
}

/* ===== LEAD-CAPTURE POPUP — 4-step branded form ===== */
.lead-popup {
  position: fixed; inset: 0; z-index: 9998;
  background: rgba(20,16,14,0.55);
  backdrop-filter: blur(10px) saturate(140%);
  -webkit-backdrop-filter: blur(10px) saturate(140%);
  display: flex; align-items: center; justify-content: center;
  padding: 20px 16px;
  opacity: 0; visibility: hidden;
  transition: opacity .35s ease, visibility .35s ease;
}
.lead-popup.open { opacity: 1; visibility: visible; }
.lead-card {
  position: relative;
  width: 100%; max-width: 420px;
  background: #fff;
  border-radius: 18px;
  padding: 24px 22px 20px;
  box-shadow: 0 40px 80px -20px rgba(20,16,14,0.45),
              0 0 0 1px rgba(20,16,14,0.05),
              0 0 50px -20px rgba(230,49,121,0.25);
  transform: scale(0.94) translateY(20px);
  transition: transform .5s cubic-bezier(.2,.7,.2,1);
  max-height: calc(100vh - 40px); overflow-y: auto;
  background-image: radial-gradient(60% 50% at 100% 0%, rgba(230,49,121,0.07), transparent 70%);
}
.lead-popup.open .lead-card { transform: scale(1) translateY(0); }
.lead-close {
  position: absolute; top: 10px; right: 10px;
  width: 30px; height: 30px; border-radius: 50%;
  background: var(--bg-2); color: var(--ink);
  border: none; cursor: pointer;
  font-size: 18px; line-height: 1;
  display: grid; place-items: center;
  font-family: inherit;
  transition: background .25s, color .25s, transform .3s;
}
.lead-close:hover { background: var(--ink); color: #fff; transform: rotate(90deg); }

.lead-head {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 16px;
}
.lead-avatar {
  position: relative;
  width: 40px; height: 40px; border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--pink);
  flex-shrink: 0;
  box-shadow: 0 6px 14px -6px rgba(230,49,121,0.5);
}
.lead-avatar img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 16%;
  display: block;
}
.lead-avatar .online-dot {
  position: absolute; bottom: -1px; right: -1px;
  width: 11px; height: 11px; border-radius: 50%;
  background: #22c55e;
  border: 2px solid #fff;
}
.lead-greet .lead-name {
  font-weight: 700; font-size: 13.5px; color: var(--ink);
  letter-spacing: -0.01em;
}
.lead-greet .lead-role {
  font-size: 10.5px; color: var(--ink-3);
  letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
}

.lead-progress { display: flex; gap: 4px; margin-bottom: 14px; }
.lp-dot {
  flex: 1; height: 3px; border-radius: 100px;
  background: var(--bg-2);
  transition: background .35s ease;
}
.lp-dot.active {
  background: linear-gradient(90deg, var(--pink), #ff7eb3);
}

.lead-step { display: none; }
.lead-step.active {
  display: block;
  animation: leadFade .4s ease;
}
@keyframes leadFade {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.lead-eyebrow {
  display: inline-block;
  font-size: 9.5px; letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--pink); font-weight: 700;
  margin-bottom: 8px;
}
.lead-step h3 {
  font-size: 17px; font-weight: 600; line-height: 1.25;
  letter-spacing: -0.02em;
  color: var(--ink); margin-bottom: 6px;
}
.lead-step > p {
  font-size: 13px; color: var(--ink-2);
  margin-bottom: 14px; line-height: 1.5;
}

.lead-options { display: flex; flex-direction: column; gap: 6px; }
.lead-options label {
  position: relative;
  display: flex; align-items: center; gap: 10px;
  padding: 11px 13px;
  background: #fff;
  border: 1.5px solid var(--line);
  border-radius: 11px;
  cursor: pointer;
  transition: border-color .2s, background .2s, transform .2s;
  font-size: 13px; color: var(--ink); font-weight: 500;
  user-select: none;
}
.lead-options label::before {
  content: "";
  width: 16px; height: 16px; border-radius: 50%;
  border: 2px solid var(--line-2);
  flex-shrink: 0;
  transition: border-color .2s, background .2s, box-shadow .2s;
}
.lead-options label:hover {
  border-color: var(--pink);
  background: var(--pink-tint);
  transform: translateX(2px);
}
.lead-options label.is-checked {
  border-color: var(--pink);
  background: var(--pink-soft);
}
.lead-options label.is-checked::before {
  border-color: var(--pink);
  background: var(--pink);
  box-shadow: inset 0 0 0 2.5px #fff;
}
.lead-options input { position: absolute; opacity: 0; pointer-events: none; }

.lead-fields { display: flex; flex-direction: column; gap: 10px; }
.lead-field {
  display: flex; flex-direction: column; gap: 4px;
}
.lead-field span {
  font-size: 10.5px; font-weight: 700; color: var(--ink-2);
  letter-spacing: 0.06em; text-transform: uppercase;
}
.lead-field input {
  padding: 10px 12px;
  border: 1.5px solid var(--line);
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  background: #fff;
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
}
.lead-field input:focus {
  outline: none;
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.lead-field input::placeholder { color: var(--ink-3); }

.lead-nav {
  display: flex; justify-content: space-between; align-items: center; gap: 8px;
  margin-top: 18px;
}
.lead-back, .lead-next, .lead-submit {
  font-family: inherit; cursor: pointer;
  border-radius: 100px;
  font-weight: 600;
  transition: background .2s, transform .2s, opacity .25s, box-shadow .25s;
}
.lead-back {
  padding: 9px 14px;
  background: transparent; color: var(--ink-3);
  border: none;
  font-size: 12px;
}
.lead-back:hover:not(:disabled) { color: var(--ink); }
.lead-back:disabled {
  opacity: 0; pointer-events: none;
}
.lead-next, .lead-submit {
  padding: 10px 20px;
  background: var(--pink); color: #fff;
  border: none;
  font-size: 13px;
  letter-spacing: 0.01em;
  box-shadow: 0 10px 22px -10px rgba(230,49,121,0.6);
  margin-left: auto;
  display: inline-flex; align-items: center; gap: 6px;
}
.lead-next:hover:not(:disabled),
.lead-submit:hover:not(:disabled) {
  background: var(--ink);
  transform: translateY(-2px);
  box-shadow: 0 16px 30px -10px rgba(20,16,14,0.4);
}
.lead-next:disabled,
.lead-submit:disabled {
  opacity: 0.35; cursor: not-allowed;
  box-shadow: none;
}

.lead-step[data-step="success"] { text-align: center; padding: 10px 0 4px; }
.lead-step[data-step="success"] h3 { font-size: 19px; }
.lead-step[data-step="success"] > p { max-width: 340px; margin-left: auto; margin-right: auto; }
.lead-success-icon {
  width: 56px; height: 56px; border-radius: 50%;
  background: linear-gradient(135deg, var(--pink), #ff7eb3);
  color: #fff;
  display: grid; place-items: center;
  font-size: 28px; font-weight: 700;
  margin: 0 auto 14px;
  box-shadow: 0 14px 30px -10px rgba(230,49,121,0.6);
  animation: leadPop .6s cubic-bezier(.2,1.4,.4,1);
}
@keyframes leadPop {
  0% { transform: scale(0); }
  60% { transform: scale(1.15); }
  100% { transform: scale(1); }
}
.lead-step[data-step="success"] .btn { margin-top: 8px; }

.lead-trust {
  margin-top: 16px; padding-top: 12px;
  border-top: 1px solid var(--line);
  display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
  font-size: 11px; color: var(--ink-3);
}
.lead-trust .lt-stars { color: var(--gold); letter-spacing: 2px; font-size: 11px; }
.lead-trust strong { color: var(--ink); font-weight: 700; }

@media (max-width: 560px) {
  .lead-popup { padding: 12px; }
  .lead-card { padding: 22px 18px 18px; border-radius: 16px; }
  .lead-step h3 { font-size: 16px; }
  .lead-options label { padding: 10px 12px; font-size: 13px; }
  .lead-success-icon { width: 50px; height: 50px; font-size: 24px; }
}

/* ===== STICKY MOBILE CTA BAR ===== */
.sticky-cta {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0; z-index: 90;
  padding: 12px 14px;
  padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
  background: rgba(255,255,255,0.96);
  backdrop-filter: blur(24px) saturate(140%);
  -webkit-backdrop-filter: blur(24px) saturate(140%);
  border-top: 1px solid var(--line);
  box-shadow: 0 -10px 36px -10px rgba(20,16,14,0.14);
  align-items: center; justify-content: space-between; gap: 12px;
}
.sticky-cta .lhs { min-width: 0; flex: 1; }
.sticky-cta .lhs strong {
  font-size: 14px; font-weight: 700; color: var(--ink); display: block;
  letter-spacing: -0.01em;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sticky-cta .lhs span {
  font-size: 11.5px; color: var(--ink-3);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  display: block;
}
.sticky-cta a {
  display: inline-flex; align-items: center; gap: 6px;
  background: var(--pink); color: #fff;
  padding: 13px 20px; border-radius: 100px;
  font-size: 13.5px; font-weight: 600;
  white-space: nowrap;
  box-shadow: 0 10px 24px -10px rgba(230,49,121,0.6);
  transition: transform .2s, background .2s;
}
.sticky-cta a:active { transform: scale(0.97); background: var(--ink); }
@media (max-width: 700px) {
  .sticky-cta { display: flex; }
  body { padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px)); }
  body.page-onboarding .sticky-cta { display: none; }
  body.page-onboarding { padding-bottom: 0; }
}
@media (max-width: 380px) {
  .sticky-cta { padding: 10px 12px; padding-bottom: calc(10px + env(safe-area-inset-bottom, 0px)); }
  .sticky-cta a { padding: 11px 16px; font-size: 13px; }
  .sticky-cta .lhs strong { font-size: 13px; }
  .sticky-cta .lhs span { font-size: 11px; }
}

/* ===== Reveal on scroll (progressive enhancement) ===== */
.js .reveal { opacity: 0; transform: translateY(28px); transition: opacity .8s var(--ease), transform .8s var(--ease); }
.js .reveal.in { opacity: 1; transform: translateY(0); }
.reveal-d2 { transition-delay: .08s; }
.reveal-d3 { transition-delay: .16s; }
.reveal-d4 { transition-delay: .24s; }

/* ===== Scrollbar ===== */
::-webkit-scrollbar { width: 10px; }
::-webkit-scrollbar-track { background: var(--bg); }
::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 100px; border: 3px solid var(--bg); }
::-webkit-scrollbar-thumb:hover { background: var(--pink); }

/* ===== NAV — Services Dropdown ===== */
.nav-dropdown { position: relative; }
.nav-dd-trigger {
  display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
}
.nav-dd-trigger .caret {
  width: 10px; height: 10px; transition: transform .25s; opacity: 0.55;
}
.nav-dropdown:hover .nav-dd-trigger,
.nav-dropdown:focus-within .nav-dd-trigger { color: var(--ink); background: rgba(20,16,14,0.05); }
.nav-dropdown:hover .nav-dd-trigger .caret,
.nav-dropdown:focus-within .nav-dd-trigger .caret { transform: rotate(180deg); opacity: 1; }
.nav-dd-menu {
  position: absolute; top: calc(100% + 14px); left: 50%;
  background: rgba(255,255,255,0.98);
  backdrop-filter: blur(20px) saturate(140%);
  -webkit-backdrop-filter: blur(20px) saturate(140%);
  border: 1px solid var(--line);
  border-radius: 22px;
  box-shadow: 0 30px 60px -20px rgba(20,16,14,0.18);
  padding: 10px;
  min-width: 340px;
  display: flex; flex-direction: column; gap: 2px;
  opacity: 0; visibility: hidden; pointer-events: none;
  transform: translateX(-50%) translateY(-8px);
  transition: opacity .25s var(--ease), transform .25s var(--ease), visibility .25s;
  z-index: 110;
}
.nav-dropdown:hover .nav-dd-menu,
.nav-dropdown:focus-within .nav-dd-menu {
  opacity: 1; visibility: visible; pointer-events: auto;
  transform: translateX(-50%) translateY(0);
}
.nav-dd-menu::before {
  content: ""; position: absolute; top: -6px; left: 50%; transform: translateX(-50%) rotate(45deg);
  width: 12px; height: 12px;
  background: rgba(255,255,255,0.98);
  border-left: 1px solid var(--line); border-top: 1px solid var(--line);
  border-radius: 3px 0 0 0;
}
.nav-dd-menu a {
  display: flex; align-items: center; gap: 14px;
  padding: 12px 14px; border-radius: 14px;
  font-size: 13.5px; font-weight: 600; color: var(--ink);
  background: transparent;
  transition: background .2s;
}
.nav-dd-menu a:hover { background: var(--bg-2); color: var(--ink); }
.nav-dd-menu a .ic {
  width: 36px; height: 36px; border-radius: 10px;
  background: var(--pink-soft); color: var(--pink);
  display: grid; place-items: center; flex-shrink: 0;
  font-weight: 700; font-size: 11.5px; letter-spacing: 0.04em;
}
.nav-dd-menu a:nth-child(2) .ic { background: var(--gold-soft); color: var(--gold); }
.nav-dd-menu a:nth-child(4) .ic { background: var(--gold-soft); color: var(--gold); }
.nav-dd-menu a .lab { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.nav-dd-menu a .lab small {
  font-weight: 400; font-size: 11.5px; color: var(--ink-3);
  letter-spacing: 0; line-height: 1.3;
}
.nav-dd-menu .nav-dd-foot {
  margin-top: 6px; padding: 12px 14px; border-top: 1px solid var(--line);
  font-size: 12px; color: var(--ink-3);
  display: flex; justify-content: space-between; align-items: center; gap: 10px;
}
.nav-dd-menu .nav-dd-foot a {
  display: inline-flex; padding: 0; background: transparent; color: var(--pink);
  font-weight: 600; font-size: 12.5px; gap: 4px;
}
.nav-dd-menu .nav-dd-foot a:hover { color: var(--ink); background: transparent; }

/* Mobile sub-menu */
.nm-group { display: flex; flex-direction: column; align-items: center; gap: 4px; width: 100%; }
.nm-group .nm-head {
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--ink-3);
  font-weight: 700; margin: 16px 0 4px;
}
.nm-group a { font-size: 22px; font-weight: 500; padding: 4px 0; }

/* ===== SERVICE PAGE STYLES ===== */
.svc-hero {
  padding: 150px 0 90px;
  position: relative; overflow: hidden;
}
.svc-hero::before {
  content: ""; position: absolute; top: -120px; right: -200px;
  width: 720px; height: 720px; border-radius: 50%;
  background: radial-gradient(circle, rgba(230,49,121,0.12), transparent 70%);
  z-index: 0;
}
.svc-hero::after {
  content: ""; position: absolute; bottom: -240px; left: -200px;
  width: 640px; height: 640px; border-radius: 50%;
  background: radial-gradient(circle, rgba(200,154,74,0.10), transparent 70%);
  z-index: 0;
}
.svc-crumbs {
  font-size: 11.5px; letter-spacing: 0.16em; text-transform: uppercase;
  color: var(--ink-3); font-weight: 700; margin-bottom: 26px;
  display: inline-flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.svc-crumbs a { color: var(--ink-3); transition: color .2s; }
.svc-crumbs a:hover { color: var(--pink); }
.svc-crumbs .sep { color: var(--ink-3); opacity: 0.5; }
.svc-crumbs .here { color: var(--ink); }
.svc-hero-grid {
  display: grid; grid-template-columns: 1.05fr 1fr; gap: 70px; align-items: center;
}
.svc-hero h1 { margin-bottom: 22px; font-size: clamp(2.4rem, 4.8vw, 4.2rem); }
.svc-hero h1 .serif { color: var(--ink); }
.svc-hero .lede {
  font-size: 18.5px; line-height: 1.6; max-width: 560px; margin-bottom: 32px;
}
.svc-hero .lede strong { color: var(--ink); font-weight: 600; }
.svc-hero-ctas { display: flex; gap: 12px; flex-wrap: wrap; }
.svc-hero-stats {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
  margin-top: 40px; padding-top: 32px; border-top: 1px solid var(--line);
  max-width: 540px;
}
.svc-hero-stats .stt strong {
  display: block; font-size: 26px; font-weight: 600; letter-spacing: -0.02em;
  color: var(--ink); line-height: 1; margin-bottom: 6px;
}
.svc-hero-stats .stt span { font-size: 12px; color: var(--ink-2); line-height: 1.3; }
.svc-hero-img {
  position: relative;
  aspect-ratio: 4/5;
  border-radius: var(--r-xl);
  overflow: hidden;
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.35),
              0 20px 40px -20px rgba(230,49,121,0.18);
}
.svc-hero-img img {
  width: 100%; height: 100%; object-fit: cover;
  filter: contrast(1.04) saturate(1.05);
}
.svc-hero-img::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(20,12,16,0.45) 100%);
}
.svc-hero-img .tag {
  position: absolute; top: 22px; left: 22px; z-index: 3;
  display: inline-flex; align-items: center; gap: 8px;
  padding: 7px 13px; border-radius: 100px;
  background: rgba(255,255,255,0.96); color: var(--ink);
  font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase;
  box-shadow: 0 6px 16px -4px rgba(0,0,0,0.2);
}
.svc-hero-img .tag::before {
  content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--pink);
}
.svc-hero-img .fly-card {
  position: absolute; left: -32px; bottom: 28px; z-index: 5;
  background: #fff; border: 1px solid var(--line); border-radius: 18px;
  padding: 14px 18px; display: flex; align-items: center; gap: 12px;
  box-shadow: 0 20px 40px -10px rgba(20,16,14,0.18);
  animation: floatA 5s ease-in-out infinite alternate;
}
.svc-hero-img .fly-card .icn {
  width: 38px; height: 38px; border-radius: 12px;
  background: var(--grad-warm); color: #fff;
  display: grid; place-items: center; font-weight: 700; font-size: 15px;
  box-shadow: 0 8px 16px -4px rgba(230,49,121,0.4);
}
.svc-hero-img .fly-card .lab { font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px; }
.svc-hero-img .fly-card .val { font-size: 16px; font-weight: 700; color: var(--ink); letter-spacing: -0.01em; }
@media (max-width: 1000px) {
  .svc-hero-grid { grid-template-columns: 1fr; gap: 50px; }
  .svc-hero-img { max-width: 480px; margin: 0 auto; }
  .svc-hero-img .fly-card { left: 8px; }
}
@media (max-width: 600px) { .svc-hero { padding: 120px 0 60px; } }

/* Outcomes */
.svc-outcomes { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.outcomes-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.outcome {
  background: #fff; border: 1px solid var(--line); border-radius: var(--r-lg);
  padding: 32px 26px;
  transition: transform .3s, box-shadow .3s, border-color .3s;
}
.outcome:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -25px rgba(20,16,14,0.18); border-color: var(--line-2); }
.outcome .ic {
  width: 48px; height: 48px; border-radius: 14px;
  background: var(--pink-soft); color: var(--pink);
  display: grid; place-items: center; margin-bottom: 22px;
  font-size: 18px; font-weight: 700;
}
.outcome:nth-child(2) .ic { background: var(--gold-soft); color: var(--gold); }
.outcome:nth-child(4) .ic { background: var(--gold-soft); color: var(--gold); }
.outcome h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; letter-spacing: -0.02em; }
.outcome p { font-size: 14.5px; line-height: 1.55; }
@media (max-width: 1000px) { .outcomes-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .outcomes-grid { grid-template-columns: 1fr; } }

/* Included */
.svc-included { padding: 110px 0; }
.included-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 70px; align-items: center; }
.included-grid.flip { grid-template-columns: 1fr 1fr; }
.included-grid.flip .included-img { order: 2; }
.included-img {
  aspect-ratio: 4/5;
  border-radius: var(--r-xl);
  overflow: hidden;
  box-shadow: 0 40px 80px -30px rgba(20,16,14,0.3);
  position: relative;
}
.included-img img { width: 100%; height: 100%; object-fit: cover; }
.included-img::after {
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(20,12,16,0.35) 100%);
}
.included-list h2 { margin-bottom: 18px; }
.included-list .eyebrow { margin-bottom: 18px; }
.included-list > p { margin-bottom: 22px; font-size: 16.5px; }
.included-list ul {
  list-style: none; display: flex; flex-direction: column; gap: 0;
  margin-top: 6px;
}
.included-list ul li {
  display: flex; align-items: flex-start; gap: 14px;
  font-size: 15.5px; color: var(--ink); font-weight: 600;
  padding: 18px 0; border-bottom: 1px solid var(--line);
}
.included-list ul li:last-child { border-bottom: 0; }
.included-list ul li .ck {
  width: 24px; height: 24px; border-radius: 50%;
  background: var(--pink); color: #fff;
  display: grid; place-items: center;
  flex-shrink: 0; font-size: 13px; font-weight: 700;
  margin-top: 1px;
}
.included-list ul li small {
  display: block; font-size: 13.5px; font-weight: 400;
  color: var(--ink-2); margin-top: 4px; line-height: 1.5;
}
@media (max-width: 1000px) {
  .included-grid, .included-grid.flip { grid-template-columns: 1fr; gap: 50px; }
  .included-grid.flip .included-img { order: 0; }
  .included-img { max-width: 480px; margin: 0 auto; }
}

/* Quote */
.svc-quote { padding: 110px 0; background: var(--bg-2); border-block: 1px solid var(--line); }
.quote-card {
  max-width: 920px; margin: 0 auto;
  background: var(--ink); color: #fff;
  border-radius: var(--r-xl);
  padding: 70px 60px;
  position: relative; overflow: hidden;
  background-image: radial-gradient(60% 50% at 100% 0%, rgba(230,49,121,0.3), transparent 60%);
}
.quote-card .stars { color: var(--gold-2); letter-spacing: 2px; font-size: 14px; margin-bottom: 22px; }
.quote-card .q {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: clamp(1.6rem, 3vw, 2.2rem); line-height: 1.3; color: #fff; letter-spacing: -0.01em;
  margin-bottom: 30px;
}
.quote-card .who {
  display: flex; align-items: center; gap: 14px;
  padding-top: 26px; border-top: 1px solid rgba(255,255,255,0.12);
}
.quote-card .av {
  width: 48px; height: 48px; border-radius: 50%;
  background: var(--grad-warm); color: #fff;
  display: grid; place-items: center; font-weight: 700; font-size: 14px; flex-shrink: 0;
}
.quote-card .nm { font-size: 14.5px; font-weight: 600; color: #fff; }
.quote-card .loc { font-size: 12px; color: rgba(255,255,255,0.55); }
@media (max-width: 700px) { .quote-card { padding: 50px 30px; } }

/* Service-page final CTA */
.svc-cta { padding: 110px 0 130px; }

/* Other services strip */
.svc-other { padding: 110px 0; }
.svc-other-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.svc-other a {
  display: flex; flex-direction: column; gap: 8px;
  padding: 22px 22px 24px;
  background: #fff; border: 1px solid var(--line); border-radius: 18px;
  color: var(--ink); transition: transform .3s, box-shadow .3s, border-color .3s;
}
.svc-other a:hover { transform: translateY(-4px); box-shadow: 0 24px 48px -20px rgba(20,16,14,0.18); border-color: var(--pink); }
.svc-other a .lab { font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--pink); font-weight: 700; }
.svc-other a strong { font-size: 16px; font-weight: 600; letter-spacing: -0.01em; }
.svc-other a span.go { font-size: 13px; color: var(--ink-3); display: inline-flex; align-items: center; gap: 4px; margin-top: 6px; }
@media (max-width: 1000px) { .svc-other-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 540px) { .svc-other-grid { grid-template-columns: 1fr; } }

/* ===== GLOBAL RESPONSIVE FIXES (mobile polish across every page) ===== */
@media (max-width: 700px) {
  /* Tighter container padding so content can breathe at narrow widths */
  .container { padding: 0 20px; }

  /* Service-page hero stats — 3 cols becomes 2 col grid */
  .svc-hero-stats { grid-template-columns: 1fr 1fr; gap: 12px; padding-top: 24px; margin-top: 28px; }
  .svc-hero-stats .stt:last-child { grid-column: 1 / -1; }
  .svc-hero-stats .stt strong { font-size: 22px; }

  /* Hide the floating fly-card on service hero — it overflows tight viewports */
  .svc-hero-img .fly-card {
    left: 50%; right: auto; bottom: 14px;
    transform: translateX(-50%);
    padding: 10px 14px;
    animation: none;
  }
  .svc-hero-img .fly-card .icn { width: 32px; height: 32px; }
  .svc-hero-img .fly-card .val { font-size: 14px; }
  .svc-hero-img .fly-card .lab { font-size: 10px; }

  /* Final CTA card — tighter padding so it doesn't feel cramped */
  .cta-section { padding: 80px 0 90px; }
  .cta-card { border-radius: 24px; }
  .cta-text { padding: 40px 24px; }
  .cta-text h2 { font-size: clamp(1.8rem, 6vw, 2.4rem); }
  .cta-text p { font-size: 15.5px; max-width: none; }
  .cta-text .ctas { flex-direction: column; align-items: stretch; gap: 10px; }
  .cta-text .ctas .btn { width: 100%; justify-content: center; }

  /* Generic section padding compression — keeps long pages from feeling endless */
  .pain-section,
  .promise-section,
  .services-section,
  .tests-section,
  .pricing-section,
  .ebooks-section,
  .boss-section,
  .authority,
  .faq-section,
  .postpurchase-section,
  .ob-steps-section,
  .ob-tests-section { padding: 70px 0; }

  /* Section heads */
  .section-head { margin-bottom: 36px; }
  .section-head h2 { font-size: clamp(1.6rem, 6vw, 2.2rem); }
  .section-head p { font-size: 15px; }

  /* Process section steps */
  .process-section { padding: 70px 0; }
  .process-grid, .process-grid.cols-3 { gap: 12px; }

  /* Pricing cards — kill the giant strike-through clutter */
  .price { padding: 28px 22px; }
  .price .amt { font-size: 38px; }

  /* About section — portrait sits above the text */
  .about-section { padding: 70px 0; }
  .about-portrait { max-width: 360px; }

  /* Authority mosaic */
  .authority { padding: 70px 0; }
  .auth-strip { padding: 12px 14px; }
  .auth-pill { font-size: 11px; padding: 5px 10px; }

  /* Mentorship/boss cards readable on mobile */
  .boss-card { padding: 30px 22px; }
  .boss-card h3 { font-size: 22px; }

  /* Final/quote CTA on service pages */
  .svc-cta { padding: 80px 0 90px; }

  /* Quote card on service pages */
  .quote-card .q { font-size: 18px; line-height: 1.35; }

  /* Pain-foot copy on homepage */
  .pain-foot { font-size: 14.5px; }

  /* Tap-friendly buttons (44px+ touch target) */
  .btn { min-height: 44px; }
}

@media (max-width: 480px) {
  .container { padding: 0 16px; }

  /* Title sizes */
  h1 { font-size: clamp(2rem, 9vw, 2.6rem); }
  h2 { font-size: clamp(1.5rem, 6vw, 2rem); }

  /* Tighter footer padding */
  footer { padding: 50px 0 30px; }

  /* Hero stage portraits cap at smaller width */
  .hero-stage, .ob-hero-grid .hero-portrait { max-width: 360px; margin-inline: auto; }

  /* Service-page hero stats — single column at the tiniest sizes */
  .svc-hero-stats { grid-template-columns: 1fr; }
  .svc-hero-stats .stt:last-child { grid-column: 1; }

  /* Boss-card tier rows wrap nicely */
  .tier-row { grid-template-columns: 1fr 1fr; gap: 10px; }

  /* Onboarding form — even tighter padding */
  .ob-form { padding: 22px 18px; }
  .ob-section-secure { padding-left: 0; padding-right: 0; }
  .ob-dob { grid-template-columns: 1fr; gap: 8px; }

  /* Funding card — tighter */
  .fund-card { padding: 24px 18px; }
  .fund-step h3 { font-size: 17px; }

  /* Contact form */
  .ct-card { padding: 22px 18px; }

  /* Footer columns */
  .footer-top { gap: 28px; }
  .footer-col h5 { font-size: 12px; margin-bottom: 12px; }
}

/* ===============================================================
   INDEX HOMEPAGE — MOBILE POLISH (≤ 760px)
   Centers headings, sub copy, and CTAs on every stacked section.
   Bumps headline sizes so they hit properly on phones.
   Card interiors are NOT touched — only the outer wrappers.
   Desktop styles are untouched (this block fires below 760px only).
   =============================================================== */
@media (max-width: 760px) {

  /* ── HERO ──────────────────────────────────────────────── */
  .hero { padding: 110px 0 60px; }
  .hero-grid { gap: 44px; }
  .hero-text { text-align: center; }
  .hero h1 {
    font-size: clamp(2.7rem, 11vw, 3.8rem);
    line-height: 1.02;
    letter-spacing: -0.04em;
    margin-bottom: 22px;
  }
  .hero .lede {
    font-size: 17px; line-height: 1.55;
    margin-left: auto; margin-right: auto;
    margin-bottom: 30px;
    max-width: 540px;
  }
  .hero-ctas {
    justify-content: center;
    gap: 10px;
  }
  .hero-ctas .btn { flex: 1 1 240px; max-width: 360px; justify-content: center; }
  .hero-meta { justify-content: center; }
  .hero-meta .avs-text { text-align: left; }

  /* ── SECTION HEADS — already centered, just sized bigger ─ */
  .section-head { margin: 0 auto 40px; padding: 0 4px; }
  .section-head h2 {
    font-size: clamp(2.35rem, 8.6vw, 3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
  }
  .section-head p { font-size: 16px; line-height: 1.6; }

  /* Pain section foot — center the inline CTA */
  .pain-foot { text-align: center; font-size: 14.5px; line-height: 1.6; }

  /* ── ABOUT ─────────────────────────────────────────────── */
  .about-grid { gap: 40px; }
  .about-text { text-align: center; }
  .about-text h2 {
    font-size: clamp(2.35rem, 8.6vw, 3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
  }
  .about-text .eyebrow { justify-content: center; }
  .about-text p { max-width: 540px; margin-left: auto; margin-right: auto; }
  /* Bullets remain left-aligned but the whole list centers as a block */
  .about-bullets {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    margin: 22px auto;
    text-align: left;
  }
  .about-text .signature { text-align: center; }
  .about-cta {
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  .about-cta .btn { flex: 1 1 240px; max-width: 360px; justify-content: center; }

  /* ── SCORES head inside Results section ────────────────── */
  .scores-head { text-align: center; }
  .scores-head h3 {
    font-size: clamp(2rem, 7.6vw, 2.6rem);
    line-height: 1.08;
    letter-spacing: -0.03em;
    margin-bottom: 10px;
  }
  .scores-sub { font-size: 15px; max-width: 480px; margin: 0 auto; }

  /* ── AUTHORITY (luxury credit) ─────────────────────────── */
  .auth-text { text-align: center; }
  .auth-text .eyebrow { justify-content: center; }
  .auth-text h2 {
    font-size: clamp(2.35rem, 8.6vw, 3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
  }
  .auth-text .lede {
    max-width: 540px;
    margin-left: auto; margin-right: auto;
  }
  .auth-meta { max-width: 480px; margin: 28px auto; }
  /* Trust ticks: keep left-aligned ticks but center the block */
  .auth-trust {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    text-align: left;
    margin: 0 auto;
  }
  .auth-ctas {
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  .auth-ctas .btn { flex: 1 1 240px; max-width: 360px; justify-content: center; }

  /* ── FAQ side column ───────────────────────────────────── */
  .faq-side { text-align: center; }
  .faq-side .eyebrow { justify-content: center; }
  .faq-side h2 {
    font-size: clamp(2.35rem, 8.6vw, 3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
  }
  .faq-side p { max-width: 520px; margin-left: auto; margin-right: auto; }
  .faq-side .ctas {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 24px;
  }
  .faq-side .ctas .btn { flex: 1 1 240px; max-width: 360px; justify-content: center; }

  /* ── FINAL CTA card (already collapses cleanly; just upsize) */
  .cta-text h2 {
    font-size: clamp(2.35rem, 8.6vw, 3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
  }
  .cta-text p { margin-left: auto; margin-right: auto; }
  .cta-text .stamp { justify-content: center; }

  /* Pricing copy below the cards */
  .price-meta { padding: 0 8px; font-size: 13.5px; }

  /* ── BUTTONS — generous tap target everywhere ──────────── */
  .btn {
    padding: 16px 24px;
    font-size: 15px;
    min-height: 50px;
  }
}

/* Tiniest phones — single-column CTAs so labels never wrap */
@media (max-width: 420px) {
  .hero-ctas .btn,
  .about-cta .btn,
  .auth-ctas .btn,
  .faq-side .ctas .btn {
    flex: 1 1 100%;
    max-width: 100%;
  }
}

</style>
</head>
<body class="@yield('bodyClass')">

<!-- ============ NAV ============ -->
<nav class="nav" id="nav">
  <a href="{{ url('/') }}" class="logo" aria-label="Victorious Opportunities — home">
    <img src="{{ asset('images/companylogo.png') }}" alt="Victorious Opportunities" width="180" height="36" decoding="async" />
  </a>
  <div class="nav-links">
    <a href="{{ url('/services/diy-business-funding') }}">Funding</a>
    <a href="{{ url('/services/credit-consultations') }}">Consultations</a>
    <a href="{{ url('/') }}#pricing">Pricing</a>
    <a href="{{ url('/') }}#ebooks">Ebooks</a>
    <a href="{{ route('mentorship') }}">Mentorship</a>
    <a href="{{ route('contact.show') }}">Contact</a>
  </div>
  <a href="{{ route('contact.show') }}" class="nav-cta">Free Strategy Call <span class="arr">→</span></a>
  <button class="menu-btn" id="menuBtn" aria-label="Menu"><span></span></button>
</nav>
<div class="nav-mobile" id="navMobile">
  <a href="{{ url('/services/diy-business-funding') }}">DIY Business + Funding</a>
  <a href="{{ url('/services/credit-consultations') }}">Credit Consultations</a>
  <a href="{{ url('/') }}#pricing">Pricing</a>
  <a href="{{ url('/') }}#ebooks">Ebooks</a>
  <a href="{{ route('mentorship') }}">Mentorship</a>
  <a href="{{ route('contact.show') }}">Contact</a>
  <a href="{{ route('contact.show') }}" class="cta">Book Free Call →</a>
</div>

@yield('content')

<!-- ============ FOOTER ============ -->
<footer>
  <div class="container">
    <div class="footer-top">
      <div class="footer-brand">
        <a href="{{ url('/') }}" class="logo" aria-label="Victorious Opportunities — home">
          <img src="{{ asset('images/companylogo.png') }}" alt="Victorious Opportunities" width="180" height="48" loading="lazy" decoding="async" />
        </a>
        <p>Helping you fix your credit, own your home, and build wealth that outlives you. Texas + nationwide.</p>
      </div>
      <div class="footer-col">
        <h5>Services</h5>
        <a href="{{ url('/services/credit-repair') }}">Credit Repair</a>
        <a href="{{ url('/services/diy-business-funding') }}">DIY Business + Funding</a>
        <a href="{{ url('/services/credit-consultations') }}">Credit Consultations</a>
      </div>
      <div class="footer-col">
        <h5>Learn</h5>
        <a href="{{ url('/') }}#ebooks">E-Books</a>
        <a href="{{ route('mentorship') }}">Mentorship</a>
        <a href="{{ url('/') }}#faq">FAQ</a>
      </div>
      <div class="footer-col">
        <h5>Connect</h5>
        <a href="mailto:info@victoriousopportunities.com">Email</a>
        <a href="https://instagram.com/iamvictoria.love" target="_blank">Instagram · @iamvictoria.love</a>
        <a href="https://tiktok.com/@iam.victorialove" target="_blank">TikTok · @iam.victorialove</a>
      </div>
      <div class="footer-col">
        <h5>Legal</h5>
        <a href="{{ route('legal.privacy-policy') }}">Privacy Policy</a>
        <a href="{{ route('legal.terms-of-service') }}">Terms of Service</a>
        <a href="{{ route('legal.disclaimer') }}">Disclaimer</a>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© <span id="yr"></span> Victorious Opportunities. All Rights Reserved.</div>
      <div class="socials">
        <a href="https://instagram.com/iamvictoria.love" target="_blank" aria-label="Instagram"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.8" fill="currentColor"/></svg></a>
        <a href="https://tiktok.com/@iam.victorialove" target="_blank" aria-label="TikTok"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19.6 6.3a4.7 4.7 0 0 1-3.7-4.3h-3.3v13.5a2.7 2.7 0 1 1-2.7-2.7c.3 0 .6 0 .9.1V9.6a6 6 0 1 0 5.1 5.9V8.4c1.1.7 2.3 1.1 3.7 1.1V6.3z"/></svg></a>
        <a href="#" target="_blank" aria-label="Facebook"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9V15h-2.5v-3h2.5v-2.3c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5H15c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 3h-2.3v6.9A10 10 0 0 0 22 12z"/></svg></a>
      </div>
    </div>
  </div>
</footer>

<!-- Sticky mobile CTA bar -->
<div class="sticky-cta">
  <div class="lhs">
    <strong>Choose your plan</strong>
    <span>Simple monthly pricing · real results</span>
  </div>
  <a href="{{ url('/') }}#pricing">View pricing →</a>
</div>

<script>
/* Enable JS-only reveal animations (progressive enhancement) */
document.documentElement.classList.add('js');

/* Mobile menu */
const mb = document.getElementById('menuBtn');
const nm = document.getElementById('navMobile');
mb.addEventListener('click', () => {
  mb.classList.toggle('open'); nm.classList.toggle('open');
  document.body.style.overflow = nm.classList.contains('open') ? 'hidden' : '';
});
nm.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
  mb.classList.remove('open'); nm.classList.remove('open'); document.body.style.overflow = '';
}));

/* Reveal on scroll */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); });
}, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* FAQ accordion */
document.querySelectorAll('.faq-item').forEach(item => {
  const q = item.querySelector('.faq-q');
  q.addEventListener('click', () => {
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
  });
});

/* Nav scrolled state */
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 30);
});

/* Year */
document.getElementById('yr').textContent = new Date().getFullYear();

/* Multi-step lead-capture popup */
(function () {
  const popup = document.getElementById('leadPopup');
  if (!popup) return;

  const STORAGE_KEY = 'vl_lead_popup_dismissed';
  if (sessionStorage.getItem(STORAGE_KEY)) return;

  const form      = document.getElementById('leadForm');
  const navEl     = document.getElementById('leadNav');
  const closeBtn  = document.getElementById('leadClose');
  const backBtn   = document.getElementById('leadBack');
  const nextBtn   = document.getElementById('leadNext');
  const submitBtn = document.getElementById('leadSubmit');
  const doneBtn   = document.getElementById('leadDoneBtn');
  const steps     = popup.querySelectorAll('.lead-step');
  const dots      = popup.querySelectorAll('.lp-dot');
  const totalSteps = 4;
  let currentStep = 1;

  const openPopup = () => {
    popup.classList.add('open');
    document.body.style.overflow = 'hidden';
  };

  const closePopup = () => {
    popup.classList.remove('open');
    document.body.style.overflow = '';
    sessionStorage.setItem(STORAGE_KEY, 'yes');
  };

  const validate = () => {
    const target = popup.querySelector(`.lead-step[data-step="${currentStep}"]`);
    if (!target) return false;
    const radios = target.querySelectorAll('input[type="radio"]');
    const inputs = target.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]');
    let ok = true;
    if (radios.length)  ok = Array.from(radios).some(r => r.checked);
    if (inputs.length)  ok = Array.from(inputs).every(i => i.value.trim() !== '');
    nextBtn.disabled = !ok;
    submitBtn.disabled = !ok;
    return ok;
  };

  const showStep = (n) => {
    steps.forEach(s => s.classList.remove('active'));
    const target = popup.querySelector(`.lead-step[data-step="${n}"]`);
    if (target) target.classList.add('active');
    dots.forEach((d, i) => d.classList.toggle('active', i < n));
    backBtn.disabled = (n === 1);
    if (n === totalSteps) {
      nextBtn.hidden = true;
      submitBtn.hidden = false;
    } else {
      nextBtn.hidden = false;
      submitBtn.hidden = true;
    }
    currentStep = n;
    validate();
  };

  /* Sync the .is-checked class on selected option labels */
  form.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      form.querySelectorAll(`input[name="${radio.name}"]`).forEach(r => {
        r.closest('label').classList.toggle('is-checked', r.checked);
      });
      validate();
      /* Auto-advance on choice for steps 1-3 */
      if (currentStep < totalSteps) {
        setTimeout(() => showStep(currentStep + 1), 320);
      }
    });
  });

  form.addEventListener('input', validate);

  nextBtn.addEventListener('click', () => {
    if (validate() && currentStep < totalSteps) showStep(currentStep + 1);
  });
  backBtn.addEventListener('click', () => {
    if (currentStep > 1) showStep(currentStep - 1);
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!validate()) return;

    /* Persist to /lead — fails silent so we never block the user-facing success state */
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const data = new FormData(form);
    data.append('source', 'popup');
    fetch('{{ route("lead.submit") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: data,
    }).catch(() => {});

    /* Show success state */
    navEl.style.display = 'none';
    steps.forEach(s => s.classList.remove('active'));
    popup.querySelector('.lead-step[data-step="success"]').classList.add('active');
    dots.forEach(d => d.classList.add('active'));
    sessionStorage.setItem(STORAGE_KEY, 'yes');
  });

  closeBtn.addEventListener('click', closePopup);
  if (doneBtn) doneBtn.addEventListener('click', closePopup);
  popup.addEventListener('click', (e) => { if (e.target === popup) closePopup(); });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && popup.classList.contains('open')) closePopup();
  });

  /* Open after 5 seconds */
  setTimeout(openPopup, 5000);
})();

/* Testimonial lightbox */
(function () {
  const lb       = document.getElementById('lightbox');
  if (!lb) return;
  const lbImg    = document.getElementById('lightboxImg');
  const lbClose  = document.getElementById('lightboxClose');
  const lbPrev   = document.getElementById('lightboxPrev');
  const lbNext   = document.getElementById('lightboxNext');
  const cards    = Array.from(document.querySelectorAll('.test-card, .score-card'));
  let current    = 0;

  const open = (i) => {
    current = (i + cards.length) % cards.length;
    lbImg.src = cards[current].dataset.src;
    lbImg.alt = cards[current].getAttribute('aria-label') || '';
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
  const close = () => {
    lb.classList.remove('open');
    document.body.style.overflow = '';
    setTimeout(() => { lbImg.src = ''; }, 250);
  };

  cards.forEach((c, i) => c.addEventListener('click', () => open(i)));
  lbClose.addEventListener('click', close);
  lbPrev.addEventListener('click', (e) => { e.stopPropagation(); open(current - 1); });
  lbNext.addEventListener('click', (e) => { e.stopPropagation(); open(current + 1); });
  lb.addEventListener('click', (e) => { if (e.target === lb) close(); });
  document.addEventListener('keydown', (e) => {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape')      close();
    if (e.key === 'ArrowLeft')   open(current - 1);
    if (e.key === 'ArrowRight')  open(current + 1);
  });
})();
</script>
</body>
</html>
