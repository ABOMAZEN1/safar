<?php

declare(strict_types=1);

return [
    'cors' => [
        'allowed_origins' => env('CORS_ALLOWED_ORIGINS', '*'),
        'allowed_methods' => env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS'),
        'allowed_headers' => env('CORS_ALLOWED_HEADERS', 'X-Requested-With,Content-Type,X-Token-Auth,Authorization'),
        'exposed_headers' => env('CORS_EXPOSED_HEADERS', ''),
        'max_age' => env('CORS_MAX_AGE', 86400),
        'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', true),
    ],

    'csp' => [
        'default-src' => ["'self'"],
        'img-src' => ["'self'", 'data:', 'https:'],
        'style-src' => ["'self'", "'unsafe-inline'"],
        'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
        'connect-src' => ["'self'", 'https:'],
    ],
];
