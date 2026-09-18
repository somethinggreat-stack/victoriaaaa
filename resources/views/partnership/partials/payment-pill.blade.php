@php
  $ps = $client->paymentStatus();
  $tone = match($ps) {
    'paid'      => 'green',
    'failed'    => 'red',
    'cancelled' => 'red',
    default     => 'grey',
  };
  $label = match($ps) {
    'paid'      => 'Paid',
    'failed'    => 'Failed',
    'cancelled' => 'Cancelled',
    'unpaid'    => 'Unpaid',
    default     => ucfirst($ps),
  };
@endphp
<span class="pill {{ $tone }}">{{ $label }}</span>
