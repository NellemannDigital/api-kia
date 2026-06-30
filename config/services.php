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

    'power_automate' => [
        'test_drive_webhook_url' => env('POWER_AUTOMATE_TEST_DRIVE_WEBHOOK_URL'),
        'advisor_webhook_url' => env('POWER_AUTOMATE_ADVISOR_WEBHOOK_URL'),
        'profile_lookup_url' => env('POWER_AUTOMATE_PROFILE_LOOKUP_URL'),
    ],

    'meta_feed' => [
        'currency' => env('META_FEED_CURRENCY', 'DKK'),
        'location' => [
            'street_address' => env('META_FEED_STREET_ADDRESS', 'Trianglen 4'),
            'city' => env('META_FEED_CITY', 'Kolding'),
            'region' => env('META_FEED_REGION', 'Syddanmark'),
            'country' => env('META_FEED_COUNTRY', 'DK'),
            'postal_code' => env('META_FEED_POSTAL_CODE', '6000'),
        ],
    ],
];
