<?php

namespace Tests\Feature;

use App\Services\Lyrics\LyricsGenerator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeneralLyricsRouteTest extends TestCase
{
    public function test_it_generates_general_lyrics_with_deepseek(): void
    {
        config([
            'ai.providers.deepseek.key' => 'test-key',
            'ai.category_overrides.anders' => [
                'provider' => 'deepseek',
                'model' => 'deepseek-chat',
            ],
        ]);

        Http::fake([
            'api.deepseek.com/*' => Http::response([
                'choices' => [[
                    'message' => ['content' => implode("\n", [
                        '[Verse 1]',
                        'Pensioen na jaren bouwen',
                        'Iedereen kon op Henk vertrouwen',
                        'De helm mag nu voorgoed aan de kant',
                        'Vandaag heft de ploeg samen het glas in de hand',
                        '[Chorus]',
                        'Henk dit is jouw moment',
                        'Een feest waarop iedereen je kent',
                        'De bouwplaats zingt vandaag voor jou',
                        'Omdat de hele ploeg van je houdt',
                        '[Verse 2]',
                        'Elke ochtend stond de koffie klaar',
                        'Met sterke verhalen jaar na jaar',
                        'Nu wacht er tijd voor fiets en reis',
                        'Maar jouw naam blijft hier een bewijs',
                        '[Bridge]',
                        'Nog één keer klinkt jouw vaste lach',
                        'Zoals op elke vrijdagmiddagdag',
                        'We vergeten nooit wat jij hier deed',
                        'Omdat de hele ploeg dat weet',
                        '[Final Chorus]',
                        'Henk dit is jouw moment',
                        'Een feest waarop iedereen je kent',
                        'De bouwplaats zingt vandaag voor jou',
                        'Omdat de hele ploeg van je houdt',
                    ])],
                ]],
            ]),
        ]);

        $response = $this->postJson('/api/v1/lyrics/general', [
            'intake' => [
                'occasion' => 'Pensioen',
                'recipientName' => 'Henk',
                'fromName' => 'de bouwploeg',
                'tone' => 'feestelijk',
                'anecdotes' => 'Henk zette altijd als eerste koffie en fietst graag.',
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('category', 'anders')
            ->assertJsonPath('used_ai', true)
            ->assertJsonPath('sections.0.section', 'verse1')
            ->assertJsonPath('sections.4.section', 'chorus_final');

        $this->assertStringContainsString('Henk dit is jouw moment', $response->json('lyrics'));

        Http::assertSentCount(3);

        Http::assertSent(fn ($request) =>
            $request['model'] === 'deepseek-chat'
            && str_contains($request['messages'][0]['content'], 'Gelegenheid: Pensioen')
            && str_contains($request['messages'][0]['content'], 'Verbeteringsronde')
        );

        $requests = Http::recorded();
        $secondPrompt = $requests[1][0]['messages'][0]['content'];
        $this->assertStringContainsString('Verbeteringsronde 2 van 3', $secondPrompt);
        $this->assertStringContainsString('Henk dit is jouw moment', $secondPrompt);
    }

    public function test_general_moments_have_a_large_local_base_library(): void
    {
        config(['ai.providers.deepseek.key' => null]);

        $generator = app(LyricsGenerator::class);
        $preview = $generator->previewCategory('anders');

        $this->assertGreaterThanOrEqual(20, $preview['verse1']['count']);
        $this->assertGreaterThanOrEqual(20, $preview['verse2']['count']);
        $this->assertGreaterThanOrEqual(12, $preview['chorus']['count']);
        $this->assertGreaterThanOrEqual(12, $preview['bridge']['count']);

        $result = $generator->generateGeneral([
            'occasion' => 'Afscheid',
            'recipientName' => 'Sophie',
            'fromName' => 'het team',
            'anecdotes' => 'Sophie stond altijd als eerste klaar met koffie.',
        ]);

        $this->assertFalse($result['used_ai']);
        $this->assertCount(5, $result['sections']);
        $this->assertStringNotContainsString('{{', $result['lyrics']);
    }
}
