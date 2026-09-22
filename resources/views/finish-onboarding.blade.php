@extends('burgundy.layout')
@section('title', 'Finish Your File — ' . $brand)
@section('description', 'Two quick items and your credit file is ready to go.')

@section('body')

@if ($alreadyDone)

  <main>
    <div class="wrap narrow">
      <div class="card success-card">
        <div class="ico">✓</div>
        <h1>Thank you{{ $submission->firstname ? ', ' . $submission->firstname : '' }} — that's everything.</h1>
        <p>Your file is complete and has gone to our processing team. There's nothing else for you to do.</p>

        <div class="next">
          <strong>What happens next</strong>
          <ol>
            <li>Your credit reports are pulled and reviewed in detail.</li>
            <li>Your first dispute round goes out within 7 days.</li>
            <li>You'll hear from us with updates as results come in.</li>
          </ol>
        </div>

        <p style="margin-top:26px;font-size:13.5px;color:var(--ink-3)">
          Questions? <a href="mailto:support@victorialovecredit.com">Get in touch any time.</a>
        </p>
      </div>
    </div>
  </main>

@else

<section class="hero">
  <div class="wrap">
    <span class="eyebrow">Almost there</span>
    <h1>Just <em class="serif">two things</em> left.</h1>
    <p>
      We already have everything else you sent us. We only need your credit-monitoring
      login and two documents — about a minute.
    </p>
  </div>
</section>

<main>
  <div class="wrap narrow">

    @if ($errors->any())
      <div class="alert err">
        <strong>Please fix the following:</strong>
        <ul>
          @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- Shown so they can confirm it's them — and so it's obvious we are not
         asking them to type any of it again. --}}
    <div class="card" style="background:var(--bg-2)">
      <h3 style="margin-top:0">Already on file</h3>
      <div class="grid" style="gap:12px 14px">
        <div class="col-6">
          <div class="hint" style="margin:0">Name</div>
          <div style="font-weight:700">{{ trim($submission->firstname . ' ' . $submission->lastname) }}</div>
        </div>
        <div class="col-6">
          <div class="hint" style="margin:0">Date of birth</div>
          <div style="font-weight:700">{{ $submission->birth_date?->format('M j, Y') ?: '—' }}</div>
        </div>
        <div class="col-6">
          <div class="hint" style="margin:0">Address</div>
          <div style="font-weight:700">{{ $submission->street_address }}, {{ $submission->city }}, {{ $submission->state }} {{ $submission->zip }}</div>
        </div>
        <div class="col-6">
          <div class="hint" style="margin:0">Social Security Number</div>
          <div style="font-weight:700" class="mono">{{ $needsSsn ? 'needs re-entering' : $submission->masked_ssn }}</div>
        </div>
      </div>
      <p class="hint" style="margin-bottom:0">
        Something wrong here? <a href="mailto:support@victorialovecredit.com?subject=Correction%20to%20my%20details">Tell us</a> and we'll fix it — don't re-submit.
      </p>
    </div>

    <form id="finishForm" class="card" method="POST" action="{{ $postUrl }}" enctype="multipart/form-data" novalidate>
      @csrf

      @if ($needsSsn)
        <h3>Confirm your SSN</h3>
        <div class="grid">
          <div class="field col-6">
            <label for="ssn">Social Security Number <span class="req">*</span></label>
            <input type="text" id="ssn" name="ssn" required placeholder="123-45-6789"
                   inputmode="numeric" maxlength="11" value="{{ old('ssn') }}">
            <div class="hint">🔒 Encrypted at rest. Required by the bureaus to verify your identity.</div>
          </div>
        </div>
      @endif

      <h3>Credit monitoring login</h3>
      <p class="hint" style="margin:-6px 0 14px">
        We use this to pull your three-bureau reports and track progress. No account yet?
        Create one at <a href="https://www.myfreescorenow.com" target="_blank" rel="noopener">MyFreeScoreNow</a> first.
      </p>
      <div class="grid">
        <div class="field col-6">
          <label for="credit_monitoring_email">Monitoring login email <span class="req">*</span></label>
          <input type="email" id="credit_monitoring_email" name="credit_monitoring_email" required
                 autocomplete="off" value="{{ old('credit_monitoring_email', $submission->email) }}">
        </div>
        <div class="field col-6">
          <label for="credit_monitoring_password">Monitoring password <span class="req">*</span></label>
          <input type="text" id="credit_monitoring_password" name="credit_monitoring_password" required
                 autocomplete="off" value="{{ old('credit_monitoring_password') }}">
        </div>
        <div class="field full">
          <label for="credit_monitoring_security_answer">Security question answer</label>
          <input type="text" id="credit_monitoring_security_answer" name="credit_monitoring_security_answer"
                 autocomplete="off" value="{{ old('credit_monitoring_security_answer') }}">
          <div class="hint">If your monitoring account asks a security question, put the answer here.</div>
        </div>
      </div>

      <h3>Documents</h3>
      <p class="hint" style="margin:-6px 0 14px">
        Required by the bureaus to prove identity and address. PDF or photo, up to 10 MB each.
      </p>
      <div class="grid">
        <div class="field col-6">
          <label for="drivers_license">Driver's license or state ID <span class="req">*</span></label>
          <input type="file" id="drivers_license" name="drivers_license" required accept=".pdf,.jpg,.jpeg,.png,.webp">
        </div>
        <div class="field col-6">
          <label for="proof_of_address">Proof of address <span class="req">*</span></label>
          <input type="file" id="proof_of_address" name="proof_of_address" required accept=".pdf,.jpg,.jpeg,.png,.webp">
          <div class="hint">A utility bill, lease or bank statement from the last 60 days.</div>
        </div>
        <div class="field full">
          <label for="ssn_card">Social Security card <span style="color:var(--ink-3);font-weight:500">(optional, speeds things up)</span></label>
          <input type="file" id="ssn_card" name="ssn_card" accept=".pdf,.jpg,.jpeg,.png,.webp">
        </div>
      </div>

      <button type="submit" id="finishBtn" class="btn wide" style="margin-top:20px">
        Finish &amp; start my disputes
      </button>

      <p class="hint" style="text-align:center;margin-top:13px">
        🔒 Encrypted in transit and at rest, and used only to work your credit file.
      </p>
    </form>
  </div>
</main>

@endif

@endsection

@push('scripts')
<script>
(function () {
  var form = document.getElementById('finishForm');
  if (!form) return;

  var ssn = document.getElementById('ssn');
  if (ssn) {
    ssn.addEventListener('input', function () {
      var v = ssn.value.replace(/\D/g, '').slice(0, 9);
      if (v.length >= 6)      { ssn.value = v.slice(0,3) + '-' + v.slice(3,5) + '-' + v.slice(5); }
      else if (v.length >= 4) { ssn.value = v.slice(0,3) + '-' + v.slice(3); }
      else                    { ssn.value = v; }
    });
  }

  form.addEventListener('submit', function () {
    if (!form.checkValidity()) return;
    var btn = document.getElementById('finishBtn');
    setTimeout(function () {
      btn.disabled = true;
      btn.textContent = 'Uploading your documents…';
    }, 10);
  });
})();
</script>
@endpush
