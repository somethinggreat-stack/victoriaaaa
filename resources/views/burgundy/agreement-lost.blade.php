@extends('burgundy.layout')
@section('title', 'Continue Your Enrollment')

@section('body')

<main>
  <div class="wrap narrow">
    <div class="card success-card">
      <div class="ico" style="background:var(--amber-soft);color:var(--amber)">↻</div>
      <h1>Let's pick up where you left off</h1>
      <p>
        We couldn't find your session — that usually just means the page was closed
        or reopened on a different device.
      </p>

      <div class="next">
        <strong>If you've already paid:</strong>
        <p style="margin:0 0 16px;font-size:13.5px;color:var(--ink-2)">
          Continue straight to the onboarding form. We'll match you to your payment
          using your email and phone number.
        </p>
        <a href="{{ route('burgundy.onboarding.show') }}" class="btn wide" style="text-align:center">
          Continue to onboarding
        </a>
      </div>

      <p style="margin-top:26px;font-size:13.5px">
        Haven't paid yet? <a href="{{ route('burgundy.checkout.show') }}">Start your enrollment here.</a>
      </p>
    </div>
  </div>
</main>

@endsection
