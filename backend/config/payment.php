<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Betaalprovider
    |--------------------------------------------------------------------------
    |
    | Gebruik 'stripe' in productie. De stub is uitsluitend bedoeld voor
    | lokale ontwikkeling en tests.
    |
    */

    'default' => env('PAYMENT_PROVIDER', 'stub'),

    'currency' => 'EUR',

    // Optionele 100%-kortingscode. Deze waarde blijft uitsluitend server-side.
    'discount_code' => env('SECRET_DISCOUNT_CODE'),

    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
