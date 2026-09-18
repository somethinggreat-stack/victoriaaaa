<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Partnership Login</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box}
body{margin:0;font-family:'Manrope',sans-serif;background:#1a1214;color:#1a1214;
  min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
body::before{content:'';position:fixed;inset:0;
  background:radial-gradient(1100px 620px at 50% -12%, rgba(163,43,82,.42), transparent 62%);pointer-events:none}
.box{position:relative;width:100%;max-width:392px;background:#fff;border-radius:20px;padding:36px 32px;
  box-shadow:0 30px 70px -22px rgba(0,0,0,.6)}
.mark{font-family:'Instrument Serif',serif;font-style:italic;font-size:31px;color:#7b1e3c;line-height:1;margin-bottom:6px}
h1{margin:0 0 5px;font-size:19px;font-weight:800;letter-spacing:-.02em}
.sub{margin:0 0 26px;font-size:13px;color:#5c5054}
label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#938589;margin-bottom:6px}
input{width:100%;padding:11px 13px;border:1px solid rgba(26,18,20,.16);border-radius:10px;
  font-family:inherit;font-size:14px;margin-bottom:16px;background:#fff;color:#1a1214}
input:focus{outline:0;border-color:#a32b52;box-shadow:0 0 0 3px #fbeef2}
button{width:100%;background:#7b1e3c;color:#fff;border:0;border-radius:100px;padding:12px;
  font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .18s}
button:hover{background:#a32b52}
.err{background:#fdecec;color:#b91c1c;padding:11px 14px;border-radius:10px;font-size:12.5px;
  font-weight:600;margin-bottom:18px}
.ok{background:#e8f6ed;color:#15803d;padding:11px 14px;border-radius:10px;font-size:12.5px;
  font-weight:600;margin-bottom:18px}
.foot{margin-top:22px;text-align:center;font-size:11.5px;color:#938589}
</style>
</head>
<body>
<div class="box">
  <div class="mark">Burgundy</div>
  <h1>Partnership Dashboard</h1>
  <p class="sub">Client, payment and onboarding visibility.</p>

  @if(session('success'))<div class="ok">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="err">{{ session('error') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

  <form method="POST" action="{{ route('partnership.login') }}">
    @csrf
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">

    <button type="submit">Log in</button>
  </form>

  <div class="foot">Authorized access only.</div>
</div>
</body>
</html>
