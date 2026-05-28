@extends('layouts.app')

@section('title', 'Pick your time | Victoria Love')
@section('description', 'Pick a 15-minute slot on Victoria\'s calendar.')
@section('bodyClass', 'page-strategy-call-booked')

@section('content')

<section class="ct-hero">
  <div class="container">
    <div class="ct-hero-text reveal">
      <span class="eyebrow"><span class="ct-eye-dot"></span> Step 2 of 2 · Pick your time</span>
      <h1>Thanks{{ $leadName ? ', '.e(explode(' ', $leadName)[0]) : '' }} — now <em class="serif gradient-text">pick a time.</em></h1>
      <p class="lede">Your file is in. Pick a 15-minute slot below and you're locked in.</p>
      <ul class="ct-hero-trust">
        <li><span>✅</span>Form received</li>
        <li><span>📅</span>Pick any open slot</li>
        <li><span>📞</span>I'll call you on Zoom</li>
      </ul>
    </div>
  </div>
</section>

<section class="ct-body-section">
  <div class="container">

    <div class="ct-card reveal" style="padding:24px 24px 18px">
      <div class="calendly-inline-widget"
           data-url="{{ $calendlyUrl }}?hide_event_type_details=0&hide_gdpr_banner=1"
           style="min-width:320px;height:760px;"></div>
    </div>

    <p class="ct-fine" style="text-align:center;margin-top:20px">
      Calendar not loading? <a href="{{ $calendlyUrl }}" target="_blank" rel="noopener" style="color:var(--pink);font-weight:600">Open it in a new tab →</a>
    </p>

  </div>
</section>

<script src="https://assets.calendly.com/assets/external/widget.js" async></script>

@endsection
