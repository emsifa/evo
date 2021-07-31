<?php
// config for Emsifa/Evo
return [
    'ignore_mock' => env('IGNORE_MOCK') === 'true',
    'openapi' => [
        /**
         * OpenAPI Info Object
         * See: https://swagger.io/specification/#info-object
         */
        'info' => [
            'title' => 'API Documentation',
            'version' => '0.1.0',
        ],
        /**
         * OpenAPI Server Object
         * See: https://swagger.io/specification/#server-object
         */
        'servers' => [
            [
                'url' => env('APP_URL'),
                'description' => '',
            ],
        ],
        /**
         * Security Scheme Objects
         * See: https://swagger.io/specification/#security-scheme-object
         */
        'security_schemes' => [],
    ],
];
