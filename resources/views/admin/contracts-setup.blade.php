@extends('admin.layout')
@section('title', 'Contracts')

@section('content')
<div class="admin-header">
  <div>
    <h1>Contracts</h1>
    <div class="sub">Database setup hasn't finished</div>
  </div>
</div>

<div class="adm-card" style="border-color:#ffd8a8; background:#fffaf2;">
  <div class="adm-card-head"><h2>One step still to run</h2></div>

  <p style="font-size:14px; color:var(--ink-2); line-height:1.65; margin-bottom:14px;">
    The contracts feature needs a few new columns on the <code>payment_agreements</code> table, and they
    aren't there yet. The deploy script hides a failed migration, so the deploy reported success even
    though this step didn't run.
  </p>

  <p style="font-size:14px; color:var(--ink-2); line-height:1.65; margin-bottom:6px;">
    <strong>To fix it:</strong> run the contracts setup SQL in phpMyAdmin, then reload this page.
    Everything else on the site is unaffected — payments and onboarding are working normally.
  </p>

  <p style="font-size:12.5px; color:var(--ink-3); margin-top:16px; margin-bottom:0;">
    Clients who pay in the meantime will still be charged correctly. Their agreement simply won't be
    recorded until this is done, so they're worth checking here afterwards.
  </p>
</div>
@endsection
