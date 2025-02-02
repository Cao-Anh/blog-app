<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'), // Default to Pusher
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true, // Use TLS for secure connections
                'encrypted' => true, // Ensure encryption is enabled
            ],
        ],
        'null' => [
            'driver' => 'null', // Fallback driver
        ],
    ],
];