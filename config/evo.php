<?php
// config for Emsifa/Evo
return [
    'openapi' => [
        'info' => [
            'title' => 'API Documentation',
            'version' => '0.1.0',
        ],
        'servers' => [
            [
                'url' => env('APP_URL'),
                'description' => '',
                // See: https://swagger.io/specification/#server-variable-object
                // 'variables' => [
                //     'key' => [
                //         'description' => 'a key',
                //         'default' => 's3cr3t',
                //     ],
                // ],
            ],
        ],
    ],
];
