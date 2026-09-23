@extends('burgundy.layout')
@section('title', 'Agreement Signed — ' . $brand)

@section('body')

<main>
  <div class="wrap narrow">
    <div class="card success-card">
      <div class="ico">✓</div>
      <h1>Signed and on file{{ $agreement->full_name ? ', ' . \Illuminate\Support\Str::before($agreement->full_name, ' ') : '' }}.</h1>
      <p>
        Your service agreement is complete and we have kept a copy with your record.
        There is nothing else for you to do here.
      </p>

      <div class="next">
        <strong>What you agreed to</strong>
        <ol>
          <li>{{ $agreement->plan_label }}</li>
          <li>Charged today: ${{ number_format((float) $agreement->deposit_amount, 2) }}</li>
          @if ($agreement->installment_amount && (float) $agreement->installment_amount > 0)
            <li>
              Then ${{ number_format((float) $agreement->installment_amount, 2) }} per month{{ $agreement->installment_count ? '' : ', until you cancel' }}
            </li>
          @endif
          <li>Signed {{ optional($agreement->signed_at)->format('F j, Y') }}</li>
        </ol>
      </div>

      @if ($agreement->next_url)
        <a href="{{ $agreement->next_url }}" class="btn wide" style="margin-top:22px;text-align:center">Continue</a>
      @endif

      <p style="margin-top:26px;font-size:13.5px;color:var(--ink-3)">
        Questions? <a href="mailto:support@victorialovecredit.com">Get in touch any time.</a>
      </p>
    </div>
  </div>
</main>

@endsection
