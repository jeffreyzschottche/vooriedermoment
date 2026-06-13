<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\SongRequest;
use App\Services\Production\SongProductionPipeline;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('songs:produce {songRequestId}', function (SongProductionPipeline $production) {
    $songRequest = SongRequest::findOrFail((int) $this->argument('songRequestId'));

    $this->info('Stap 1/2: definitieve lyrics genereren...');
    $this->info('Stap 2/2: muziekprompt/muziekgeneratie starten...');

    $songRequest = $production->run($songRequest);

    $this->info('Productie afgerond met status: '.$songRequest->status);
    $this->line('Muziekreferentie: '.($songRequest->music_reference ?? 'geen'));
})->purpose('Genereer definitieve lyrics en muziek voor een song-aanvraag');
