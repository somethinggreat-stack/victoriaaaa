@extends('layouts.app')

@section('title', 'Payment complete | Victorious Opportunities')
@section('description', 'Your client\'s payment is complete.')
@section('bodyClass', 'page-checkout')

@section('head_extra')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<section style="padding:140px 0 110px;">
  <div class="container" style="max-width:600px;">
    <div style="text-align:center; background:#fff; border:1px solid var(--line); border-radius:20px; padding:48px 32px; box-shadow:0 30px 60px -30px rgba(20,16,14,.10);">
      <div style="width:66px;height:66px;border-radius:50%;background:#f0fdf4;color:#157a3d;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;">✓</div>
      <h1 style="font-size:28px;margin-bottom:10px;">Paid in full — thank you!</h1>
      <p style="color:var(--ink-2);font-size:15.5px;margin-bottom:6px;">
        {{ $agreement->client_name }}'s service is paid for. We emailed {{ $agreement->email }} their contract and next steps.
      </p>
      <p style="color:var(--ink-2);font-size:14.5px;margin-bottom:22px;">If they don't see it, check spam — or send them this link yourself:</p>
      <input type="text" readonly value="{{ $signUrl }}" onclick="this.select()" style="width:100%;padding:12px 14px;border:1px solid var(--line);border-radius:10px;font:13px ui-monospace,monospace;">
      <button type="button" class="btn btn-pink" style="margin-top:14px;"
        onclick="navigator.clipboard&&navigator.clipboard.writeText('{{ $signUrl }}');this.textContent='✓ Copied'">Copy link</button>
    </div>
  </div>
</section>
@endsection
