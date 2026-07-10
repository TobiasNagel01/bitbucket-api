<?php

return [
    'base_url' => 'https://api.bitbucket.org/2.0/',

    'oauth' => [
        'client_id' => env('BITBUCKET_API_CLIENT_ID'),
        'client_secret' => env('BITBUCKET_API_CLIENT_SECRET'),
        'redirect' => env('BITBUCKET_API_REDIRECT_URI'),
    ],

    'scopes' => [

    ],
];