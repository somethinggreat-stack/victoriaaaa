@extends('admin.layout')
@section('title', 'Contract')

@section('content')
<div class="admin-header">
  <div>
    <h1>{{ $contract->signerName() }}</h1>
    <div class="sub">
      #{{ str_pad($contract->id, 4, '0', STR_PAD_LEFT) }} ·
      {{ $contract->isSigned() ? 'signed ' . optional($contract->signed_at)->format('M j, Y \a\t g:ia') : 'awaiting signature' }}
    </div>
  </div>
  <div style="display:flex;gap:10px;">
    <a class="adm-btn ghost" href="{{ route('admin.contracts') }}">← All contracts</a>
    <a class="adm-btn" href="{{ route('admin.contracts.pdf', $contract) }}">Download PDF</a>
  </div>
</div>

@if (! $contract->fullySigned())
  <div class="adm-card" style="margin-bottom:16px; border-color:#ffd8a8; background:#fffaf2;">
    <div class="adm-card-head"><h2>{{ $contract->isPartiallySigned() ? 'Waiting on the second signature' : 'Paid, but not signed' }}</h2></div>
    <p style="font-size:13.5px; color:var(--ink-2); margin-bottom:14px;">
      @if ($contract->isPartiallySigned())
        <strong>{{ $contract->awaitingSignatureFrom() }}</strong> still needs to sign. The same link below reopens the
        agreement showing the signature already given, and asks only for the missing one.
      @else
      This client was charged {{ $contract->priceSummary() }} but closed the page before signing.
      @endif
      Send them this link — it reopens the same agreement, already priced from their payment.
      It is valid for 30 days; reload this page for a fresh one.
    </p>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="text" id="sigLink" value="{{ $signingUrl }}" readonly onclick="this.select()"
             style="flex:1; min-width:240px; padding:10px 12px; border:1.5px solid #ffd8a8; border-radius:9px; font-family:ui-monospace,monospace; font-size:13px; background:#fff;">
      <button type="button" class="adm-btn"
              onclick="navigator.clipboard.writeText(document.getElementById('sigLink').value).then(()=>{this.textContent='✓ Copied';setTimeout(()=>this.textContent='Copy link',1600);})">Copy link</button>
      @if ($contract->email)
        @php
          $subject = rawurlencode('Please sign your service agreement');
          $body = rawurlencode("Hi " . \Illuminate\Support\Str::before($contract->signerName(), ' ') . ",\n\nThank you for your payment. We just need your signature on the service agreement to get started - it takes under a minute:\n\n" . $signingUrl . "\n\nThank you!\n\n- Victoria Love");
        @endphp
        <a class="adm-btn ghost" href="mailto:{{ $contract->email }}?subject={{ $subject }}&body={{ $body }}">Email client</a>
      @endif
    </div>
  </div>
@endif

<!-- WHAT THEY AGREED TO -->
<div class="adm-card" style="margin-bottom:16px;">
  <div class="adm-card-head"><h2>Terms</h2></div>
  <div class="detail-grid">
    <div class="lab">Service</div>        <div class="val">{{ $contract->plan_label }}</div>
    <div class="lab">Charged today</div>  <div class="val">${{ number_format((float) $contract->deposit_amount, 2) }}</div>
    <div class="lab">Recurring</div>
    <div class="val">
      @if ($contract->installment_amount && (float) $contract->installment_amount > 0)
        ${{ number_format((float) $contract->installment_amount, 2) }}/mo
        @if ($contract->installment_count) × {{ $contract->installment_count }} @else (until cancelled) @endif
      @else — @endif
    </div>
    <div class="lab">Invoice</div>        <div class="val mono">{{ $contract->invoice_number ?: '—' }}</div>
    <div class="lab">Email</div>          <div class="val">{{ $contract->email ?: '—' }}</div>
    <div class="lab">Partner</div>        <div class="val">{{ ucfirst($contract->partner) }}</div>
    <div class="lab">Terms version</div>  <div class="val mono">{{ $contract->terms_version }}</div>
  </div>
</div>

@if ($contract->signature_data || $contract->cosigner_signature_data)
  <div class="adm-card" style="margin-bottom:16px;">
    <div class="adm-card-head"><h2>{{ $contract->requires_cosigner ? 'Signatures' : 'Signature' }}</h2></div>

    @if ($contract->signature_data)
      <img src="{{ $contract->signature_data }}" alt="Signature"
           style="max-height:130px;border-bottom:2px solid var(--ink);padding-bottom:6px;">
      <div style="margin-top:10px;font-weight:700;">{{ $contract->full_name }}</div>
      <div class="detail-grid" style="margin-top:12px;">
        <div class="lab">Signed at</div>  <div class="val">{{ optional($contract->signed_at)->format('Y-m-d H:i:s') }}</div>
        <div class="lab">IP address</div> <div class="val mono">{{ $contract->ip_address ?: '—' }}</div>
      </div>
    @elseif ($contract->requires_cosigner)
      <div style="color:#991b1b;font-weight:700;">{{ $contract->client_name ?: 'First client' }} has not signed yet.</div>
    @endif

    @if ($contract->requires_cosigner)
      <hr style="border:0;border-top:1px solid var(--line);margin:18px 0;">
      @if ($contract->cosigner_signature_data)
        <img src="{{ $contract->cosigner_signature_data }}" alt="Signature"
             style="max-height:130px;border-bottom:2px solid var(--ink);padding-bottom:6px;">
        <div style="margin-top:10px;font-weight:700;">{{ $contract->cosigner_full_name }}</div>
        <div class="detail-grid" style="margin-top:12px;">
          <div class="lab">Signed at</div>  <div class="val">{{ optional($contract->cosigner_signed_at)->format('Y-m-d H:i:s') }}</div>
          <div class="lab">IP address</div> <div class="val mono">{{ $contract->cosigner_ip_address ?: '—' }}</div>
        </div>
      @else
        <div style="color:#991b1b;font-weight:700;">{{ $contract->cosigner_name ?: 'Second client' }} has not signed yet.</div>
      @endif
    @endif
  </div>
@endif

<!-- THE DOCUMENT AS SHOWN -->
<div class="adm-card">
  <div class="adm-card-head"><h2>Agreement as signed</h2></div>
  <p style="font-size:12px;color:var(--ink-3);margin-bottom:12px;">
    Stored verbatim at the moment of signing, so it reproduces exactly even after the wording changes.
  </p>
  <div class="mono" style="white-space:pre-wrap;background:var(--bg-2);padding:16px;border-radius:9px;font-size:12px;line-height:1.65;max-height:520px;overflow:auto;">{{ $text }}</div>
</div>
@endsection
