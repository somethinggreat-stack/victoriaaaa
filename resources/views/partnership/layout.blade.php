<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Partnership') · Burgundy</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#1a1214; --ink-2:#5c5054; --ink-3:#938589;
  --bg:#faf7f6; --bg-2:#f2ebec; --bg-3:#ffffff;
  --line:rgba(26,18,20,.09); --line-2:rgba(26,18,20,.16);
  --wine:#7b1e3c; --wine-2:#a32b52; --wine-soft:#fbeef2; --wine-tint:#fff6f8;
  --green:#15803d; --green-soft:#e8f6ed;
  --red:#b91c1c; --red-soft:#fdecec;
  --amber:#b45309; --amber-soft:#fdf2e3;
  --blue:#1d4ed8; --blue-soft:#eaf0fd;
  --r:12px; --r-lg:16px;
}
*,*::before,*::after{box-sizing:border-box}
html,body{margin:0;padding:0;width:100%;max-width:100%;overflow-x:hidden}
body{font-family:'Manrope',sans-serif;background:var(--bg);color:var(--ink);font-size:14px;line-height:1.5;-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}
.serif{font-family:'Instrument Serif',serif;font-style:italic}

/* ---------- shell ---------- */
.shell{min-height:100vh}
.sidebar{background:var(--ink);color:rgba(255,255,255,.85);padding:18px 14px;position:fixed;top:0;left:0;width:238px;height:100vh;overflow-y:auto;display:flex;flex-direction:column;z-index:40;scrollbar-width:none}
.sidebar::-webkit-scrollbar{display:none}
.brand{padding-bottom:16px;margin-bottom:14px;border-bottom:1px solid rgba(255,255,255,.09)}
.brand strong{display:block;font-size:15px;color:#fff;font-weight:800;letter-spacing:-.01em}
.brand small{display:block;font-size:10.5px;color:rgba(255,255,255,.45);letter-spacing:.1em;text-transform:uppercase;margin-top:3px}
.nav{display:flex;flex-direction:column;gap:2px}
.nav a{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:9px;font-size:13px;font-weight:500;color:rgba(255,255,255,.72);transition:background .18s,color .18s}
.nav a:hover{background:rgba(255,255,255,.07);color:#fff}
.nav a.active{background:var(--wine-2);color:#fff;font-weight:600}
.nav .ic{width:17px;text-align:center;opacity:.9;flex-shrink:0}
.nav .badge{margin-left:auto;background:var(--amber);color:#fff;font-size:10.5px;font-weight:700;padding:1px 7px;border-radius:100px}
.nav a.active .badge{background:rgba(255,255,255,.25)}
.foot{margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.09)}
.linkbox{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:10px;margin-bottom:10px}
.linkbox label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.45);margin-bottom:5px;font-weight:600}
.linkbox input{width:100%;background:rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.8);border-radius:6px;padding:6px 7px;font-size:10.5px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace}
.linkbox button{width:100%;margin-top:6px;background:var(--wine-2);color:#fff;border:0;border-radius:100px;padding:7px;font-size:11.5px;font-weight:700;cursor:pointer;font-family:inherit}
.linkbox button:hover{background:var(--wine)}
.logout{width:100%;background:rgba(255,255,255,.06);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.1);padding:9px 12px;border-radius:100px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:inherit}
.logout:hover{background:var(--wine-2);color:#fff;border-color:var(--wine-2)}

.main{margin-left:238px;padding:0 34px 60px;min-width:0;max-width:calc(100% - 238px)}
.topbar{position:sticky;top:0;z-index:30;background:var(--bg);padding:22px 0 16px;border-bottom:1px solid var(--line);margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.topbar h1{margin:0;font-size:23px;font-weight:800;letter-spacing:-.02em}
.topbar p{margin:3px 0 0;font-size:13px;color:var(--ink-2)}

/* ---------- pieces ---------- */
.card{background:var(--bg-3);border:1px solid var(--line);border-radius:var(--r-lg);padding:20px;margin-bottom:20px}
.card h2{margin:0 0 4px;font-size:15px;font-weight:700}
.card .sub{margin:0 0 16px;font-size:12.5px;color:var(--ink-2)}

.tiles{display:grid;grid-template-columns:repeat(auto-fill,minmax(158px,1fr));gap:12px;margin-bottom:22px}
.tile{background:var(--bg-3);border:1px solid var(--line);border-radius:var(--r);padding:14px 15px;transition:border-color .18s,transform .18s}
a.tile:hover{border-color:var(--wine-2);transform:translateY(-1px)}
.tile .k{font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-3);font-weight:700}
.tile .v{font-size:25px;font-weight:800;letter-spacing:-.025em;margin-top:5px;line-height:1.1}
.tile .n{font-size:11.5px;color:var(--ink-2);margin-top:2px}
.tile.wine .v{color:var(--wine)}
.tile.green .v{color:var(--green)}
.tile.red .v{color:var(--red)}
.tile.amber{background:var(--amber-soft);border-color:rgba(180,83,9,.25)}
.tile.amber .v{color:var(--amber)}

table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-3);font-weight:700;padding:0 12px 9px;border-bottom:1px solid var(--line);white-space:nowrap}
td{padding:11px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
tr:last-child td{border-bottom:0}
tbody tr:hover{background:var(--wine-tint)}
.tbl-wrap{overflow-x:auto;margin:0 -20px;padding:0 20px}
.nm{font-weight:700}
.mut{color:var(--ink-3)}
.mono{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px}

.pill{display:inline-block;padding:3px 9px;border-radius:100px;font-size:11px;font-weight:700;white-space:nowrap}
.pill.grey{background:var(--bg-2);color:var(--ink-2)}
.pill.wine{background:var(--wine-soft);color:var(--wine)}
.pill.green{background:var(--green-soft);color:var(--green)}
.pill.red{background:var(--red-soft);color:var(--red)}
.pill.amber{background:var(--amber-soft);color:var(--amber)}
.pill.blue{background:var(--blue-soft);color:var(--blue)}

.btn{display:inline-block;background:var(--wine);color:#fff;border:0;border-radius:100px;padding:9px 18px;font-size:12.5px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .18s}
.btn:hover{background:var(--wine-2)}
.btn.ghost{background:transparent;color:var(--ink);border:1px solid var(--line-2)}
.btn.ghost:hover{background:var(--bg-2)}
.btn.sm{padding:6px 13px;font-size:11.5px}
.btn.danger{background:var(--red)}

input[type=text],input[type=email],input[type=search],input[type=password],select,textarea{
  width:100%;padding:9px 11px;border:1px solid var(--line-2);border-radius:9px;font-family:inherit;font-size:13px;background:var(--bg-3);color:var(--ink)}
input:focus,select:focus,textarea:focus{outline:0;border-color:var(--wine-2);box-shadow:0 0 0 3px var(--wine-soft)}
label.fl{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-3);margin-bottom:5px}
.field{margin-bottom:14px}

.flash{padding:12px 16px;border-radius:var(--r);margin-bottom:18px;font-size:13px;font-weight:600}
.flash.ok{background:var(--green-soft);color:var(--green)}
.flash.err{background:var(--red-soft);color:var(--red)}

.filters{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:16px}
.filters a{padding:6px 13px;border-radius:100px;font-size:12px;font-weight:600;background:var(--bg-3);border:1px solid var(--line);color:var(--ink-2)}
.filters a:hover{border-color:var(--wine-2);color:var(--wine)}
.filters a.on{background:var(--wine);border-color:var(--wine);color:#fff}

.empty{text-align:center;padding:44px 20px;color:var(--ink-3)}
.empty strong{display:block;font-size:15px;color:var(--ink-2);margin-bottom:5px}

.pager{display:flex;justify-content:space-between;align-items:center;margin-top:18px;font-size:12.5px;color:var(--ink-2);gap:12px;flex-wrap:wrap}
.pager .links{display:flex;flex-wrap:wrap;gap:4px}
.pager .links a,.pager .links span{display:inline-block;padding:6px 11px;border-radius:8px;font-weight:600;font-size:12.5px}
.pager .links a{background:var(--bg-3);border:1px solid var(--line);color:var(--ink)}
.pager .links a:hover{background:var(--wine);color:#fff;border-color:var(--wine)}
.pager .links .current{background:var(--ink);color:#fff}
.pager .links .disabled{color:var(--ink-3)}

@media(max-width:900px){
  .sidebar{position:static;width:100%;height:auto}
  .main{margin-left:0;max-width:100%;padding:0 16px 40px}
  .nav{flex-direction:row;flex-wrap:wrap}
  .foot{margin-top:14px}
}
</style>
@stack('head')
</head>
<body>
@php
  // Schema readiness is enforced by the partnership.schema middleware before
  // any controller runs, so by the time this renders the tables are known good.
  $reviewCount = \App\Models\BurgundyClient::where('match_status', 'needs_review')
      ->orWhereNotNull('possible_duplicate_of')->count();
@endphp
<div class="shell">
  <aside class="sidebar">
    <div class="brand">
      <strong>Burgundy × Victoria</strong>
      <small>Partnership</small>
    </div>

    <nav class="nav">
      <a href="{{ route('partnership.dashboard') }}" class="{{ request()->routeIs('partnership.dashboard') ? 'active' : '' }}">
        <span class="ic">▦</span> Overview
      </a>
      <a href="{{ route('partnership.clients') }}" class="{{ request()->routeIs('partnership.clients*') ? 'active' : '' }}">
        <span class="ic">☰</span> Clients
      </a>
      <a href="{{ route('partnership.review') }}" class="{{ request()->routeIs('partnership.review*') ? 'active' : '' }}">
        <span class="ic">⚑</span> Needs Review
        @if($reviewCount > 0)<span class="badge">{{ $reviewCount }}</span>@endif
      </a>
      <a href="{{ route('partnership.payments') }}" class="{{ request()->routeIs('partnership.payments*') ? 'active' : '' }}">
        <span class="ic">$</span> Payments
      </a>
      <a href="{{ route('partnership.import') }}" class="{{ request()->routeIs('partnership.import*') ? 'active' : '' }}">
        <span class="ic">↑</span> Import Clients
      </a>
    </nav>

    <div class="foot">
      <div class="linkbox">
        <label>Checkout link — $100</label>
        <input type="text" id="coLink" readonly value="{{ route('burgundy.checkout.show') }}" onclick="this.select()">
        <button type="button" onclick="copyCo(this)">Copy link</button>
      </div>
      <form method="POST" action="{{ route('partnership.logout') }}">@csrf
        <button type="submit" class="logout">Log out</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <h1>@yield('title', 'Partnership')</h1>
        @hasSection('subtitle')<p>@yield('subtitle')</p>@endif
      </div>
      <div>@yield('actions')</div>
    </div>

    @if(session('success'))<div class="flash ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash err">{{ session('error') }}</div>@endif

    @yield('content')
  </main>
</div>

<script>
function copyCo(btn){
  var i = document.getElementById('coLink');
  i.select(); i.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(i.value).then(function(){
    var t = btn.textContent; btn.textContent = 'Copied';
    setTimeout(function(){ btn.textContent = t; }, 1400);
  });
}
</script>
@stack('scripts')
</body>
</html>
