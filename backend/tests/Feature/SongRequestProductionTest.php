<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongRequestProductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_generates_final_lyrics_and_music_prompt(): void
    {
        $create = $this->postJson('/api/v1/song-requests', [
            'category' => 'bouwbedrijven',
            'categoryTitle' => 'Bouwbedrijven',
            'intake' => [
                'companyName' => 'Bouwbedrijf Jansen',
                'contactName' => 'Jan',
                'slogan' => 'Bouwen op vertrouwen',
                'tone' => 'Stoer & energiek',
                'musicStyle' => 'Rock / anthem',
                'anecdotes' => 'Ze bouwen scholen en zingen altijd op vrijdag.',
                'mustMention' => 'De vrijdagmiddagborrel',
                'avoid' => 'Geen flauwe grappen',
                'email' => 'jan@example.com',
            ],
        ]);

        $create->assertCreated();
        $id = $create->json('data.id');

        $checkout = $this->postJson("/api/v1/song-requests/{$id}/checkout");

        $checkout
            ->assertOk()
            ->assertJsonPath('data.status', 'music_prompt_ready')
            ->assertJsonPath('data.music_reference', fn (?string $value) => str_starts_with($value ?? '', 'stub-music-'));

        $this->assertDatabaseHas('song_requests', [
            'id' => $id,
            'status' => 'music_prompt_ready',
            'category' => 'bouwbedrijven',
        ]);

        $this->assertNotEmpty($checkout->json('data.production_steps'));
    }
}

