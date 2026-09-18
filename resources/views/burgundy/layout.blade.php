<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Enrollment')</title>
<meta name="description" content="@yield('description', 'Secure enrollment.')">
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#1a1214; --ink-2:#574b4f; --ink-3:#8d7f84;
  --bg:#faf7f6; --bg-2:#f3ecee; --bg-3:#fff;
  --line:rgba(26,18,20,.1); --line-2:rgba(26,18,20,.18);
  --wine:#7b1e3c; --wine-2:#a32b52; --wine-soft:#fbeef2;
  --green:#15803d; --green-soft:#e8f6ed;
  --red:#b91c1c; --red-soft:#fdecec;
  --amber:#b45309; --amber-soft:#fdf2e3;
  --r:12px; --r-lg:18px;
}
*,*::before,*::after{box-sizing:border-box}
html,body{margin:0;padding:0;max-width:100%;overflow-x:hidden}
body{font-family:'Manrope',sans-serif;background:var(--bg);color:var(--ink);font-size:15px;line-height:1.6;-webkit-font-smoothing:antialiased}
a{color:var(--wine);text-decoration:none}
a:hover{text-decoration:underline}
.serif{font-family:'Instrument Serif',serif;font-style:italic}
img{max-width:100%;height:auto}

.wrap{max-width:940px;margin:0 auto;padding:0 20px}
.narrow{max-width:680px}

/* header */
.top{background:var(--ink);color:#fff;padding:16px 0}
.top .wrap{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
.top .mark{font-family:'Instrument Serif',serif;font-style:italic;font-size:23px;line-height:1}
.top .secure{font-size:11.5px;color:rgba(255,255,255,.6);letter-spacing:.05em;text-transform:uppercase;font-weight:600}

/* hero */
.hero{background:linear-gradient(180deg,#1a1214 0%,#2b1a20 100%);color:#fff;padding:44px 0 58px;position:relative}
.hero::after{content:'';position:absolute;inset:0;background:radial-gradient(760px 340px at 50% -20%,rgba(163,43,82,.4),transparent 62%);pointer-events:none}
.hero .wrap{position:relative}
.hero .eyebrow{display:inline-block;font-size:11px;letter-spacing:.13em;text-transform:uppercase;color:rgba(255,255,255,.62);font-weight:700;margin-bottom:12px}
.hero h1{margin:0 0 12px;font-size:clamp(27px,4.6vw,40px);line-height:1.13;font-weight:800;letter-spacing:-.025em}
.hero h1 em{color:#ff9dbd}
.hero p{margin:0;font-size:15.5px;color:rgba(255,255,255,.76);max-width:560px}
.steps{display:flex;gap:9px;flex-wrap:wrap;margin-top:22px}
.step{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);
  border-radius:100px;padding:6px 14px;font-size:12px;font-weight:600;color:rgba(255,255,255,.72)}
.step.on{background:var(--wine-2);border-color:var(--wine-2);color:#fff}
.step.done{background:rgba(21,128,61,.24);border-color:rgba(21,128,61,.44);color:#a7e9c0}
.step .n{width:18px;height:18px;border-radius:50%;background:rgba(255,255,255,.2);display:grid;place-items:center;font-size:10.5px;font-weight:800}

/* content */
main{padding:38px 0 70px}
.card{background:var(--bg-3);border:1px solid var(--line);border-radius:var(--r-lg);padding:26px;margin-bottom:22px;
  box-shadow:0 2px 14px -8px rgba(26,18,20,.14)}
.card h2{margin:0 0 5px;font-size:19px;font-weight:800;letter-spacing:-.02em}
.card h3{margin:22px 0 12px;font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-3)}
.card h3:first-child{margin-top:0}
.card .sub{margin:0 0 20px;font-size:13.5px;color:var(--ink-2)}

.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:15px}
.grid.two{grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.field{margin-bottom:15px}
.field.full{grid-column:1/-1}
label{display:block;font-size:12px;font-weight:700;color:var(--ink-2);margin-bottom:6px}
label .req{color:var(--wine-2)}
.hint{font-size:11.5px;color:var(--ink-3);margin-top:5px}
input[type=text],input[type=email],input[type=tel],input[type=password],input[type=number],select,textarea{
  width:100%;padding:11px 13px;border:1px solid var(--line-2);border-radius:10px;font-family:inherit;
  font-size:15px;background:var(--bg-3);color:var(--ink);transition:border-color .16s,box-shadow .16s}
input:focus,select:focus,textarea:focus{outline:0;border-color:var(--wine-2);box-shadow:0 0 0 3px var(--wine-soft)}
input.bad,select.bad{border-color:var(--red);background:var(--red-soft)}
.err-msg{color:var(--red);font-size:12px;font-weight:600;margin-top:5px;display:none}
.err-msg.on{display:block}

input[type=file]{width:100%;padding:11px;border:1.5px dashed var(--line-2);border-radius:10px;background:var(--bg-2);font-size:13px;font-family:inherit}
input[type=file]:hover{border-color:var(--wine-2)}

.check{display:flex;gap:10px;align-items:flex-start;margin-bottom:13px;font-size:13.5px;color:var(--ink-2)}
.check input{margin-top:3px;width:17px;height:17px;accent-color:var(--wine);flex-shrink:0}

.btn{display:inline-block;background:var(--wine);color:#fff;border:0;border-radius:100px;padding:15px 34px;
  font-size:15.5px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .18s,transform .18s;text-decoration:none}
.btn:hover{background:var(--wine-2);text-decoration:none;transform:translateY(-1px)}
.btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
.btn.wide{width:100%}
.btn.ghost{background:transparent;color:var(--ink);border:1px solid var(--line-2)}
.btn.ghost:hover{background:var(--bg-2)}

.alert{padding:14px 17px;border-radius:var(--r);margin-bottom:20px;font-size:13.5px}
.alert.err{background:var(--red-soft);color:var(--red)}
.alert.ok{background:var(--green-soft);color:var(--green)}
.alert.warn{background:var(--amber-soft);color:var(--amber)}
.alert strong{display:block;margin-bottom:4px}
.alert ul{margin:6px 0 0;padding-left:18px}

/* order summary */
.summary{background:var(--ink);color:#fff;border-radius:var(--r-lg);padding:24px;margin-bottom:22px}
.summary h2{color:#fff;margin:0 0 3px;font-size:17px}
.summary .tag{color:rgba(255,255,255,.6);font-size:13px;margin:0 0 18px}
.line{display:flex;justify-content:space-between;align-items:baseline;padding:11px 0;border-bottom:1px solid rgba(255,255,255,.1);font-size:14px}
.line:last-of-type{border-bottom:0}
.line .l{color:rgba(255,255,255,.76)}
.line .r{font-weight:700}
.line.total{margin-top:9px;padding-top:15px;border-top:2px solid rgba(255,255,255,.2);font-size:17px}
.line.total .r{font-size:22px;font-weight:800;color:#ff9dbd}
.note{margin-top:15px;font-size:12px;color:rgba(255,255,255,.55);line-height:1.5}

/* contract */
.contract{background:var(--bg-2);border:1px solid var(--line);border-radius:var(--r);padding:20px;
  max-height:390px;overflow-y:auto;font-size:13.5px;line-height:1.72;white-space:pre-wrap;
  font-family:ui-monospace,SFMono-Regular,Menlo,monospace;color:var(--ink-2);margin-bottom:8px}
.scrollnote{font-size:12px;color:var(--ink-3);margin:0 0 20px;text-align:center}

/* signature */
.sigwrap{border:1.5px dashed var(--line-2);border-radius:10px;background:var(--bg-3);position:relative;overflow:hidden}
#sigPad{display:block;width:100%;height:170px;touch-action:none;cursor:crosshair}
.sigph{position:absolute;inset:0;display:grid;place-items:center;color:var(--ink-3);font-size:13.5px;pointer-events:none}
.sigph.hide{display:none}
.sigbar{display:flex;justify-content:space-between;align-items:center;margin-top:9px}
.sigbar button{background:0;border:0;color:var(--wine);font-size:12.5px;font-weight:700;cursor:pointer;font-family:inherit;padding:0}

/* success */
.done{text-align:center;padding:52px 26px}
.done .ico{width:64px;height:64px;border-radius:50%;background:var(--green-soft);color:var(--green);
  display:grid;place-items:center;font-size:31px;margin:0 auto 20px}
.done h1{margin:0 0 10px;font-size:29px;font-weight:800;letter-spacing:-.025em}
.done p{margin:0 auto 22px;font-size:15.5px;color:var(--ink-2);max-width:470px}
.next{background:var(--bg-2);border-radius:var(--r);padding:20px;text-align:left;max-width:470px;margin:0 auto}
.next strong{display:block;margin-bottom:11px;font-size:13.5px}
.next ol{margin:0;padding-left:20px;font-size:13.5px;color:var(--ink-2)}
.next li{margin-bottom:7px}

footer{border-top:1px solid var(--line);padding:26px 0;text-align:center;font-size:12.5px;color:var(--ink-3)}
footer a{color:var(--ink-3);text-decoration:underline}

@media(max-width:640px){
  .hero{padding:32px 0 42px}
  .card{padding:20px}
  main{padding:26px 0 50px}
}
</style>
@stack('head')
</head>
<body>

<header class="top">
  <div class="wrap">
    <div class="mark">Burgundy</div>
    <div class="secure">🔒 Secure</div>
  </div>
</header>

@yield('body')

<footer>
  <div class="wrap">
    <p style="margin:0">
      &copy; {{ date('Y') }} Victoria Love Credit ·
      <a href="{{ route('legal.privacy-policy') }}">Privacy</a> ·
      <a href="{{ route('legal.terms-of-service') }}">Terms</a>
    </p>
  </div>
</footer>

@stack('scripts')
</body>
</html>
