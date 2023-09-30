<?php

return [
    'prefered_notification_channels' => 'mail',
    'token_lifespan' => 30,
    'site_name' => env('APP_NAME'),
    'collect_user_data' => true,
    'system' => [
        'ipinfo' => [
            'access_token' => env('IPINFO_ACCESS_TOKEN'),
        ]
    ],
];