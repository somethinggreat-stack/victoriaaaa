<?php

// Credentials for the Burgundy × Victoria partnership dashboard.
//
// Read through config() rather than env() directly, so they survive
// `php artisan config:cache` — env() returns null in cached production,
// which is the same trap config/auth_reviewer.php exists to avoid.
//
// This login is deliberately NOT a Laravel Auth user. /victoria-admin is
// guarded by a plain Auth::check(), so a partnership user in the users table
// would silently unlock Victoria's entire admin area.
return [
    'email'    => env('PARTNERSHIP_EMAIL', ''),
    'password' => env('PARTNERSHIP_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Partnership plans
    |--------------------------------------------------------------------------
    |
    | Two prices run side by side and must not be confused:
    |
    |   legacy — the 56 clients transferring across from Burgundy's old book.
    |            They were promised $100, and that price stays live until the
    |            whole list has transitioned.
    |   new    — anyone signing up from today.
    |
    | The plan key is stamped onto the subscription, so a client's price is a
    | property of their subscription rather than of this file. Changing a price
    | here never re-prices somebody who already signed.
    |
    */
    'plans' => [
        'legacy' => [
            'key'        => 'burgundy-100',
            'label'      => 'Credit Restoration Program',
            'enrollment' => '100.00',
            'monthly'    => '100.00',
            'audience'   => 'Legacy clients transferring from Burgundy',
            'route'      => 'burgundy.checkout.show',
        ],
        'new' => [
            'key'        => 'burgundy-149',
            'label'      => 'Credit Restoration Program',
            'enrollment' => '149.00',
            'monthly'    => '149.00',
            'audience'   => 'New clients',
            'route'      => 'burgundy.checkout.new',
        ],
    ],

    'default_plan' => 'legacy',
];
