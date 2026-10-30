<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_OAUTH_ID'),
        'client_secret' => env('GOOGLE_OAUTH_SECRET'),
        'redirect' => env('APP_URL').'/auth/google/verify',
    ],

    'paystack' => [
        'public' => env('PAYSTACK_PUBLIC'),
        'secret' => env('PAYSTACK_SECRET'),
        'plans' => [
            'individual' => 'PLN_cixdc3ryt807n8n',
            'institution' => 'PLN_8h2anxof44ecvb3',
            'single' => 'PLN_b5j5vrew4458cor'
        ]
    ]
];
