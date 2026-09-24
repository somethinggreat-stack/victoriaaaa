{{--
  One signer. Used once for a normal agreement, twice for a joint one.
  $key   — 'primary' or 'cosigner' (drives the field names)
  $label — heading shown above the block
  $name  — name we expect, prefilled
  $done  — already signed, so show it as complete rather than asking again
  $when  — when they signed
--}}
@php
  $nameField = $key === 'cosigner' ? 'cosigner_full_name' : 'full_name';
  $sigField  = $key === 'cosigner' ? 'cosigner_signature_data' : 'signature_data';
@endphp

<div class="signer @if($done) signer-done @endif" data-key="{{ $key }}" data-done="{{ $done ? '1' : '0' }}">
  <div class="signer-head">
    <strong>{{ $label }}</strong>
    @if ($done)
      <span class="pill-done">✓ Signed {{ $when?->format('M j, Y') }}</span>
    @endif
  </div>

  @if ($done)
    <div class="signer-doneline">{{ $name }}</div>
  @else
    <div class="field">
      <label for="{{ $nameField }}">Full legal name <span class="req">*</span></label>
      <input type="text" id="{{ $nameField }}" name="{{ $nameField }}" value="{{ $name }}"
             autocomplete="off" class="js-signer-name">
    </div>

    <div class="field" style="margin-bottom:0">
      <label>Signature <span class="req">*</span></label>
      <div class="sigwrap">
        <canvas class="js-sigpad" data-target="{{ $sigField }}"></canvas>
        <div class="sigph">Sign here with your mouse or finger</div>
      </div>
      <div class="sigbar">
        <span class="hint" style="margin:0">Use your mouse, trackpad or finger.</span>
        <button type="button" class="js-clearsig">Clear</button>
      </div>
      <input type="hidden" name="{{ $sigField }}" class="js-sigdata">
    </div>
  @endif
</div>
