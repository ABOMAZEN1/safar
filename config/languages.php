<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application Supported Locales
    |--------------------------------------------------------------------------
    |
    | This array contains the supported locales for the application.
    | The first locale in the array is considered the default locale.
    |
    */
    'supported' => [
        'en', // English (default)
        'ar', // Arabic
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale to use when a specific locale is not requested
    | or when the requested locale is not supported.
    |
    */
    'default' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Locale Display Names
    |--------------------------------------------------------------------------
    |
    | Human-readable names for each supported locale.
    |
    */
    'names' => [
        'en' => 'English',
        'ar' => 'العربية',
    ],

    /*
    |--------------------------------------------------------------------------
    | RTL Languages
    |--------------------------------------------------------------------------
    |
    | Languages that are read from right to left.
    |
    */
    'rtl' => [
        'ar',
    ],
];
