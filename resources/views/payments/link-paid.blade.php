@extends('layouts.app')

@section('title', 'Payment complete | Victoria Love')
@section('description', 'This payment has already been completed.')
@section('bodyClass', 'page-checkout')

@section('head_extra')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<section style="padding:150px 0 120px;">
  <div class="container" style="max-width:560px;">
    <div style="text-align:center; background:var(--bg-3,#fff); border:1px solid var(--line); border-radius:var(--r-lg,20px); padding:52px 34px; box-shadow:0 30px 60px -30px rgba(20,16,14,0.10);">
      <div style="width:66px;height:66px;border-radius:50%;background:#f0fdf4;color:#157a3d;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;">✓</div>
      <h1 style="font-size:26px;margin-bottom:10px;">Payment already complete</h1>
      <p style="color:var(--ink-2);font-size:15.5px;max-width:420px;margin:0 auto 8px;">
        This payment link has already been paid — no further charge is needed. If you believe this is a mistake,
        please contact us and we'll help right away.
      </p>
      <p style="color:var(--ink-3);font-size:13px;margin-top:18px;">
        <a href="{{ route('contact.show') }}" style="color:var(--pink);font-weight:600;">Contact support</a>
      </p>
    </div>
  </div>
</section>
@endsection
