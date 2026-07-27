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

    // Stuur een notificatie (met JSON-bijlage) naar dit adres. Leeg = geen mail.
    'notify_email' => env('ORDERS_NOTIFY_EMAIL'),

    // Nieuwe naam met tijdelijke fallback voor bestaande Coolify-configuratie.
    'api_key' => env('AUTOMATION_API_KEY', env('ORDERS_API_KEY')),

    // Na deze tijd mag een gecrashte macro-order opnieuw geclaimd worden.
    'claim_ttl_minutes' => (int) env('AUTOMATION_CLAIM_TTL_MINUTES', 60),

    // Gebruik 'local' met een persistent Coolify-volume, of 's3' voor R2/S3.
    'storage_disk' => env('SAMPLES_STORAGE_DISK', 'local'),

    'sample_retention_days' => (int) env('SAMPLES_RETENTION_DAYS', 14),

];
