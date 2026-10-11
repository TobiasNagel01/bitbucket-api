<?php

declare(strict_types=1);

return [
    'base_url' => env('BITBUCKET_API_BASE_URL', 'https://api.bitbucket.org/2.0'),

    'oauth' => [
        'client_id' => env('BITBUCKET_API_CLIENT_ID'),
        'client_secret' => env('BITBUCKET_API_CLIENT_SECRET'),
        'redirect' => env('BITBUCKET_API_REDIRECT_URI'),
    ],

    'scopes' => [

    ],
];
