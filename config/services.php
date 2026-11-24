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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time notification server (Node.js / Socket.IO)
    |--------------------------------------------------------------------------
    | Full base URL for server.js. When empty, uses http://localhost:NOTIFICATION_PORT.
    | Same server: http://localhost:3050 or https://yourdomain.com:3050
    | Different server: https://notify.yourdomain.com
    */
    'notification_url' => env('NOTIFICATION_SERVER_URL')
        ? rtrim(env('NOTIFICATION_SERVER_URL'), '/')
        : ('http://localhost:' . (env('NOTIFICATION_PORT', 3050))),

];
