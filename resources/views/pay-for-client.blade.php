@extends('layouts.app')

@section('title', 'Pay for a client | Victorious Opportunities')
@section('description', 'Pay a client\'s credit repair fee in full.')
@section('bodyClass', 'page-checkout')

@section('head_extra')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<style>
  .pfc { padding: 130px 0 100px; }
  .pfc .container { max-width: 620px; }
  .pfc-card { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 34px 32px; box-shadow: 0 30px 60px -30px rgba(20,16,14,.10); }
  .pfc h1 { font-family: 'Instrument Serif', serif; font-size: 36px; line-height: 1.1; margin: 0 0 8px; color: var(--ink); }
  .pfc p.lede { color: var(--ink-2); margin-bottom: 22px; }
  .pfc label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 6px; color: var(--ink); }
  .pfc input[type=text], .pfc input[type=email] { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; font: inherit; }
  .pfc .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .pfc .plan { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 16px; border: 1.5px solid var(--line); border-radius: 12px; margin-bottom: 10px; cursor: pointer; font-weight: 600; color: var(--ink); }
  .pfc .plan input { margin-right: 10px; }
  .pfc .plan:has(input:checked) { border-color: var(--pink); background: var(--pink-tint); }
  .pfc .plan .amt { font-variant-numeric: tabular-nums; }
  .pfc .err { color: #c0392b; font-size: 14px; margin-top: 10px; }
  .pfc .fine { font-size: 13px; color: var(--ink-3, #777); margin-top: 14px; }
  @media (max-width: 520px) { .pfc .row2 { grid-template-columns: 1fr; } }
</style>

<section class="pfc">
  <div class="container">
    <div class="pfc-card">
      <h1>Pay for a client</h1>
      <p class="lede">Hi {{ explode(' ', $affiliate->name)[0] }} — enter your client's details and pick their plan. You pay the full amount once; your client then gets their own contract and onboarding link by email.</p>

      <form method="POST" action="{{ route('pay-for-client.store', $affiliate->code) }}">
        @csrf
        <label style="margin-top:0">Plan</label>
        @foreach ($plans as $key => $p)
          <label class="plan">
            <span><input type="radio" name="plan" value="{{ $key }}" required @checked(old('plan', 'standard') === $key)>{{ $p['label'] }}</span>
            <span class="amt">${{ number_format($p['total'], 0) }} paid in full</span>
          </label>
        @endforeach

        <div class="row2">
          <div><label for="pc-fn">Client first name *</label><input id="pc-fn" type="text" name="first_name" required maxlength="80" value="{{ old('first_name') }}"></div>
          <div><label for="pc-ln">Client last name *</label><input id="pc-ln" type="text" name="last_name" required maxlength="80" value="{{ old('last_name') }}"></div>
        </div>
        <label for="pc-em">Client email * <span style="font-weight:400">(their contract is sent here)</span></label>
        <input id="pc-em" type="email" name="email" required maxlength="150" value="{{ old('email') }}">

        @if ($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

        <button type="submit" class="btn btn-pink" style="margin-top:22px;width:100%;justify-content:center;">Continue to payment <span class="arr">→</span></button>
        <p class="fine">Next you'll enter your own card on a secure page. The price is the normal price — no recurring charges.</p>
      </form>
    </div>
  </div>
</section>
@endsection
