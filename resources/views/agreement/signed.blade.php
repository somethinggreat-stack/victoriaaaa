@extends('burgundy.layout')
@section('title', 'Agreement Signed — ' . $brand)

@section('body')

<main>
  <div class="wrap narrow">
    <div class="card success-card">
      <div class="ico">✓</div>
      @php
        $greeting = $agreement->requires_cosigner
          ? 'Signed and on file.'
          : 'Signed and on file' . ($agreement->full_name
              ? ', ' . \Illuminate\Support\Str::before($agreement->full_name, ' ')
              : '') . '.';
      @endphp
      <h1>{{ $greeting }}</h1>
      <p>
        @if ($agreement->requires_cosigner)
          Both signatures are in. Your service agreement is complete and we have kept a copy
          with your record — there is nothing else for either of you to do.
        @else
          Your service agreement is complete and we have kept a copy with your record.
          There is nothing else for you to do here.
        @endif
      </p>

      <div class="next">
        <strong>What you agreed to</strong>
        <ol>
          <li>{{ $agreement->plan_label }}</li>
          @if ($agreement->requires_cosigner)
            <li>Signed by {{ $agreement->partiesLabel() }}</li>
          @endif
          <li>Charged today: ${{ number_format((float) $agreement->deposit_amount, 2) }}</li>
          @if ($agreement->installment_amount && (float) $agreement->installment_amount > 0)
            @php
              $every = $agreement->recurring_interval === 'week' ? 'week' : 'month';
              $tail  = $agreement->installment_count
                ? ', ' . $agreement->installment_count . ' time' . ($agreement->installment_count == 1 ? '' : 's')
                : ', until you cancel';
            @endphp
            <li>Then ${{ number_format((float) $agreement->installment_amount, 2) }} per {{ $every }}{{ $tail }}</li>
            @if ($agreement->installment_count)
              <li>Total: ${{ number_format((float) $agreement->deposit_amount + ((float) $agreement->installment_amount * $agreement->installment_count), 2) }}</li>
            @endif
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
