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

    // Monthly subscription the partnership sells. Single source of truth for
    // the checkout, the contract and the dashboard's revenue figures.
    'plan' => [
        'key'       => 'burgundy-100',
        'label'     => 'Credit Restoration Program',
        'enrollment' => '100.00',   // charged today
        'monthly'    => '100.00',   // charged every month thereafter
    ],
];
