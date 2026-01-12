<?php

return [
    'pim' => [
        'api_key' => env('PIM_API_KEY'),
        'api_url' => env('PIM_API_URL'),
    ],

    'azure' => [
        'web_app' => [
            'url' => env('AZURE_WEB_APP_URL'),
            'code' => env('AZURE_WEB_APP_CODE'),
        ],
    ],

    'bilinfo' => [
        'api_url' => env('BILINFO_API_URL'),
        'username' => env('BILINFO_USERNAME'),
        'password' => env('BILINFO_PASSWORD'),
    ],

    'dynamics' => [
        'api_url' => env('DYNAMICS_API_URL'),
        'tenant_id' => env('DYNAMICS_TENANT_ID'),
        'client_id' => env('DYNAMICS_CLIENT_ID'),
        'client_secret' => env('DYNAMICS_CLIENT_SECRET'),
        'resource' => env('DYNAMICS_RESOURCE'),
    ],
];
