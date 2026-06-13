<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Muziekgeneratie
    |--------------------------------------------------------------------------
    |
    | De checkout-pipeline bouwt na betaling een definitieve lyrics-set en
    | daarna een muziekprompt. Met 'stub' wordt er nog geen externe API
    | aangeroepen; de prompt en referentie worden wel opgeslagen.
    |
    */

    'default' => env('MUSIC_PROVIDER', 'stub'),

    'providers' => [
        'stub' => [
            'driver' => 'stub',
        ],
    ],
];

