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

    'flight_tracking' => [
        'provider' => env('FLIGHT_TRACKING_PROVIDER', 'opensky'),
        'api_key'  => env('FLIGHT_API_KEY'),
        'opensky'  => [
            'username' => env('OPENSKY_USERNAME'),
            'password' => env('OPENSKY_PASSWORD'),
            // Default bounding box for Indonesia flight information region (FIR Jakarta & Ujung Pandang)
            'bbox' => [
                'lamin' => (float) env('OPENSKY_LAMIN', -11.5),
                'lomin' => (float) env('OPENSKY_LOMIN', 94.5),
                'lamax' => (float) env('OPENSKY_LAMAX', 6.5),
                'lomax' => (float) env('OPENSKY_LOMAX', 141.5),
            ],
        ],
    ],


];
