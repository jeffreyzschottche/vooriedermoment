<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Betaalprovider
    |--------------------------------------------------------------------------
    |
    | Voorlopig 'stub': betaling wordt direct als geslaagd gemarkeerd zonder
    | externe call. Later plug je hier Mollie of Stripe in door een nieuwe
    | PaymentProvider-implementatie te registreren in AppServiceProvider.
    |
    */

    'default' => env('PAYMENT_PROVIDER', 'stub'),

    'currency' => 'EUR',

];
