@extends('layouts.app')

@section('title', 'Affiliate Referral Program | Victorious Opportunities')
@section('description', 'Earn a one-time $100 commission for every new credit repair client you refer to Victorious Opportunities. Read the agreement and apply.')
@section('bodyClass', 'page-legal')

@section('content')
<style>
  .aff-hero { padding: 110px 0 36px; background: linear-gradient(180deg, var(--bg) 0%, var(--bg-2) 100%); border-bottom: 1px solid var(--line); }
  .aff-hero .container, .aff-body .container { max-width: 820px; }
  .aff-hero h1 { font-family: 'Instrument Serif', serif; font-size: clamp(40px, 6vw, 62px); line-height: 1.05; letter-spacing: -0.02em; color: var(--ink); margin: 14px 0 10px; }
  .aff-hero .kick { font-size: 13px; letter-spacing: .16em; text-transform: uppercase; color: var(--pink); font-weight: 600; }
  .aff-hero p { font-size: 17px; color: var(--ink-2); max-width: 680px; }
  .aff-body { padding: 48px 0 96px; background: var(--bg); }
  .aff-card { background: #fff; border: 1px solid var(--line); border-radius: 16px; padding: 28px 30px; margin-bottom: 28px; }
  .aff-card h2 { font-family: 'Instrument Serif', serif; font-size: 28px; margin: 0 0 14px; color: var(--ink); }
  .agreement { max-height: 460px; overflow: auto; padding-right: 8px; }
  .agreement h3 { font-size: 16px; font-weight: 700; margin: 22px 0 6px; color: var(--ink); }
  .agreement h3:first-child { margin-top: 0; }
  .agreement p, .agreement li { color: var(--ink-2); font-size: 15px; line-height: 1.7; }
  .agreement p { margin-bottom: 12px; } .agreement ul { padding-left: 20px; margin-bottom: 12px; }
  .agreement .disclosure { background: var(--pink-tint); border-left: 4px solid var(--pink); border-radius: 8px; padding: 12px 16px; font-style: italic; }
  .aff-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
  .aff-form label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--ink); }
  .aff-form input, .aff-form select { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; font: inherit; background: #fff; }
  .aff-form .full { grid-column: 1 / -1; }
  .aff-agree { display: flex; gap: 10px; align-items: flex-start; font-size: 15px; color: var(--ink); line-height: 1.5; }
  .aff-agree input { width: 20px; height: 20px; margin-top: 2px; flex: none; }
  .aff-err { color: #c0392b; font-size: 14px; margin-top: 8px; }
  .aff-ok { background: #f0fdf4; border: 1px solid #c9eccd; border-radius: 16px; padding: 28px 30px; }
  .aff-ok h2 { color: #157a3d; }
  .hp { position: absolute; left: -9999px; }
</style>

<section class="aff-hero">
  <div class="container">
    <span class="kick">Affiliate Referral Program</span>
    <h1>Earn $100 for every client you refer.</h1>
    <p>Know someone who needs credit repair? Share your link. When they enroll with Victorious Opportunities, you earn a one-time $100 commission.</p>
  </div>
</section>

<section class="aff-body">
  <div class="container">

    @if (session('applied'))
      <div class="aff-ok">
        <h2>Application received ✓</h2>
        <p>Thank you! We recorded your acceptance of the agreement and sent a confirmation to your email. Once we approve your application you'll receive your unique signup link.</p>
      </div>
    @elseif (! $ready)
      <div class="aff-card"><p>The affiliate program is opening soon — please check back shortly.</p></div>
    @else

    <div class="aff-card">
      <h2>Affiliate Referral Agreement</h2>
      <div class="agreement">
        <h3>1. Joining the program</h3>
        <p>Participation in the Victorious Opportunities LLC Affiliate Referral Program begins when we approve your application. Once approved, you’ll receive a unique signup link to share with prospective clients.</p>

        <h3>2. Earning your $100 commission</h3>
        <p>You earn a one-time $100 commission for each qualifying new client you refer. A referral qualifies when:</p>
        <ul>
          <li>You are an approved affiliate when you make the referral.</li>
          <li>A new client uses your unique signup link to complete enrollment in an eligible Victorious Opportunities credit repair service and signs the required client agreement.</li>
          <li>The applicable cancellation period has ended and the client is still enrolled when we verify the referral.</li>
          <li>We confirm that the completed enrollment was tracked to your unique signup link.</li>
        </ul>
        <p>Each qualifying client can generate one commission. Clicks, inquiries, consultation bookings, self-referrals, duplicate referrals, and fraudulent enrollments are ineligible.</p>

        <h3>3. Receiving your payout</h3>
        <p>Victorious Opportunities LLC will send your $100 commission within 3 business days after we verify the qualifying referral and have any required payout and tax information on file. We verify referrals after the client’s applicable cancellation period ends.</p>
        <p>Please keep your payout details current. You are responsible for reporting and paying any taxes that apply to your commissions. If you believe a referral or payout was recorded incorrectly, contact <a href="mailto:support@victorialovecredit.com">support@victorialovecredit.com</a> so we can review it.</p>

        <h3>4. Sharing your signup link</h3>
        <p>Clearly disclose that you may earn a commission whenever you recommend our services or share your affiliate link. Place the disclosure near the recommendation or link where people can easily see it. If you promote the service in a video, include the disclosure in the video itself.</p>
        <p>You may use this wording:</p>
        <p class="disclosure">I may earn $100 from Victorious Opportunities if you sign up for an eligible credit repair service through my link.</p>
        <p>Describe our services accurately and use company-approved marketing materials. Do not promise credit score increases, specific deletions, financing approval, or results within a set timeframe. Do not send spam, impersonate Victorious Opportunities, enroll clients on their behalf, or collect their payments, credit reports, or sensitive personal information. Direct prospective clients to your unique signup link so they can enroll themselves.</p>

        <h3>5. Program scope and affiliate relationship</h3>
        <p>This program pays commissions only for eligible Victorious Opportunities credit repair enrollments. Commissions are never tied to lender or mortgage referrals, real estate transactions, title services, or other settlement services. Referrals are ineligible where compensation is prohibited by law or applicable professional rules.</p>
        <p>You participate as an independent affiliate. This agreement does not create an employment relationship, legal partnership, or authority to act on behalf of Victorious Opportunities LLC.</p>

        <h3>6. Changes and ending participation</h3>
        <p>Either party may end participation at any time. Victorious Opportunities may suspend or remove affiliates for fraud, misleading promotions, or other violations of this agreement. Valid commissions already earned remain payable. Updates to this agreement apply to future referrals after notice.</p>

        <h3>7. Acceptance</h3>
        <p>By checking the acceptance box and submitting your application, you confirm that you have read and agree to this Affiliate Referral Agreement.</p>
      </div>
    </div>

    <div class="aff-card">
      <h2>Apply to join</h2>
      <form method="POST" action="{{ route('affiliate.apply.store') }}" class="aff-form">
        @csrf
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div><label for="a-name">Full name *</label><input id="a-name" name="name" required maxlength="150" value="{{ old('name') }}" autocomplete="name"></div>
        <div><label for="a-email">Email *</label><input id="a-email" type="email" name="email" required maxlength="150" value="{{ old('email') }}" autocomplete="email"></div>
        <div><label for="a-phone">Phone</label><input id="a-phone" name="phone" maxlength="30" value="{{ old('phone') }}" autocomplete="tel"></div>
        <div>
          <label for="a-method">How should we pay you? *</label>
          <select id="a-method" name="payout_method" required>
            @foreach (\App\Models\AffiliateApplication::METHODS as $k => $v)
              <option value="{{ $k }}" @selected(old('payout_method') === $k)>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="full"><label for="a-details">Payout details * <span style="font-weight:400;color:var(--ink-2)">(Zelle/PayPal/Cash App handle, or mailing address for a check)</span></label>
          <input id="a-details" name="payout_details" required maxlength="200" value="{{ old('payout_details') }}"></div>
        <div class="full">
          <label class="aff-agree">
            <input type="checkbox" name="agree" value="1" required @checked(old('agree'))>
            <span>I have read and agree to the Affiliate Referral Agreement above.</span>
          </label>
          @if ($errors->any())<div class="aff-err">{{ $errors->first() }}</div>@endif
        </div>
        <div class="full"><button type="submit" class="btn btn-pink">Submit application <span class="arr">→</span></button></div>
      </form>
    </div>
    @endif

  </div>
</section>
@endsection
