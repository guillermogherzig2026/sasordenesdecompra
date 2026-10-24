<?php

// Fusiona esta entrada con el arreglo retornado por config/services.php.
return [
    'whatsapp' => [
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'app_secret' => env('META_APP_SECRET'),
        'graph_version' => env('META_GRAPH_VERSION', 'v22.0'),
    ],
];
