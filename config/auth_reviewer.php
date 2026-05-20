<?php

// Reviewer credentials for the Authorize.Net underwriting review (and similar
// read-only audit logins). Values are pulled from .env here so they survive
// `php artisan config:cache` — env() called from controllers does NOT work
// after config caching, so we must go through config().
return [
    'email'    => env('REVIEWER_EMAIL', ''),
    'password' => env('REVIEWER_PASSWORD', ''),
];
