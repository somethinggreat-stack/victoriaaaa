<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Service Agreement — {{ $contract->signerName() }}</title>
<style>
  @page { margin: 34px 40px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1a1214; line-height: 1.55; }
  .head { border-bottom: 2px solid #7b1e3c; padding-bottom: 10px; margin-bottom: 16px; }
  .head h1 { margin: 0; font-size: 17px; color: #7b1e3c; }
  .head .sub { font-size: 10px; color: #6b6065; margin-top: 3px; }
  .terms { white-space: pre-wrap; font-size: 10px; line-height: 1.6; }
  .sig { margin-top: 24px; border-top: 1px solid #ddd; padding-top: 14px; }
  .sig img { max-height: 90px; border-bottom: 1px solid #333; }
  .meta { margin-top: 12px; font-size: 9px; color: #6b6065; }
  .meta td { padding: 2px 10px 2px 0; vertical-align: top; }
  .label { font-size: 9px; color: #6b6065; text-transform: uppercase; letter-spacing: .06em; }
</style>
</head>
<body>

<div class="head">
  <h1>Service Agreement</h1>
  <div class="sub">
    Victoria Love Credit ·
    {{ $contract->isSigned() ? 'Signed ' . optional($contract->signed_at)->format('F j, Y \a\t g:ia') : 'NOT YET SIGNED' }}
  </div>
</div>

<div class="terms">{{ $text }}</div>

<div class="sig">
  <div class="label">Client signature</div>
  @if ($contract->signature_data)
    <img src="{{ $contract->signature_data }}" alt="Signature">
  @else
    <div style="color:#b91c1c;font-weight:bold;margin-top:6px;">No signature on file — this agreement was never signed.</div>
  @endif
  <div style="margin-top:6px;font-size:11px;"><strong>{{ $contract->signerName() }}</strong></div>
</div>

{{-- Evidence that the signature is attributable, which is the point of keeping it. --}}
<table class="meta">
  <tr><td class="label">Signed at</td><td>{{ optional($contract->signed_at)->format('Y-m-d H:i:s') ?: '—' }}</td></tr>
  <tr><td class="label">Invoice</td><td>{{ $contract->invoice_number ?: '—' }}</td></tr>
  <tr><td class="label">Email</td><td>{{ $contract->email ?: '—' }}</td></tr>
  <tr><td class="label">IP address</td><td>{{ $contract->ip_address ?: '—' }}</td></tr>
  <tr><td class="label">Device</td><td>{{ \Illuminate\Support\Str::limit($contract->user_agent, 110) ?: '—' }}</td></tr>
  <tr><td class="label">Reference</td><td>{{ $contract->terms_version }} · #{{ $contract->id }}</td></tr>
</table>

</body>
</html>
