<?php
return [
    'api_url' => env('API_URL'),
    'frontend_url' => env('FRONTEND_URL', 'https://emole.gotechjsc.com/'),
    'app_url' => env('APP_URL'),
    'paging' => env('PAGING', 10),
    'activity_content' => [
        'category' => [
            'key' => 'category'
        ],
        'job' => [
            'key' => 'job'
        ],
        'sns' => [
            'key' => 'sns'
        ]
    ]
];
