<?php
return [
    'api_url' => env('API_URL'),
    'frontend_url' => env('FRONTEND_URL', 'https://emole.gotechjsc.com'),
    'frontend_profile' => env('FRONTEND_URL', 'https://emole.gotechjsc.com'),
    'app_url' => env('APP_URL', "https://emole-api.t4code.xyz"),
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
        ],
        'genre' => [
            'key' => 'genre'
        ]
    ],
    'facebook' => [
        'app_id' => env('FACEBOOK_APP_ID', '555802755811269'),
        'secret_key' => env('APP_SECRET_ENV_NAME', '13569b94c57a73dfa972cd461954296d')
    ],
    'status_code' => [
        '500' => 500
    ]
];
