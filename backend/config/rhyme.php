<?php

/*
|--------------------------------------------------------------------------
| Rijmcontrole via Van Dale rijmwoordenboek
|--------------------------------------------------------------------------
|
| Na het genereren van een AI-couplet checkt de LyricsGenerator of elk
| rijmpaar écht rijmt volgens rijmwoordenboek.vandale.nl (op uitspraak).
| Best-effort: kan Van Dale het niet bevestigen of is de site onbereikbaar,
| dan wordt het couplet niet afgekeurd.
|
*/

return [

    'enabled' => env('RHYME_CHECK_ENABLED', true),

    'base_url' => env('RHYME_BASE_URL', 'https://rijmwoordenboek.vandale.nl/rijm'),

    // Maximaal aantal rijmpagina's per woord ophalen (paginering /rijm/woord/2 ...).
    'max_pages' => env('RHYME_MAX_PAGES', 10),

    // Hoelang rijmlijsten cachen (rijm verandert niet).
    'cache_days' => env('RHYME_CACHE_DAYS', 30),

];
