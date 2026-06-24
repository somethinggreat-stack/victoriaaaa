@extends('layouts.app')

@section('title', 'Secure Payment — ' . $link['label'] . ' | Victoria Love')
@section('description', 'Secure 256-bit encrypted payment.')
@section('bodyClass', 'page-checkout')

@section('content')

<style>
.lc-wrap { padding: 130px 0 100px; }
.lc-wrap .container { max-width: 1080px; }
.lc-head { text-align: center; margin-bottom: 36px; }
.lc-head .eyebrow { margin-bottom: 14px; }
.lc-head h1 { font-size: clamp(1.9rem, 3.4vw, 2.6rem); margin-bottom: 10px; }
.lc-head p { font-size: 15.5px; color: var(--ink-2); max-width: 540px; margin: 0 auto; }
.lc-trust { display:flex; justify-content:center; gap:24px; flex-wrap:wrap; margin-top:20px; font-size:12px; color:var(--ink-3); text-transform:uppercase; letter-spacing:0.12em; font-weight:600; }
.lc-trust span { display:inline-flex; align-items:center; gap:7px; }
.lc-trust svg { width:13px; height:13px; color:var(--pink); }

.lc-grid { display:grid; grid-template-columns: 1fr 360px; gap:36px; align-items:start; }

.lc-form-wrap { background: var(--bg-3,#fff); border:1px solid var(--line); border-radius: var(--r-lg,20px); padding:34px; box-shadow:0 30px 60px -30px rgba(20,16,14,0.08); }
.lc-form-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; padding-bottom:20px; border-bottom:1px dashed var(--line-2); }
.lc-form-head h2 { font-size:21px; margin:0; }
.lc-secure { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:#0f8a4a; background:rgba(15,138,74,0.08); padding:6px 12px; border-radius:100px; }
.lc-secure svg { width:12px; height:12px; }

.lc-sec { margin-bottom:24px; }
.lc-sec-ttl { display:flex; align-items:center; gap:10px; font-size:11.5px; font-weight:700; letter-spacing:0.16em; text-transform:uppercase; color:var(--ink-3); margin-bottom:13px; }
.lc-sec-ttl::before { content:''; width:18px; height:1px; background:var(--pink); }

.lc-row { display:grid; grid-template-columns:1fr 1fr; gap:13px; }
.lc-card-row { display:grid; grid-template-columns:1fr 110px 110px; gap:13px; margin-top:13px; }
.lc-full { grid-column:1/-1; }
.lc-field { display:flex; flex-direction:column; gap:6px; }
.lc-field > span { font-size:12.5px; font-weight:600; color:var(--ink); }
.lc-field > span em { color:var(--pink); font-style:normal; }
.lc-input, .lc-select { width:100%; padding:13px 14px; border:1.5px solid var(--line-2); border-radius:var(--r-sm,10px); background:#fff; font-family:inherit; font-size:15px; color:var(--ink); outline:none; transition:border-color .2s, box-shadow .2s; }
.lc-input:focus, .lc-select:focus { border-color:var(--pink); box-shadow:0 0 0 4px rgba(230,49,121,0.10); }
.lc-input.is-invalid, .lc-select.is-invalid { border-color:#d93838; box-shadow:0 0 0 4px rgba(217,56,56,0.10); }
.lc-help { font-size:12px; color:var(--ink-3); margin-top:8px; }

.lc-agree { display:flex; gap:11px; align-items:flex-start; padding:13px 15px; background:var(--bg); border:1px solid var(--line); border-radius:var(--r-sm,10px); margin-bottom:10px; font-size:13px; line-height:1.5; color:var(--ink-2); cursor:pointer; }
.lc-agree input { margin-top:3px; width:18px; height:18px; flex:0 0 18px; accent-color:var(--pink); cursor:pointer; }
.lc-agree a { color:var(--pink); font-weight:600; text-decoration:underline; text-underline-offset:3px; }

.lc-pay { width:100%; margin-top:22px; background:var(--pink); color:#fff; border:none; padding:18px 24px; border-radius:100px; font-family:inherit; font-size:16px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 16px 36px -12px rgba(230,49,121,0.55); transition:background .25s, transform .25s; }
.lc-pay:hover:not(:disabled) { background:var(--ink); transform:translateY(-2px); }
.lc-pay:disabled { opacity:.55; cursor:not-allowed; }
.lc-fine { text-align:center; font-size:12px; color:var(--ink-3); margin-top:13px; }

.lc-alert { display:none; background:#fce8e8; border:1px solid #f1b5b5; color:#8a1f1f; padding:13px 16px; border-radius:var(--r-sm,10px); margin-bottom:18px; font-size:14px; line-height:1.5; }
.lc-alert.show { display:block; }

.lc-summary { position:sticky; top:110px; background:linear-gradient(180deg,#fff 0%,#fff8fb 100%); border:1px solid var(--line); border-radius:var(--r-lg,20px); padding:28px 26px; box-shadow:0 30px 60px -30px rgba(230,49,121,0.18); }
.lc-summary .eyebrow { margin:0 0 16px; }
.lc-summary .name { font-size:18px; font-weight:700; color:var(--ink); margin-bottom:4px; }
.lc-summary .tag { font-size:13px; color:var(--ink-2); margin-bottom:18px; }
.lc-sched { list-style:none; padding:0; margin:0 0 18px; }
.lc-sched li { display:flex; justify-content:space-between; align-items:baseline; gap:10px; padding:11px 0; border-bottom:1px solid var(--line); }
.lc-sched li:last-child { border-bottom:0; }
.lc-sched .when { font-size:13.5px; color:var(--ink); font-weight:600; }
.lc-sched .when small { display:block; font-size:11px; color:var(--ink-3); font-weight:500; }
.lc-sched .amt { font-size:15px; font-weight:700; color:var(--ink); white-space:nowrap; }
.lc-totals { background:#fff; border:1px solid var(--line); border-radius:var(--r-sm,10px); padding:13px 15px; margin-bottom:16px; }
.lc-totals .r { display:flex; justify-content:space-between; align-items:baseline; padding:5px 0; font-size:14px; color:var(--ink-2); }
.lc-totals .r.tot { border-top:1px solid var(--line); margin-top:5px; padding-top:11px; font-size:17px; font-weight:700; color:var(--ink); }
.lc-strust { display:flex; flex-direction:column; gap:8px; font-size:12px; color:var(--ink-3); }
.lc-strust span { display:inline-flex; align-items:center; gap:8px; }
.lc-strust svg { width:12px; height:12px; color:var(--pink); }

.lc-success { display:none; text-align:center; padding:48px 30px; background:var(--bg-3,#fff); border:1px solid var(--line); border-radius:var(--r-lg,20px); box-shadow:0 30px 60px -30px rgba(20,16,14,0.10); max-width:560px; margin:0 auto; }
.lc-success.show { display:block; }
.lc-success .ico { width:64px; height:64px; border-radius:50%; background:#f0fdf4; color:#157a3d; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 18px; }
.lc-success h2 { font-size:24px; margin-bottom:8px; }
.lc-success p { color:var(--ink-2); font-size:15px; }

@media (max-width:900px){ .lc-grid{ grid-template-columns:1fr; gap:22px; } .lc-summary{ position:static; order:-1; } .lc-row,.lc-card-row{ grid-template-columns:1fr; } }
</style>

<section class="lc-wrap">
  <div class="container">

    <div class="lc-head reveal" id="lcHead">
      <span class="eyebrow">Secure checkout · 256-bit encrypted</span>
      <h1>Complete your <em class="serif gradient-text">secure payment</em></h1>
      <p>Your details stay encrypted. Payments are processed securely through Authorize.Net.</p>
      <div class="lc-trust">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>256-BIT SSL</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z"/></svg>PCI COMPLIANT</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>AUTHORIZE.NET</span>
      </div>
    </div>

    <!-- SUCCESS PANEL -->
    <div class="lc-success" id="lcSuccess">
      <div class="ico">✓</div>
      <h2>Payment successful</h2>
      <p>Thank you, <span id="lcSuccessName">your payment</span> has been processed. A receipt has been sent to your email. You can close this page.</p>
    </div>

    <div class="lc-grid" id="lcGrid">

      <!-- FORM -->
      <div class="lc-form-wrap reveal">
        <div class="lc-form-head">
          <h2>Payment details</h2>
          <span class="lc-secure"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Secured</span>
        </div>

        <div class="lc-alert" id="lcAlert"></div>

        <form id="lcForm" novalidate autocomplete="on">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="lc-sec">
            <div class="lc-sec-ttl">01 · Your details</div>
            <div class="lc-row">
              <label class="lc-field"><span>First name <em>*</em></span><input class="lc-input" type="text" name="first_name" required maxlength="100" autocomplete="given-name"></label>
              <label class="lc-field"><span>Last name <em>*</em></span><input class="lc-input" type="text" name="last_name" required maxlength="100" autocomplete="family-name"></label>
              <label class="lc-field"><span>Email <em>*</em></span><input class="lc-input" type="email" name="email" required maxlength="150" autocomplete="email"></label>
              <label class="lc-field"><span>Phone <em>*</em></span><input class="lc-input" type="tel" name="phone" id="lcPhone" required maxlength="20" placeholder="(555) 123-4567" autocomplete="tel"></label>
            </div>
          </div>

          <div class="lc-sec">
            <div class="lc-sec-ttl">02 · Billing address</div>
            <div class="lc-row">
              <label class="lc-field lc-full"><span>Street address <em>*</em></span><input class="lc-input" type="text" name="address" required maxlength="255" autocomplete="street-address"></label>
              <label class="lc-field"><span>City <em>*</em></span><input class="lc-input" type="text" name="city" required maxlength="100" autocomplete="address-level2"></label>
              <label class="lc-field"><span>State <em>*</em></span>
                <select class="lc-select" name="state" required autocomplete="address-level1">
                  <option value="">Select…</option>
                  @php $states = ['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY']; @endphp
                  @foreach($states as $st)<option value="{{ $st }}">{{ $st }}</option>@endforeach
                </select>
              </label>
              <label class="lc-field"><span>ZIP <em>*</em></span><input class="lc-input" type="text" name="zip" required maxlength="10" inputmode="numeric" autocomplete="postal-code"></label>
            </div>
          </div>

          <div class="lc-sec">
            <div class="lc-sec-ttl">03 · Card details</div>
            <div class="lc-row">
              <label class="lc-field lc-full"><span>Name on card <em>*</em></span><input class="lc-input" type="text" name="cardName" required maxlength="150" autocomplete="cc-name"></label>
            </div>
            <div class="lc-card-row">
              <label class="lc-field"><span>Card number <em>*</em></span><input class="lc-input" type="text" id="lcCardNumber" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" maxlength="23"></label>
              <label class="lc-field"><span>Expiry <em>*</em></span><input class="lc-input" type="text" id="lcCardExp" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/YY" maxlength="7"></label>
              <label class="lc-field"><span>CVC <em>*</em></span><input class="lc-input" type="text" id="lcCardCvc" inputmode="numeric" autocomplete="cc-csc" placeholder="123" maxlength="4"></label>
            </div>
            <p class="lc-help">Your card is encrypted before it reaches us and processed securely by Authorize.Net.</p>
          </div>

          <div class="lc-sec">
            <div class="lc-sec-ttl">04 · Authorization</div>
            <label class="lc-agree">
              <input type="checkbox" name="agree_terms" value="1" required>
              <span>I authorize Victorious Opportunities to charge my card for the amount(s) and on the schedule shown, and agree to the <a href="{{ route('legal.terms-of-service') }}" target="_blank" rel="noopener">Terms of Service</a>.</span>
            </label>
            <label class="lc-agree">
              <input type="checkbox" name="agree_privacy" value="1" required>
              <span>I have read the <a href="{{ route('legal.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a> and consent to my data being used to process this payment.</span>
            </label>
          </div>

          <button type="submit" class="lc-pay" id="lcPay">
            <span class="label">Pay {{ '$' . number_format((float) $link['amount'], 2) }} now</span>
          </button>
          <p class="lc-fine"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px;color:#0f8a4a;vertical-align:-1px;margin-right:4px"><path d="M20 6L9 17l-5-5"/></svg>You'll see a confirmation as soon as your payment goes through.</p>
        </form>
      </div>

      <!-- SUMMARY -->
      <aside class="lc-summary reveal reveal-d2">
        <span class="eyebrow">Payment summary</span>
        <div class="name">{{ $link['label'] }}</div>
        <div class="tag">{{ $link['tagline'] }}</div>

        <ul class="lc-sched">
          @foreach($link['schedule'] as $row)
            <li>
              <span class="when">{{ $row['label'] }}<small>{{ $row['note'] }}</small></span>
              <span class="amt">{{ $row['amount'] }}</span>
            </li>
          @endforeach
        </ul>

        <div class="lc-totals">
          <div class="r"><span>Due today</span><span>{{ '$' . number_format((float) $link['amount'], 2) }}</span></div>
          <div class="r tot"><span>Total</span><span>{{ '$' . number_format((float) $link['total'], 2) }}</span></div>
        </div>

        <div class="lc-strust">
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Bank-grade 256-bit SSL encryption</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6z"/></svg>Processed by Authorize.Net — PCI Level 1</span>
        </div>
      </aside>

    </div>
  </div>
</section>

<script>
(function () {
  const form = document.getElementById('lcForm');
  const payBtn = document.getElementById('lcPay');
  const alertBox = document.getElementById('lcAlert');
  const phoneEl = document.getElementById('lcPhone');
  const cardNumberEl = document.getElementById('lcCardNumber');
  const cardExpEl = document.getElementById('lcCardExp');
  const cardCvcEl = document.getElementById('lcCardCvc');

  const showAlert = (t) => { alertBox.textContent = t; alertBox.classList.add('show'); alertBox.scrollIntoView({behavior:'smooth',block:'center'}); };
  const hideAlert = () => alertBox.classList.remove('show');
  const setInvalid = (el,on) => el.classList.toggle('is-invalid', !!on);

  if (phoneEl) phoneEl.addEventListener('input', () => {
    let d = phoneEl.value.replace(/\D+/g,''); if (d.length>10 && d.startsWith('1')) d=d.slice(1); d=d.slice(0,10);
    let o=''; if(!d.length)o=''; else if(d.length<4)o='('+d; else if(d.length<7)o='('+d.slice(0,3)+') '+d.slice(3); else o='('+d.slice(0,3)+') '+d.slice(3,6)+'-'+d.slice(6);
    phoneEl.value=o;
  });
  cardNumberEl.addEventListener('input', () => {
    let d = cardNumberEl.value.replace(/\D+/g,'').slice(0,19);
    cardNumberEl.value = (d.match(/.{1,4}/g)||[]).join(' ');
  });
  cardExpEl.addEventListener('input', () => {
    let d = cardExpEl.value.replace(/\D+/g,'').slice(0,4);
    cardExpEl.value = d.length>=3 ? d.slice(0,2)+'/'+d.slice(2) : d;
  });
  cardCvcEl.addEventListener('input', () => { cardCvcEl.value = cardCvcEl.value.replace(/\D+/g,'').slice(0,4); });

  const luhn = (num) => { const d=num.replace(/\D+/g,''); if(d.length<12)return false; let s=0,a=false; for(let i=d.length-1;i>=0;i--){let n=+d[i]; if(a){n*=2; if(n>9)n-=9;} s+=n; a=!a;} return s%10===0; };
  const expOk = (v) => { const m=v.match(/^(\d{2})\/(\d{2})$/); if(!m)return false; const mo=+m[1], yr=2000+ +m[2]; if(mo<1||mo>12)return false; return new Date(yr,mo,0,23,59,59) >= new Date(); };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    hideAlert();
    let firstInvalid = null;
    form.querySelectorAll('input[required], select[required]').forEach(el => {
      const ok = el.type==='checkbox' ? el.checked : (el.value||'').trim()!=='';
      setInvalid(el, !ok); if(!ok && !firstInvalid) firstInvalid = el;
    });
    const cardRaw = cardNumberEl.value.replace(/\s+/g,'');
    const cOk = luhn(cardRaw); setInvalid(cardNumberEl, !cOk); if(!cOk && !firstInvalid) firstInvalid = cardNumberEl;
    const eOk = expOk(cardExpEl.value); setInvalid(cardExpEl, !eOk); if(!eOk && !firstInvalid) firstInvalid = cardExpEl;
    const vOk = /^\d{3,4}$/.test(cardCvcEl.value); setInvalid(cardCvcEl, !vOk); if(!vOk && !firstInvalid) firstInvalid = cardCvcEl;
    if (firstInvalid) { firstInvalid.scrollIntoView({behavior:'smooth',block:'center'}); firstInvalid.focus({preventScroll:true}); if(!alertBox.classList.contains('show')) showAlert('Please complete all required fields with valid card details.'); return; }

    payBtn.disabled = true;
    const original = payBtn.innerHTML;
    payBtn.innerHTML = 'Processing…';

    const [mm,yy] = cardExpEl.value.split('/').map(s=>(s||'').trim());
    const fd = new FormData(form);
    fd.set('cardNumber', cardRaw);
    fd.set('expMonth', mm.padStart(2,'0'));
    fd.set('expYear', yy.length===2 ? '20'+yy : yy);
    fd.set('cardCode', cardCvcEl.value.trim());
    ['agree_terms','agree_privacy'].forEach(n => { const el=form.querySelector(`input[name="${n}"]`); if(el && !el.checked) fd.set(n,'0'); });

    fetch('{{ route('custom-pay.process') }}', {
      method:'POST',
      headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' },
      body: fd,
    })
    .then(r => r.json().then(j => ({ok:r.ok, body:j})))
    .then(({ok, body}) => {
      if (ok && body.success) {
        const fn = (form.querySelector('input[name="first_name"]').value || '').trim();
        document.getElementById('lcSuccessName').textContent = fn ? (fn + ', your payment') : 'your payment';
        document.getElementById('lcGrid').style.display = 'none';
        document.getElementById('lcHead').style.display = 'none';
        document.getElementById('lcSuccess').classList.add('show');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
      }
      showAlert(body.message || 'Payment could not be completed. Please try a different card or contact support.');
      payBtn.disabled = false; payBtn.innerHTML = original;
    })
    .catch(() => { showAlert('A network error stopped your payment. Please try again.'); payBtn.disabled=false; payBtn.innerHTML=original; });
  });
})();
</script>

@endsection
