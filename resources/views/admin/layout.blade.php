<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') · Victoria Love</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
:root {
  --ink: #15110f; --ink-2: #5a544f; --ink-3: #968f86;
  --bg:  #faf7f2; --bg-2: #f3ede4; --bg-3: #ffffff;
  --line:   rgba(20,16,14,0.08);
  --line-2: rgba(20,16,14,0.16);
  --pink:   #e63179; --pink-2: #ff7eb3; --pink-soft: #fdeaf2; --pink-tint: #fff5f9;
  --gold:   #c89a4a; --gold-soft: #faf0db;
  --green:  #22c55e; --red: #ef4444; --amber: #f59e0b;
  --r-md: 14px; --r-lg: 18px;
}
*, *::before, *::after { box-sizing: border-box; }
html, body {
  margin: 0; padding: 0;
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
}
body {
  font-family: 'Manrope', sans-serif;
  background: var(--bg);
  color: var(--ink);
  font-size: 14px; line-height: 1.5;
  -webkit-font-smoothing: antialiased;
}
img, svg, video { max-width: 100%; height: auto; }
.serif { font-family: 'Instrument Serif', serif; font-style: italic; }
a { color: inherit; text-decoration: none; }

/* ============ ADMIN SHELL ============ */
.admin-shell {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
  width: 100%; max-width: 100%;
  overflow-x: hidden;
}
.admin-sidebar {
  background: var(--ink);
  color: rgba(255,255,255,0.85);
  padding: 22px 18px;
  position: sticky; top: 0;
  height: 100vh; overflow-y: auto;
  display: flex; flex-direction: column;
}
.admin-brand {
  display: flex; align-items: center; gap: 10px;
  padding-bottom: 22px; margin-bottom: 18px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.admin-brand-mark {
  width: 38px; height: 38px; border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--pink);
  flex-shrink: 0;
  box-shadow: 0 8px 16px -6px rgba(230,49,121,0.5);
}
.admin-brand-mark img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 18%;
  display: block;
}
.admin-brand-text strong { display: block; font-size: 14px; color: #fff; font-weight: 700; letter-spacing: -0.01em; }
.admin-brand-text small  { display: block; font-size: 11px; color: rgba(255,255,255,0.45); letter-spacing: 0.08em; text-transform: uppercase; }

.admin-nav { display: flex; flex-direction: column; gap: 2px; }
.admin-nav a {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 13.5px; font-weight: 500;
  color: rgba(255,255,255,0.7);
  transition: background .2s, color .2s;
}
.admin-nav a:hover { background: rgba(255,255,255,0.06); color: #fff; }
.admin-nav a.active { background: var(--pink); color: #fff; box-shadow: 0 6px 14px -6px rgba(230,49,121,0.55); }
.admin-nav .ic { width: 18px; text-align: center; opacity: 0.85; }

.admin-foot {
  margin-top: auto;
  padding-top: 18px;
  border-top: 1px solid rgba(255,255,255,0.08);
  font-size: 12px;
}
.admin-foot .user { color: #fff; font-weight: 600; margin-bottom: 4px; }
.admin-foot .role { color: rgba(255,255,255,0.5); font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 14px; }
.admin-logout {
  width: 100%;
  background: rgba(255,255,255,0.06);
  color: rgba(255,255,255,0.85);
  border: 1px solid rgba(255,255,255,0.1);
  padding: 9px 12px;
  border-radius: 100px;
  font-size: 12.5px; font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background .2s, color .2s;
}
.admin-logout:hover { background: var(--pink); color: #fff; border-color: var(--pink); }

.admin-main { padding: 0 38px 50px; min-width: 0; width: 100%; max-width: 100%; overflow-x: hidden; }

.admin-topbar {
  position: sticky; top: 0; z-index: 30;
  background: var(--bg);
  padding: 18px 0 14px;
  margin-bottom: 16px;
  border-bottom: 1px solid var(--line);
}
.admin-search {
  display: flex; align-items: center; gap: 10px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 100px;
  padding: 4px 6px 4px 18px;
  box-shadow: 0 6px 14px -10px rgba(20,16,14,0.18);
  transition: border-color .2s, box-shadow .2s;
  max-width: 720px;
}
.admin-search:focus-within {
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.10);
}
.admin-search-ico { font-size: 16px; color: var(--ink-3); flex-shrink: 0; }
.admin-search input {
  flex: 1; min-width: 0;
  border: none; outline: none; background: transparent;
  font-family: inherit; font-size: 13.5px; color: var(--ink);
  padding: 10px 4px;
}
.admin-search input::placeholder { color: var(--ink-3); }
.admin-search .adm-btn { padding: 9px 18px; font-size: 12.5px; flex-shrink: 0; }
.admin-main > .admin-header { padding-top: 14px; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; gap: 16px; flex-wrap: wrap; }
.admin-header h1 { font-size: 26px; font-weight: 600; letter-spacing: -0.02em; margin: 0 0 4px; }
.admin-header .sub  { color: var(--ink-3); font-size: 13.5px; }

.flash { padding: 12px 16px; border-radius: 12px; margin-bottom: 18px; font-size: 13.5px; }
.flash.success { background: #f0fdf4; border: 1px solid #c9eccd; color: #157a3d; }
.flash.error   { background: #fff3f3; border: 1px solid #fbd6d6; color: #8b1414; }

/* Dashboard hero banner */
.adm-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 28px;
  align-items: center;
  padding: 28px 32px;
  margin-bottom: 28px;
  border-radius: 22px;
  background:
    radial-gradient(60% 80% at 0% 0%, rgba(230,49,121,0.18), transparent 65%),
    radial-gradient(50% 70% at 100% 100%, rgba(200,154,74,0.12), transparent 65%),
    linear-gradient(135deg, #1f0e18 0%, #2c1622 50%, #14080d 100%);
  color: #fff;
  box-shadow: 0 30px 60px -25px rgba(20,16,14,0.45);
  position: relative;
  overflow: hidden;
}
.adm-hero::after {
  content: ""; position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, rgba(255,255,255,0.015) 0 2px, transparent 2px 14px);
  pointer-events: none;
}
.adm-hero-portrait {
  position: relative;
  width: 88px; height: 88px; border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--pink);
  box-shadow: 0 14px 28px -8px rgba(230,49,121,0.55);
  flex-shrink: 0;
  z-index: 1;
}
.adm-hero-portrait img {
  width: 100%; height: 100%; object-fit: cover; object-position: center 18%;
}
.adm-hero-online {
  position: absolute; bottom: 2px; right: 2px;
  width: 16px; height: 16px; border-radius: 50%;
  background: #22c55e;
  border: 3px solid #1f0e18;
  z-index: 2;
}
.adm-hero-text { z-index: 1; }
.adm-hero-eye {
  display: inline-block;
  font-size: 10.5px; letter-spacing: 0.18em; text-transform: uppercase;
  font-weight: 700; color: var(--pink-2);
  margin-bottom: 8px;
}
.adm-hero-text h1 {
  font-size: 30px; font-weight: 600; margin: 0 0 4px;
  letter-spacing: -0.025em;
  color: #fff;
}
.adm-hero-text h1 .serif {
  font-family: 'Instrument Serif', serif; font-style: italic;
  background: linear-gradient(135deg, var(--pink-2), #ffd9a0);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.adm-hero-text p {
  font-size: 13.5px;
  margin: 0; line-height: 1.5;
  color: rgba(255,255,255,0.75);
}
.adm-hero-text p.sub { margin-top: 4px; color: rgba(255,255,255,0.5); font-size: 12.5px; }
.adm-hero-summary {
  display: flex; gap: 10px;
  z-index: 1;
}
.adm-hero-stat {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 14px 18px;
  text-align: center;
  min-width: 112px;
}
.adm-hero-stat strong {
  display: block; font-size: 26px; font-weight: 700;
  color: #fff; letter-spacing: -0.02em; line-height: 1;
  margin-bottom: 4px;
}
.adm-hero-stat span { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; }
.adm-hero-stat.alt strong { color: var(--pink-2); }

@media (max-width: 900px) {
  .adm-hero { grid-template-columns: 1fr; gap: 18px; padding: 22px 22px; }
  .adm-hero-portrait { width: 72px; height: 72px; }
  .adm-hero-summary { justify-content: stretch; }
  .adm-hero-stat { flex: 1; min-width: 0; }
}

.adm-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 30px; }
.adm-stat {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 18px 20px;
  box-shadow: 0 8px 20px -16px rgba(20,16,14,0.18);
}
.adm-stat .lab { font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--ink-3); font-weight: 700; margin-bottom: 10px; }
.adm-stat .val { font-size: 30px; font-weight: 700; color: var(--ink); letter-spacing: -0.02em; }
.adm-stat .delta { font-size: 11.5px; color: var(--ink-2); margin-top: 4px; }
.adm-stat .delta strong { color: var(--pink); }
.adm-stat.link { display: block; transition: transform .2s, box-shadow .2s, border-color .2s; cursor: pointer; }
.adm-stat.link:hover { transform: translateY(-3px); border-color: var(--pink); box-shadow: 0 20px 36px -16px rgba(230,49,121,0.25); }
.adm-stat.link:hover .val { color: var(--pink); }

.adm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.adm-grid-3 { display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 16px; }

.adm-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 22px 24px;
}
.adm-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
.adm-card-head h2 { font-size: 15px; font-weight: 700; margin: 0; letter-spacing: -0.005em; }
.adm-card-head a  { font-size: 12px; color: var(--pink); font-weight: 600; }

.adm-mini-list { display: flex; flex-direction: column; gap: 2px; }
.adm-mini-list .row { display: grid; grid-template-columns: 1fr auto; gap: 8px; padding: 8px 0; border-bottom: 1px solid var(--line); }
.adm-mini-list .row:last-child { border-bottom: 0; }
.adm-mini-list .row .nm { font-weight: 600; font-size: 13px; color: var(--ink); }
.adm-mini-list .row .em { font-size: 11.5px; color: var(--ink-3); }
.adm-mini-list .row time { font-size: 11px; color: var(--ink-3); text-align: right; white-space: nowrap; }

.adm-toolbar {
  display: flex; gap: 10px; flex-wrap: wrap;
  margin-bottom: 18px;
  align-items: center;
  max-width: 100%;
}
.adm-toolbar form { display: flex; gap: 8px; flex: 1; flex-wrap: wrap; max-width: 100%; }
.adm-input, .adm-select {
  font-family: inherit;
  font-size: 13px;
  padding: 9px 12px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: #fff;
  max-width: 100%;
}
.adm-input { flex: 1 1 200px; min-width: 0; }
.adm-btn {
  font-family: inherit;
  font-size: 13px; font-weight: 600;
  padding: 9px 16px;
  border-radius: 100px;
  border: none;
  background: var(--ink);
  color: #fff;
  cursor: pointer;
  transition: background .2s, transform .2s;
}
.adm-btn:hover { background: var(--pink); transform: translateY(-1px); }
.adm-btn.ghost { background: transparent; color: var(--ink); border: 1px solid var(--line-2); }

table.adm-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  overflow: hidden;
}
table.adm-table th,
table.adm-table td {
  padding: 12px 14px;
  font-size: 13px;
  text-align: left;
  border-bottom: 1px solid var(--line);
}
table.adm-table th {
  background: var(--bg-2);
  font-weight: 700;
  font-size: 11px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--ink-3);
}
table.adm-table tr:last-child td { border-bottom: 0; }
table.adm-table tr:hover { background: var(--bg); }
table.adm-table td .nm { font-weight: 600; color: var(--ink); display: block; }
table.adm-table td .sub { font-size: 11.5px; color: var(--ink-3); }
table.adm-table .actions { white-space: nowrap; }

.badge {
  display: inline-block;
  font-size: 10.5px; font-weight: 700;
  letter-spacing: 0.1em; text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 100px;
}
.badge.new       { background: var(--pink-soft); color: var(--pink); }
.badge.replied,
.badge.contacted { background: #e0f2fe; color: #075985; }
.badge.in_progress { background: #fef3c7; color: #92400e; }
.badge.active,
.badge.converted { background: #f0fdf4; color: #157a3d; }
.badge.archived  { background: var(--bg-2); color: var(--ink-3); }
.badge.failed    { background: #fee2e2; color: #991b1b; }
.badge.sent      { background: #f0fdf4; color: #157a3d; }
.badge.pending   { background: #fef3c7; color: #92400e; }

.empty {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border: 1px dashed var(--line-2);
  border-radius: var(--r-md);
  color: var(--ink-3);
  font-size: 14px;
}
.empty strong { color: var(--ink); display: block; margin-bottom: 6px; }

.detail-grid { display: grid; grid-template-columns: 240px 1fr; gap: 12px 26px; }
.detail-grid .lab { font-size: 11.5px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; color: var(--ink-3); padding: 10px 0; }
.detail-grid .val { font-size: 14px; color: var(--ink); padding: 10px 0; word-break: break-word; }
.detail-grid .val.mono { font-family: 'SF Mono', Menlo, monospace; font-size: 13px; }

.pager { display: flex; justify-content: space-between; align-items: center; margin-top: 18px; font-size: 13px; color: var(--ink-2); }
.pager .links a, .pager .links span {
  display: inline-block; padding: 8px 12px; border-radius: 8px; margin-left: 4px; font-size: 12.5px; font-weight: 600;
}
.pager .links a { background: #fff; border: 1px solid var(--line); color: var(--ink); }
.pager .links a:hover { background: var(--pink); color: #fff; border-color: var(--pink); }
.pager .links .current { background: var(--ink); color: #fff; }
.pager .links .disabled { color: var(--ink-3); }

.status-form { display: inline-flex; align-items: center; gap: 6px; }
.status-form select {
  font-size: 11px; padding: 5px 8px; border-radius: 8px;
  border: 1px solid var(--line); background: #fff;
  font-family: inherit;
}

/* ============ LOGIN PAGE ============ */
.login-shell {
  min-height: 100vh;
  display: grid; place-items: center;
  background:
    radial-gradient(50% 40% at 0% 0%, rgba(230,49,121,0.12), transparent 65%),
    radial-gradient(45% 35% at 100% 100%, rgba(200,154,74,0.10), transparent 65%),
    var(--bg);
  padding: 24px;
}
.login-card {
  width: 100%; max-width: 420px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 38px 36px 30px;
  box-shadow: 0 40px 80px -20px rgba(20,16,14,0.25), 0 0 60px -20px rgba(230,49,121,0.25);
}
.login-card .mark {
  width: 64px; height: 64px; border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--pink);
  margin-bottom: 18px;
  box-shadow: 0 14px 28px -8px rgba(230,49,121,0.5);
}
.login-card .mark img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 18%;
  display: block;
}
.login-card h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; letter-spacing: -0.015em; }
.login-card .sub { color: var(--ink-3); font-size: 13.5px; margin-bottom: 24px; }

.login-card form { display: flex; flex-direction: column; gap: 14px; }
.login-card label { display: flex; flex-direction: column; gap: 6px; }
.login-card label span {
  font-size: 11px; font-weight: 700; color: var(--ink-2); letter-spacing: 0.08em; text-transform: uppercase;
}
.login-card label input {
  padding: 13px 14px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  font-size: 14.5px; font-family: inherit;
  background: #fff; color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
}
.login-card label input:focus {
  outline: none; border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.12);
}
.login-card .remember {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: var(--ink-2);
  cursor: pointer;
}
.login-card button {
  margin-top: 6px;
  background: var(--pink); color: #fff;
  border: none; border-radius: 100px;
  padding: 14px 20px;
  font-family: inherit; font-size: 14.5px; font-weight: 600;
  cursor: pointer;
  box-shadow: 0 16px 30px -12px rgba(230,49,121,0.55);
  transition: background .2s, transform .2s;
}
.login-card button:hover { background: var(--ink); transform: translateY(-2px); }
.login-card .fine { font-size: 11.5px; color: var(--ink-3); margin-top: 20px; text-align: center; }

/* Tables can scroll horizontally when they're wider than the viewport */
.adm-table-wrap {
  max-width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: var(--r-md);
  background: #fff;
  border: 1px solid var(--line);
}
.adm-table-wrap table.adm-table {
  border: 0;
  border-radius: 0;
  min-width: 700px;
}

@media (max-width: 1000px) {
  .admin-main { padding: 0 20px 50px; }
  .adm-stats { grid-template-columns: 1fr 1fr; }
}

/* ============ TABLET — under 900px sidebar becomes top app bar ============ */
@media (max-width: 900px) {
  .admin-shell { grid-template-columns: 1fr; min-width: 0; }

  .admin-sidebar {
    position: sticky; top: 0; z-index: 60;
    height: auto;
    width: 100%; max-width: 100%;
    padding: 12px 16px;
    display: grid;
    grid-template-columns: 1fr auto;
    grid-template-areas:
      "brand foot"
      "nav   nav";
    gap: 10px 12px;
    align-items: center;
    box-shadow: 0 8px 20px -16px rgba(0,0,0,0.4);
    overflow: hidden;
  }
  .admin-brand { grid-area: brand; padding: 0; margin: 0; border: 0; gap: 10px; min-width: 0; }
  .admin-brand-text { min-width: 0; }
  .admin-brand-text strong { font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .admin-brand-text small  { font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  .admin-nav {
    grid-area: nav;
    flex-direction: row;
    gap: 4px;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    margin: 0 -16px;
    padding: 0 16px 4px;
    width: calc(100% + 32px);
    max-width: calc(100% + 32px);
  }
  .admin-nav::-webkit-scrollbar { display: none; }
  .admin-nav a {
    padding: 8px 14px; white-space: nowrap; font-size: 12.5px;
    flex-shrink: 0;
  }

  .admin-foot { grid-area: foot; margin: 0; padding: 0; border: 0; }
  .admin-foot .user, .admin-foot .role { display: none; }
  .admin-foot form { margin: 0; }
  .admin-foot .admin-logout {
    width: auto; padding: 8px 14px; font-size: 12px;
    background: rgba(255,255,255,0.08);
    white-space: nowrap;
  }

  .admin-main { padding: 0 18px 50px; }

  /* Topbar / search */
  .admin-topbar { padding: 14px 0 10px; margin-bottom: 12px; }
  .admin-search { padding: 3px 4px 3px 14px; max-width: 100%; }
  .admin-search input { font-size: 12.5px; padding: 9px 4px; min-width: 0; }
  .admin-search .adm-btn { padding: 8px 14px; font-size: 12px; }

  /* Hero banner stacks vertically */
  .adm-hero {
    grid-template-columns: 1fr;
    gap: 18px;
    padding: 22px 22px;
    text-align: center;
  }
  .adm-hero-portrait { margin: 0 auto; }
  .adm-hero-summary { justify-content: center; flex-wrap: wrap; }
  .adm-hero-text h1 { font-size: 24px; }
  .adm-hero-text { min-width: 0; }

  /* Page header — title + button stack */
  .admin-header { flex-direction: column; align-items: flex-start; gap: 10px; }
  .admin-header > div { min-width: 0; max-width: 100%; }
  .admin-header h1 { font-size: 22px; word-break: break-word; }
  .admin-header .sub { font-size: 12.5px; word-break: break-word; }

  /* Toolbars / filters — full-width vertical stack */
  .adm-toolbar form {
    flex-direction: column;
    align-items: stretch;
    width: 100%;
  }
  .adm-input, .adm-select, .adm-toolbar form .adm-btn { width: 100%; }

  /* Grids */
  .adm-grid-2, .adm-grid-3 { grid-template-columns: 1fr; }

  /* Cards */
  .adm-card { padding: 18px 18px; max-width: 100%; }
  .adm-card-head { flex-wrap: wrap; gap: 8px; }
  .adm-card-head h2 { font-size: 14px; word-break: break-word; }

  /* Detail page key/value pairs */
  .detail-grid { grid-template-columns: 1fr; gap: 0; }
  .detail-grid .lab { padding-bottom: 4px; padding-top: 14px; border-top: 1px solid var(--line); }
  .detail-grid .val { padding-top: 0; padding-bottom: 10px; word-break: break-word; }
  .detail-grid .lab:first-of-type { border-top: 0; padding-top: 0; }

  /* Tables — cells tighter, parent scrolls if needed */
  .adm-table-wrap { max-width: 100%; }
  table.adm-table { font-size: 12.5px; }
  table.adm-table th, table.adm-table td { padding: 10px 12px; }
  .adm-table-wrap table.adm-table { min-width: 580px; }
  .actions .adm-btn { padding: 6px 12px; font-size: 11.5px; }

  /* Status dropdowns shouldn't overflow */
  .status-form { max-width: 100%; }
  .status-form select { font-size: 11px; padding: 5px 7px; max-width: 130px; }

  /* Pager */
  .pager { flex-direction: column; gap: 12px; align-items: flex-start; }
  .pager .links { display: flex; flex-wrap: wrap; gap: 4px; }
  .pager .links a, .pager .links span { padding: 7px 10px; font-size: 12px; margin: 0; }

  /* Empty state */
  .empty { padding: 40px 18px; }
}

/* ============ PHONE — 480px ============ */
@media (max-width: 480px) {
  .admin-sidebar { padding: 10px 14px; gap: 8px 10px; }
  .admin-main { padding: 0 14px 40px; }

  .admin-brand-mark { width: 34px; height: 34px; }
  .admin-brand-text small { display: none; }
  .admin-brand-text strong { font-size: 12.5px; }

  /* Search bar — input + button stack vertically */
  .admin-topbar { padding: 12px 0 10px; margin-bottom: 10px; }
  .admin-search {
    flex-direction: column;
    align-items: stretch;
    border-radius: 18px;
    padding: 8px 10px;
    gap: 6px;
  }
  .admin-search-ico { display: none; }
  .admin-search input { width: 100%; padding: 10px 6px; }
  .admin-search .adm-btn { width: 100%; padding: 9px 14px; }

  /* Stats single-column */
  .adm-stats { grid-template-columns: 1fr; }
  .adm-stat { padding: 14px 16px; }
  .adm-stat .val { font-size: 22px; }
  .adm-stat .lab { font-size: 10.5px; }

  /* Hero banner — even tighter */
  .adm-hero { padding: 18px 16px; gap: 12px; border-radius: 16px; }
  .adm-hero-portrait { width: 60px; height: 60px; }
  .adm-hero-text h1 { font-size: 19px; }
  .adm-hero-text p { font-size: 12.5px; }
  .adm-hero-text p.sub { font-size: 11.5px; }
  .adm-hero-summary { gap: 8px; width: 100%; }
  .adm-hero-stat { flex: 1; min-width: 0; padding: 11px 8px; }
  .adm-hero-stat strong { font-size: 18px; }
  .adm-hero-stat span { font-size: 9.5px; letter-spacing: 0.04em; }

  .admin-header h1 { font-size: 18px; line-height: 1.2; }
  .admin-header .sub { font-size: 12px; }

  .adm-card { padding: 14px 14px; }

  /* Tables font shrink */
  table.adm-table { font-size: 12px; }
  table.adm-table th, table.adm-table td { padding: 8px 10px; }
  .adm-table-wrap table.adm-table { min-width: 520px; }

  /* Login screen tightens */
  .login-card { padding: 24px 18px 22px; border-radius: 14px; }
  .login-card h1 { font-size: 18px; }
  .login-card .sub { font-size: 12px; }
  .login-card label input { padding: 11px 12px; font-size: 14px; }
  .login-card button { padding: 12px 16px; font-size: 13.5px; }
}

/* ============ TINY PHONE — 360px ============ */
@media (max-width: 360px) {
  .admin-sidebar { padding: 8px 10px; gap: 6px 8px; }
  .admin-main { padding: 0 10px 36px; }

  .admin-brand-mark { width: 30px; height: 30px; }
  .admin-brand-text strong { font-size: 11.5px; }

  .admin-nav a { padding: 7px 11px; font-size: 11.5px; }

  .admin-search { padding: 6px 8px; border-radius: 14px; }
  .admin-search input { font-size: 12px; padding: 9px 4px; }
  .admin-search .adm-btn { font-size: 12px; padding: 8px 12px; }

  .admin-foot .admin-logout { padding: 7px 10px; font-size: 11px; }

  .adm-stat { padding: 12px 14px; }
  .adm-stat .val { font-size: 20px; }
  .adm-card { padding: 12px 12px; }

  .admin-header h1 { font-size: 17px; }
  .adm-hero { padding: 16px 14px; }
  .adm-hero-text h1 { font-size: 18px; }

  /* Tables font even smaller */
  table.adm-table { font-size: 11.5px; }
  table.adm-table th, table.adm-table td { padding: 8px 9px; }
  .adm-table-wrap table.adm-table { min-width: 480px; }
}
</style>
</head>
<body>

@auth
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <div class="admin-brand-mark">
        <img src="{{ asset('images/founderimage4.jpeg') }}" alt="Victoria Love" />
      </div>
      <div class="admin-brand-text">
        <strong>Victoria Love</strong>
        <small>Admin · v1</small>
      </div>
    </div>

    @php $current = optional(request()->route())->getName() ?? ''; @endphp
    <nav class="admin-nav">
      <a href="{{ route('admin.dashboard') }}"      class="@if($current==='admin.dashboard') active @endif"><span class="ic">⬚</span> Dashboard</a>
      <a href="{{ route('admin.subscriptions') }}"  class="@if(str_starts_with($current,'admin.subscriptions')) active @endif"><span class="ic">◆</span> Subscriptions</a>
      <a href="{{ route('admin.payments') }}"       class="@if($current==='admin.payments') active @endif"><span class="ic">$</span> Payments</a>
      <a href="{{ route('admin.webhooks') }}"       class="@if(str_starts_with($current,'admin.webhooks')) active @endif"><span class="ic">⌁</span> Webhooks</a>
      <a href="{{ route('admin.ebooks') }}"         class="@if(str_starts_with($current,'admin.ebooks')) active @endif"><span class="ic">📖</span> eBooks Catalog</a>
      <a href="{{ route('admin.ebook-orders') }}"   class="@if(str_starts_with($current,'admin.ebook-orders')) active @endif"><span class="ic">📦</span> eBook Sales</a>
      <a href="{{ route('admin.onboarding') }}"     class="@if(str_starts_with($current,'admin.onboarding')) active @endif"><span class="ic">⚑</span> Paid Credit Repair Clients</a>
      <a href="{{ route('admin.funding') }}"        class="@if(str_starts_with($current,'admin.funding')) active @endif"><span class="ic">$</span> Funding Leads</a>
      <a href="{{ route('admin.mentorship') }}"     class="@if(str_starts_with($current,'admin.mentorship')) active @endif"><span class="ic">★</span> Mentorship Leads</a>
      <a href="{{ route('admin.contacts') }}"       class="@if(str_starts_with($current,'admin.contacts')) active @endif"><span class="ic">✉</span> Contact Us Submissions</a>
      <a href="{{ route('admin.leads') }}"          class="@if(str_starts_with($current,'admin.leads')) active @endif"><span class="ic">★</span> Popup Submissions</a>
      <a href="{{ url('/') }}" target="_blank"><span class="ic">↗</span> View site</a>
    </nav>

    <div class="admin-foot">
      <div class="user">{{ auth()->user()->name }}</div>
      <div class="role">{{ auth()->user()->email }}</div>
      <form method="POST" action="{{ route('admin.logout') }}">@csrf
        <button class="admin-logout" type="submit">Sign out</button>
      </form>
    </div>
  </aside>

  <main class="admin-main">

    <div class="admin-topbar">
      <form class="admin-search" method="GET" action="{{ route('admin.search') }}">
        <span class="admin-search-ico" aria-hidden="true">⌕</span>
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="Search across paid clients, funding leads, contact us submissions, popup submissions…"
               autocomplete="off"
               aria-label="Search the dashboard">
        <button type="submit" class="adm-btn">Search</button>
      </form>
    </div>

    @if (session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @if (session('error'))  <div class="flash error">{{ session('error') }}</div>@endif

    @yield('content')
  </main>
</div>
@else
  @yield('content')
@endauth

</body>
</html>
