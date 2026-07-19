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

    'booking' => [
        // LeadConnector (GoHighLevel) booking widget URL for the free strategy call.
        'url' => env('BOOKING_URL', 'https://api.leadconnectorhq.com/widget/booking/cpNjALPPHd29F4FQS6AY'),
    ],

    'authorize_net' => [
        'environment'               => env('AUTHORIZE_NET_ENV', 'production'),
        'api_login_id'              => env('AUTHORIZE_NET_API_LOGIN_ID'),
        'transaction_key'           => env('AUTHORIZE_NET_TRANSACTION_KEY'),
        'client_key'                => env('AUTHORIZE_NET_PUBLIC_CLIENT_KEY'),
        'signature_key'             => env('AUTHORIZE_NET_SIGNATURE_KEY'),
        'webhook_enforce_signature' => env('AUTHORIZE_NET_WEBHOOK_ENFORCE_SIGNATURE', false),
    ],

    'google' => [
        // Google Apps Script web-app /exec URL that appends each paid order as a sheet row.
        'sheets_webhook_url' => env('GOOGLE_SHEETS_WEBHOOK_URL'),
    ],

    'ghl' => [
        // One GoHighLevel inbound webhook per form (resolved by the payload's `type`).
        'webhooks' => [
            'lead'          => env('GHL_WEBHOOK_LEAD'),
            'contact'       => env('GHL_WEBHOOK_CONTACT'),
            'funding'       => env('GHL_WEBHOOK_FUNDING'),
            'mentorship'    => env('GHL_WEBHOOK_MENTORSHIP'),
            'strategy_call' => env('GHL_WEBHOOK_STRATEGY'),
            'onboarding'    => env('GHL_WEBHOOK_ONBOARDING'),
        ],
        // Optional shared fallback used when a type has no specific webhook.
        'webhook_url' => env('GHL_WEBHOOK_URL'),
    ],

    'meta' => [
        'pixel_id'   => env('META_PIXEL_ID'),
        'capi_token' => env('META_CAPI_TOKEN'),
    ],

    // Apex Growth dashboard intake API — completed onboarding submissions are
    // forwarded here (multipart/form-data) so the client lands in Apex "New Clients".
    'apex' => [
        'enabled' => filter_var(env('APEX_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'url'     => env('APEX_API_URL', 'https://apexgrowthsolution.com/api/intake'),
        'key'     => env('APEX_API_KEY'),
    ],

    // "Become Your Own Boss" mentorship — everything a new mentee needs after
    // paying. Shown on the gated mentorship welcome page.
    'mentorship' => [
        'skool_url'    => env('MENTORSHIP_SKOOL_URL', 'https://www.skool.com/become-your-own-boss-1571/about?ref=9202b99c9e5a4a6f9a4d7fd72ff32c21'),
        'telegram_url' => env('MENTORSHIP_TELEGRAM_URL', 'https://t.me/+lRbocZA65Ow1MmRh'),

        'zoom_join_url'  => env('MENTORSHIP_ZOOM_URL', 'https://us06web.zoom.us/j/88052356202?pwd=95hpbfXl2KlnDEFARzy95TjGR06mxL.1'),
        'zoom_ics_url'   => env('MENTORSHIP_ZOOM_ICS', 'https://us06web.zoom.us/meeting/tZwtcuurqz0oGtYq6GVAE5vfwiEAcK3Sv5CU/ics?icsToken=DLOpeqImPEaC_I14sAAALAAAANUg_7SeaWWARBqWV5zrUdvuBS7rAfd1u4a_4j1Ooc4PJAqo7xDw8MO7NqqTReB4EPpXlCTPHwVcHp803TAwMDAwMQ&meetingMasterEventId=iowJ5Tp4Q8WAtMJp56ENZQ'),
        'zoom_meeting_id' => env('MENTORSHIP_ZOOM_ID', '880 5235 6202'),
        'zoom_passcode'   => env('MENTORSHIP_ZOOM_PASSCODE', '578899'),
        'zoom_schedule'   => env('MENTORSHIP_ZOOM_SCHEDULE', 'Every Monday · 7:30 PM Central'),
        'zoom_dial_in'    => env('MENTORSHIP_ZOOM_DIAL_IN', '+1 346 248 7799'),

        // Private link Victoria can send manually (bypasses the payment gate).
        // Anyone with this token can view the welcome page — keep it unlisted.
        'welcome_token' => env('MENTORSHIP_WELCOME_TOKEN', 'byob-w7k4qp92xm'),
    ],

];
