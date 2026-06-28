<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Order export (lokale Suno-macro)
    |--------------------------------------------------------------------------
    |
    | Bij elke betaalde aanvraag schrijft de server een Suno-klare JSON weg naar
    | onderstaande map. Die JSON bevat titel, stijl-tags en lyrics zodat een
    | lokale macro (bv. Keysmith) er automatisch een nummer mee kan genereren
    | op suno.com. Daarnaast kan een notificatiemail verstuurd worden.
    |
    | ORDERS_PATH mag absoluut zijn (bv. /Users/jij/Desktop/vim-orders of een
    | gesyncte map). Leeg = standaard storage/app/orders.
    |
    */

    'enabled' => env('ORDERS_EXPORT_ENABLED', true),

    'path' => env('ORDERS_PATH') ?: storage_path('app/orders'),

    // Stuur een notificatie (met JSON-bijlage) naar dit adres. Leeg = geen mail.
    'notify_email' => env('ORDERS_NOTIFY_EMAIL'),

    // Geheime sleutel waarmee de lokale macro openstaande aanvragen ophaalt en
    // bevestigt. Stuur mee als header "X-Export-Key". Leeg = endpoints geblokkeerd.
    'api_key' => env('ORDERS_API_KEY'),

];
