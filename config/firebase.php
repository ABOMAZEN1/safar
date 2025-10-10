<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Server Key
    |--------------------------------------------------------------------------
    |
    | The Firebase server key for Cloud Messaging. You can find this in the
    | Firebase Console under Project Settings > Cloud Messaging > Server Key.
    |
    */
    'server_key' => env('FIREBASE_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID. You can find this in the Firebase Console
    | under Project Settings > General.
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account JSON file or credentials array.
    | You can download this from Firebase Console under Project Settings > Service Accounts.
    |
    */
    'service_account' => [
        'type' => 'service_account',
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL'),
    ],
];
