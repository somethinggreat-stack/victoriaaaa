@php
  $stage = $client->stage();
  $tone = match($stage) {
    'Active', 'Onboarding Complete' => 'green',
    'Onboarding Pending', 'Paid'    => 'wine',
    'Payment Link Sent'             => 'blue',
    'Interested'                    => 'blue',
    'Cancelled', 'Not Interested'   => 'red',
    'Contacted'                     => 'amber',
    default                         => 'grey',
  };
@endphp
<span class="pill {{ $tone }}">{{ $stage }}</span>
