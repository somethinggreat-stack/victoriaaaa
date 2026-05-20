<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Standalone reviewer preview page (Authorize.Net underwriting reviews, etc.).
 *
 * This controller is intentionally isolated from the rest of the app:
 *   - no DB queries
 *   - no Laravel Auth / User model / sessions table
 *   - no admin layout / blade view caching
 *   - no view files (renders inline HTML)
 *
 * Credentials come from .env (REVIEWER_EMAIL / REVIEWER_PASSWORD). If a request
 * hits any uncaught exception inside Laravel, this page won't — it depends on
 * exactly nothing.
 */
class ReviewerPreviewController extends Controller
{
    public function show(Request $request)
    {
        // Read via config(), not env(), so values survive `config:cache`
        // (env() returns null inside controllers once config has been cached).
        $email    = trim((string) config('auth_reviewer.email', ''));
        $password = (string) config('auth_reviewer.password', '');

        $submittedEmail    = (string) $request->input('email', '');
        $submittedPassword = (string) $request->input('password', '');

        $authed = $email !== '' && $password !== ''
            && hash_equals(strtolower($email), strtolower($submittedEmail))
            && hash_equals($password, $submittedPassword);

        if ($request->isMethod('POST') && !$authed) {
            return new Response($this->loginHtml('Invalid email or password.'), 401, [
                'Content-Type' => 'text/html; charset=utf-8',
            ]);
        }

        if (!$authed) {
            return new Response($this->loginHtml(), 200, [
                'Content-Type' => 'text/html; charset=utf-8',
            ]);
        }

        return new Response($this->dashboardHtml(), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    private function loginHtml(string $error = ''): string
    {
        $errorHtml   = $error !== '' ? '<div class="err">' . htmlspecialchars($error, ENT_QUOTES) . '</div>' : '';
        $csrfDisable = ''; // intentionally no CSRF: this page is stateless

        return <<<HTML
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Reviewer access · Victoria Love</title>
<style>
  *,*::before,*::after{box-sizing:border-box}
  body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Manrope,sans-serif;background:#faf7f2;color:#15110f;display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px}
  .card{background:#fff;border:1px solid rgba(20,16,14,.08);border-radius:18px;padding:34px 32px;max-width:420px;width:100%;box-shadow:0 30px 60px -25px rgba(20,16,14,.25)}
  h1{font-size:22px;margin:0 0 6px;font-weight:700;letter-spacing:-.015em}
  .sub{color:#5a544f;font-size:13.5px;margin:0 0 22px}
  label{display:block;margin:0 0 14px}
  label span{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#5a544f;font-weight:700;margin-bottom:6px}
  input{width:100%;padding:12px 14px;border:1.5px solid rgba(20,16,14,.08);border-radius:12px;font-size:14.5px;font-family:inherit}
  input:focus{outline:none;border-color:#e63179;box-shadow:0 0 0 4px rgba(230,49,121,.12)}
  button{width:100%;margin-top:6px;background:#e63179;color:#fff;border:none;border-radius:100px;padding:13px 20px;font-size:14.5px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 16px 30px -12px rgba(230,49,121,.55)}
  button:hover{background:#15110f}
  .err{background:#fff3f3;border:1px solid #fbd6d6;color:#8b1414;padding:11px 14px;border-radius:12px;margin-bottom:18px;font-size:13px}
  .fine{font-size:11.5px;color:#968f86;margin-top:18px;text-align:center}
</style></head><body>
<form class="card" method="POST" action="/reviewer-access">
  $csrfDisable
  <h1>Reviewer access</h1>
  <p class="sub">Sign in to view the merchant admin preview for victorialovecredit.com.</p>
  $errorHtml
  <label><span>Email</span><input type="email" name="email" required autofocus autocomplete="username"></label>
  <label><span>Password</span><input type="password" name="password" required autocomplete="current-password"></label>
  <button type="submit">Sign in</button>
  <div class="fine">Authorized access only · Read-only preview</div>
</form>
</body></html>
HTML;
    }

    private function dashboardHtml(): string
    {
        $today = date('l, F j, Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Reviewer preview · Victorious Opportunities</title>
<style>
  *,*::before,*::after{box-sizing:border-box}
  body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Manrope,sans-serif;background:#faf7f2;color:#15110f;font-size:14px;line-height:1.5}
  .serif{font-family:'Instrument Serif',Georgia,serif;font-style:italic}
  .shell{max-width:1100px;margin:0 auto;padding:32px 24px 60px}
  .topbar{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(20,16,14,.08);padding-bottom:18px;margin-bottom:24px;gap:16px;flex-wrap:wrap}
  .brand{display:flex;align-items:center;gap:12px}
  .brand-mark{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#e63179,#ff7eb3);display:grid;place-items:center;color:#fff;font-weight:700;font-size:15px}
  .brand strong{display:block;font-size:15px;font-weight:700}
  .brand small{display:block;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;color:#968f86}
  .badge{background:#fdeaf2;color:#e63179;padding:6px 12px;border-radius:100px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
  .signout{background:none;border:1px solid rgba(20,16,14,.16);padding:8px 16px;border-radius:100px;font-size:12.5px;font-weight:600;cursor:pointer;color:#15110f;font-family:inherit;text-decoration:none}
  .hero{background:linear-gradient(135deg,#1f0e18 0%,#2c1622 50%,#14080d 100%);color:#fff;border-radius:22px;padding:32px 36px;margin-bottom:24px;box-shadow:0 30px 60px -25px rgba(20,16,14,.45)}
  .hero .eye{font-size:11px;letter-spacing:.18em;text-transform:uppercase;font-weight:700;color:#ff7eb3;margin-bottom:8px;display:block}
  .hero h1{font-size:30px;margin:0 0 6px;font-weight:600;letter-spacing:-.025em}
  .hero .gradient{background:linear-gradient(135deg,#ff7eb3,#ffd9a0);-webkit-background-clip:text;background-clip:text;color:transparent}
  .hero p{margin:0;color:rgba(255,255,255,.78);font-size:14px;max-width:720px}
  .notice{background:#fff5f9;border:1px solid #fbd6e6;color:#8b1452;padding:14px 18px;border-radius:14px;margin-bottom:24px;font-size:13.5px}
  .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px}
  @media (max-width:700px){.stats{grid-template-columns:1fr}}
  .stat{background:#fff;border:1px solid rgba(20,16,14,.08);border-radius:14px;padding:18px 20px;box-shadow:0 8px 20px -16px rgba(20,16,14,.18)}
  .stat .lab{font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#968f86;font-weight:700;margin-bottom:10px}
  .stat .val{font-size:18px;font-weight:700;letter-spacing:-.01em}
  .stat .delta{font-size:11.5px;color:#5a544f;margin-top:4px}
  .card{background:#fff;border:1px solid rgba(20,16,14,.08);border-radius:14px;padding:24px 26px;margin-bottom:18px}
  .card h2{font-size:15px;font-weight:700;margin:0 0 12px}
  .card p{margin:0 0 10px;color:#5a544f;font-size:13.5px;line-height:1.65}
  .card p:last-child{margin-bottom:0}
  .card code{background:#f3ede4;padding:2px 7px;border-radius:6px;font-size:12.5px}
  .card a{color:#e63179;font-weight:600;text-decoration:none}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
  @media (max-width:700px){.row{grid-template-columns:1fr}}
</style></head><body>
<div class="shell">

  <header class="topbar">
    <div class="brand">
      <div class="brand-mark">VO</div>
      <div><strong>Victorious Opportunities</strong><small>Admin · Reviewer preview</small></div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <span class="badge">Read-only reviewer</span>
      <a class="signout" href="/reviewer-access?logout=1">Sign out</a>
    </div>
  </header>

  <section class="hero">
    <span class="eye">Reviewer access · $today</span>
    <h1>Victorious Opportunities <span class="serif gradient">Admin.</span></h1>
    <p>Read-only reviewer account. Customer records, payment ledgers, and lead data are not displayed in this view. Live customer data is accessed by Victoria from the owner-admin login.</p>
  </section>

  <div class="notice"><strong>About this view:</strong> this is a sandboxed preview prepared for payment-processor underwriting reviews. It intentionally exposes no customer PII.</div>

  <div class="row">
    <div class="card">
      <h2>About this admin area</h2>
      <p>This dashboard is the merchant's internal back-office for <a href="/" target="_blank">victorialovecredit.com</a>. It manages site form submissions and payment records for Victorious Opportunities.</p>
      <p><strong>Customers do not log in here.</strong> Paid customers receive their own client portal through Credit Repair Cloud (the case-management software used to deliver the credit-repair service). Their secure portal is hosted at <code>secureportal</code> and is completely separate from this site.</p>
      <p>This reviewer account is read-only and intentionally limits what is visible — customer PII, payment ledgers, and lead data are not displayed.</p>
    </div>

    <div class="card">
      <h2>Merchant snapshot</h2>
      <div class="stats" style="grid-template-columns:1fr;margin:0">
        <div class="stat"><div class="lab">Site</div><div class="val">victorialovecredit.com</div><div class="delta">Public marketing site</div></div>
        <div class="stat"><div class="lab">Payment processor</div><div class="val">Authorize.Net</div><div class="delta">Accept.js · tokenized cards (no PAN storage)</div></div>
        <div class="stat"><div class="lab">Customer portal</div><div class="val">Credit Repair Cloud</div><div class="delta">Issued separately per client</div></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Business details</h2>
    <p><strong>Legal name:</strong> Victorious Opportunities</p>
    <p><strong>Business address:</strong> 18034 Ventura Blvd, Encino, CA 91316</p>
    <p><strong>Support email:</strong> <a href="mailto:support@victorialovecredit.com">support@victorialovecredit.com</a></p>
    <p><strong>Services:</strong> Credit repair, credit consultations, DIY business + funding coaching, mentorship</p>
  </div>

</div>
</body></html>
HTML;
    }
}
