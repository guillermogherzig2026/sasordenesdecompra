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

    'camera_recorder' => [
        'api_url' => env('CAMERA_RECORDER_API_URL', 'http://127.0.0.1:9997'),
        'hls_url' => env('CAMERA_RECORDER_HLS_URL', 'http://127.0.0.1:8888'),
        'config_path' => env('CAMERA_RECORDER_CONFIG_PATH', storage_path('app/camera-recorder/mediamtx.local.yml')),
        'ready_attempts' => (int) env('CAMERA_RECORDER_READY_ATTEMPTS', 20),
    ],

    'whatsapp' => [
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'app_secret' => env('META_APP_SECRET'),
        'graph_version' => env('META_GRAPH_VERSION'),
    ],

];
