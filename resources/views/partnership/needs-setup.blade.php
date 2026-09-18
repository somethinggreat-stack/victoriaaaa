<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup Needed · Partnership</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box}
body{margin:0;font-family:'Manrope',sans-serif;background:#faf7f6;color:#1a1214;
  min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;line-height:1.6}
.box{max-width:560px;background:#fff;border:1px solid rgba(26,18,20,.1);border-radius:18px;padding:34px}
.ico{width:52px;height:52px;border-radius:50%;background:#fdf2e3;color:#b45309;
  display:grid;place-items:center;font-size:25px;margin-bottom:18px}
h1{margin:0 0 10px;font-size:21px;font-weight:800;letter-spacing:-.02em}
p{margin:0 0 14px;font-size:14.5px;color:#574b4f}
ol{margin:0;padding-left:20px;font-size:14px;color:#574b4f}
li{margin-bottom:8px}
code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12.5px;
  background:#f3ecee;padding:2px 6px;border-radius:5px}
.note{margin-top:20px;padding-top:18px;border-top:1px solid rgba(26,18,20,.1);font-size:12.5px;color:#8d7f84}
</style>
</head>
<body>
<div class="box">
  <div class="ico">!</div>
  <h1>Database setup hasn't finished</h1>

  <p>
    The partnership tables are missing, or they don't have the shape this dashboard expects,
    so there's nothing to read yet.
  </p>

  <p><strong>To fix it, do one of these:</strong></p>
  <ol>
    <li>Run the setup SQL in phpMyAdmin, or</li>
    <li>Run <code>php artisan migrate</code>, then reload this page.</li>
  </ol>

  <div class="note">
    Worth knowing: the deploy script runs <code>migrate --force || true</code>, which hides a failed
    migration — so a deploy can report success even when this step didn't run.
  </div>
</div>
</body>
</html>
