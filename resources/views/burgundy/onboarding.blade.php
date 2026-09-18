@extends('burgundy.layout')
@section('title', 'Client Onboarding — Final Step')
@section('description', 'Complete your onboarding so we can begin work on your credit file.')

@section('body')

@php
  $states = [
    'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California',
    'CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia',
    'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
    'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana',
    'ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota',
    'MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
    'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
    'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon',
    'PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota',
    'TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia',
    'WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming',
  ];
@endphp

@if (session('success'))

  <main>
    <div class="wrap narrow">
      <div class="card success-card">
        <div class="ico">✓</div>
        <h1>You're all set{{ session('client_name') ? ', ' . session('client_name') : '' }}.</h1>
        <p>Your onboarding is complete and your file has been handed to our processing team.</p>

        <div class="next">
          <strong>What happens next</strong>
          <ol>
            <li>Your credit reports are pulled and reviewed in detail.</li>
            <li>Your first dispute round goes out within 7 days.</li>
            <li>You'll hear from Burgundy with updates as results come in.</li>
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
    <span class="eyebrow">Step 3 of 3 · Onboarding</span>
    <h1>Last step — then we <em class="serif">get to work</em>.</h1>
    <p>We need a few details to pull your reports and start disputing. The sooner this is in, the sooner your first round goes out.</p>

    <div class="steps">
      <div class="step done"><span class="n">✓</span> Payment</div>
      <div class="step {{ $signedContract ? 'done' : '' }}"><span class="n">{{ $signedContract ? '✓' : '2' }}</span> Agreement</div>
      <div class="step on"><span class="n">3</span> Onboarding</div>
    </div>
  </div>
</section>

<main>
  <div class="wrap">

    @if ($errors->any())
      <div class="alert err">
        <strong>Please fix the following:</strong>
        <ul>
          @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
      </div>
    @endif

    @if (!$justPaid)
      <div class="alert warn">
        <strong>Already paid? You're in the right place.</strong>
        Fill this in using the same email and phone number you paid with, so we can match your enrollment.
      </div>
    @endif

    <form id="obForm" class="card" method="POST" action="{{ route('burgundy.onboarding.submit') }}" enctype="multipart/form-data" autocomplete="on" novalidate>
      @csrf

      <h3>Your legal name</h3>
      <div class="grid">
        <div class="field col-3 m-6">
          <label for="firstname">First name <span class="req">*</span></label>
          <input type="text" id="firstname" name="firstname" required autocomplete="given-name"
                 value="{{ old('firstname', $prefill['first_name'] ?? '') }}">
        </div>
        <div class="field col-3 m-6">
          <label for="middlename">Middle name</label>
          <input type="text" id="middlename" name="middlename" value="{{ old('middlename') }}" autocomplete="additional-name">
        </div>
        <div class="field col-3 m-6">
          <label for="lastname">Last name <span class="req">*</span></label>
          <input type="text" id="lastname" name="lastname" required autocomplete="family-name"
                 value="{{ old('lastname', $prefill['last_name'] ?? '') }}">
        </div>
        <div class="field col-3 m-6">
          <label for="suffix">Suffix</label>
          <select id="suffix" name="suffix">
            @foreach($suffixes as $s)
              <option value="{{ $s }}" @selected(old('suffix') === $s)>{{ $s }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <p class="hint" style="margin-top:-4px">Exactly as it appears on your government ID.</p>

      <h3>Contact</h3>
      <div class="grid">
        <div class="field col-6">
          <label for="email">Email <span class="req">*</span></label>
          <input type="email" id="email" name="email" required autocomplete="email"
                 value="{{ old('email', $prefill['email'] ?? '') }}">
        </div>
        <div class="field col-6">
          <label for="phone">Mobile phone <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone" required autocomplete="tel" placeholder="(555) 123-4567"
                 value="{{ old('phone', $prefill['phone'] ?? '') }}">
        </div>
      </div>

      <h3>Identity</h3>
      <div class="grid">
        <div class="field col-6">
          <label for="birth_date">Date of birth <span class="req">*</span></label>
          <input type="text" id="birth_date" name="birth_date" required placeholder="mm/dd/yyyy"
                 inputmode="numeric" maxlength="10" value="{{ old('birth_date') }}">
        </div>
        <div class="field col-6">
          <label for="ssn">Social Security Number <span class="req">*</span></label>
          <input type="text" id="ssn" name="ssn" required placeholder="123-45-6789"
                 inputmode="numeric" maxlength="11" value="{{ old('ssn') }}">
          <div class="hint">🔒 Encrypted at rest. Required by the bureaus to verify your identity.</div>
        </div>
      </div>

      <h3>Current address</h3>
      <div class="grid">
        <div class="field full">
          <label for="street_address">Street address <span class="req">*</span></label>
          <input type="text" id="street_address" name="street_address" required autocomplete="street-address"
                 value="{{ old('street_address', $prefill['address'] ?? '') }}">
        </div>
        <div class="field full">
          <label for="address_line2">Apartment, suite, unit</label>
          <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2') }}">
        </div>
        <div class="field col-6">
          <label for="city">City <span class="req">*</span></label>
          <input type="text" id="city" name="city" required autocomplete="address-level2"
                 value="{{ old('city', $prefill['city'] ?? '') }}">
        </div>
        <div class="field col-3 m-6">
          <label for="state">State <span class="req">*</span></label>
          <select id="state" name="state" required autocomplete="address-level1">
            <option value="">Select…</option>
            @foreach($states as $abbr => $name)
              <option value="{{ $abbr }}" @selected(old('state', $prefill['state'] ?? '') === $abbr)>{{ $name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field col-3 m-6">
          <label for="zip">Zip code <span class="req">*</span></label>
          <input type="text" id="zip" name="zip" required autocomplete="postal-code" maxlength="10"
                 value="{{ old('zip', $prefill['zip'] ?? '') }}">
        </div>
      </div>

      <h3>Credit monitoring login</h3>
      <p class="hint" style="margin:-6px 0 14px">
        We use this to pull your three-bureau reports and track progress. If you don't have an account yet,
        create one at <a href="https://www.myfreescorenow.com" target="_blank" rel="noopener">MyFreeScoreNow</a> first.
      </p>
      <div class="grid">
        <div class="field col-6">
          <label for="credit_monitoring_email">Monitoring login email <span class="req">*</span></label>
          <input type="email" id="credit_monitoring_email" name="credit_monitoring_email" required
                 autocomplete="off" value="{{ old('credit_monitoring_email') }}">
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

      <button type="submit" id="obBtn" class="btn wide" style="margin-top:20px">
        Submit &amp; start my disputes
      </button>

      <p class="hint" style="text-align:center;margin-top:13px">
        🔒 Your information is encrypted in transit and at rest, and used only to work your credit file.
      </p>
    </form>
  </div>
</main>

@endif

@endsection

@push('scripts')
<script>
(function () {
  var form = document.getElementById('obForm');
  if (!form) return;

  // Date of birth: auto-insert the slashes as they type.
  var dob = document.getElementById('birth_date');
  dob.addEventListener('input', function () {
    var v = dob.value.replace(/\D/g, '').slice(0, 8);
    if (v.length >= 5)      { dob.value = v.slice(0,2) + '/' + v.slice(2,4) + '/' + v.slice(4); }
    else if (v.length >= 3) { dob.value = v.slice(0,2) + '/' + v.slice(2); }
    else                    { dob.value = v; }
  });

  // SSN: same, with dashes.
  var ssn = document.getElementById('ssn');
  ssn.addEventListener('input', function () {
    var v = ssn.value.replace(/\D/g, '').slice(0, 9);
    if (v.length >= 6)      { ssn.value = v.slice(0,3) + '-' + v.slice(3,5) + '-' + v.slice(5); }
    else if (v.length >= 4) { ssn.value = v.slice(0,3) + '-' + v.slice(3); }
    else                    { ssn.value = v; }
  });

  // Uploads can take a while — make it clear the form is working, and make a
  // double submit impossible.
  form.addEventListener('submit', function () {
    var btn = document.getElementById('obBtn');
    if (!form.checkValidity()) return;
    setTimeout(function () {
      btn.disabled = true;
      btn.textContent = 'Uploading your documents…';
    }, 10);
  });
})();
</script>
@endpush
