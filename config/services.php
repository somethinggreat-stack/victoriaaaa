<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'crc' => [
        'api_key'    => env('CRC_API_KEY'),
        'secret_key' => env('CRC_SECRET_KEY'),
        'base_url'   => env('CRC_BASE_URL', 'https://app.creditrepaircloud.com'),
        // Comma-separated team member names to assign the new client to (optional)
        'assigned_to' => env('CRC_ASSIGNED_TO'),
        // Agreement name to apply to the new client (optional, only if portal access on)
        'agreement'   => env('CRC_AGREEMENT'),
    ],

    'calendly' => [
        // Full URL to the Calendly event type, e.g. https://calendly.com/yourname/15min
        'url' => env('CALENDLY_URL', 'https://calendly.com/your-handle/15min'),
    ],

    'authorize_net' => [
        'environment'               => env('AUTHORIZE_NET_ENV', 'production'),
        'api_login_id'              => env('AUTHORIZE_NET_API_LOGIN_ID'),
        'transaction_key'           => env('AUTHORIZE_NET_TRANSACTION_KEY'),
        'client_key'                => env('AUTHORIZE_NET_PUBLIC_CLIENT_KEY'),
        'signature_key'             => env('AUTHORIZE_NET_SIGNATURE_KEY'),
        'webhook_enforce_signature' => env('AUTHORIZE_NET_WEBHOOK_ENFORCE_SIGNATURE', false),
    ],

    'meta' => [
        'pixel_id'   => env('META_PIXEL_ID'),
        'capi_token' => env('META_CAPI_TOKEN'),
    ],

];
