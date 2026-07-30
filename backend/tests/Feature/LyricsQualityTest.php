<?php

namespace Tests\Feature;

use App\Services\Lyrics\LyricsGenerator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LyricsQualityTest extends TestCase
{
    public function test_category_fallback_always_fills_required_song_sections(): void
    {
        config(['ai.default' => 'null']);

        $result = app(LyricsGenerator::class)->generate('geslaagd', [
            'recipientName' => 'Lara',
            'studyLevel' => 'vwo',
            'nextStep' => 'een tussenjaar',
            'school' => 'Utrecht',
            'examStory' => 'de laatste wiskundetoets',
        ]);

        $this->assertFalse($result['used_ai']);
        $this->assertCount(6, $result['sections']);
        $this->assertSame(
            ['verse1', 'chorus', 'verse2', 'chorus', 'bridge', 'chorus_final'],
            array_column($result['sections'], 'section'),
        );

        foreach ($result['sections'] as $section) {
            $this->assertCount(4, $section['lines']);
        }

        $this->assertStringContainsString('[Verse 1]', $result['lyrics']);
        $this->assertStringContainsString('[Final Chorus]', $result['lyrics']);
        $this->assertStringNotContainsString('[Verse1]', $result['lyrics']);
        $this->assertStringNotContainsString('[Chorus final]', $result['lyrics']);
    }

    public function test_category_ai_rewrites_the_complete_song_and_uses_a_separate_critic(): void
    {
        config([
            'ai.default' => 'deepseek',
            'ai.providers.deepseek.key' => 'test-key',
            'ai.song_lyrics_rotations' => 2,
            'ai.lyrics_critic_enabled' => true,
            'ai.lyrics_min_words' => 100,
            'ai.lyrics_max_words' => 190,
        ]);

        Http::fakeSequence()
            ->push($this->deepSeekResponse($this->weakLyrics()))
            ->push($this->deepSeekResponse("AFKEUREN\n- De regels zijn te kort en te algemeen.\n- Gebruik de concrete examendetails."))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse('GOED'));

        $result = app(LyricsGenerator::class)->generate('geslaagd', [
            'recipientName' => 'Lara',
            'studyLevel' => 'vwo',
            'nextStep' => 'een tussenjaar',
            'school' => 'Utrecht',
            'examStory' => 'de laatste wiskundetoets',
            'anecdotes' => 'Voor wiskunde sliep Lara nauwelijks en haar samenvattingen lagen door de kamer.',
            'mustMention' => 'De trein naar het tussenjaar en haar fiets.',
            'avoid' => 'Geen regenboog of superhelden.',
        ]);

        $this->assertTrue($result['used_ai']);
        $this->assertCount(6, $result['sections']);
        $this->assertStringContainsString('Na maanden leren sluit Lara haar boeken', $result['lyrics']);
        $this->assertStringNotContainsString('Dit is jouw moment', $result['lyrics']);
        $this->assertGreaterThanOrEqual(100, str_word_count(strip_tags($result['lyrics'])));

        Http::assertSentCount(4);
        Http::assertSent(fn ($request) => str_contains(
            $request['messages'][0]['content'],
            'Je bent de strenge eindredacteur',
        ));

        $requests = Http::recorded();
        $this->assertStringContainsString(
            'De regels zijn te kort en te algemeen',
            $requests[2][0]['messages'][0]['content'],
        );
    }

    private function deepSeekResponse(string $content): array
    {
        return ['choices' => [['message' => ['content' => $content]]]];
    }

    private function weakLyrics(): string
    {
        return implode("\n", [
            '[Verse 1]',
            'Dit is jouw moment',
            'Vandaag is jouw dag',
            'Iedereen is heel blij',
            'We zingen nu samen',
            '[Chorus]',
            'Dit is jouw lied',
            'Speciaal voor jou',
            'Wij zingen het luid',
            'Omdat je dit verdient',
            '[Verse 2]',
            'De wereld ligt open',
            'Alles komt wel goed',
            'Blijf altijd jezelf',
            'Ga nu maar vooruit',
            '[Bridge]',
            'Recht uit ons hart',
            'Klinkt dit mooie lied',
            'Iedereen staat naast je',
            'Niemand houdt je tegen',
            '[Final Chorus]',
            'Dit is jouw lied',
            'Speciaal voor jou',
            'Wij zingen het luid',
            'Omdat je dit verdient',
        ]);
    }

    private function strongLyrics(): string
    {
        return implode("\n", [
            '[Verse 1]',
            'Na maanden leren sluit Lara haar boeken',
            'Op school in Utrecht bleef ze antwoorden zoeken',
            'De laatste toets vroeg alles van haar kracht',
            'Vandaag kwam eindelijk het bericht waarop ze wacht',
            '[Chorus]',
            'Lara zet de vlag maar buiten vandaag',
            'Jouw vwo diploma geeft een helder antwoord',
            'Van stille twijfel naar een luid applaus',
            'Nu klinkt jouw overwinningsakkoord door het hele huis',
            '[Verse 2]',
            'Voor wiskunde sliep je nauwelijks één uur',
            'Toch zat je vroeg weer klaar bij de schoolmuur',
            'Je samenvattingen lagen verspreid over de vloer',
            'Maar koppig en precies ging jij steeds door',
            '[Bridge]',
            'Straks wacht de trein naar jouw tussenjaar',
            'Een rugzak en je fiets staan al klaar',
            'Neem mee hoe vaak je eigen twijfel kantelde',
            'Toen jij na elke tegenslag opnieuw handelde',
            '[Final Chorus]',
            'Hang de tas maar hoog aan de vlag',
            'Lara jouw diploma draagt jouw handschrift vandaag',
            'Van zenuwachtige nachten naar vrijheid vooraan',
            'Nu mag jouw volgende hoofdstuk echt opengaan',
        ]);
    }
}
