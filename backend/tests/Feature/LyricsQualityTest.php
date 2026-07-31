<?php

namespace Tests\Feature;

use App\Services\Lyrics\LyricsGenerator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LyricsQualityTest extends TestCase
{
    public function test_every_category_turns_all_lyric_relevant_form_data_into_required_facts(): void
    {
        $generator = new class extends LyricsGenerator
        {
            public function requiredFacts(string $category, array $intake): array
            {
                return $this->intakeCoverageRequirements(
                    $this->buildContext($category, $intake),
                    $intake,
                );
            }

            public function missingFacts(string $category, array $intake, string $lyrics): array
            {
                return $this->missingIntakeRequirements(
                    $lyrics,
                    $this->buildContext($category, $intake),
                    $intake,
                );
            }
        };

        $categoryDetails = [
            'rijbewijs' => [
                'instructor' => 'Rijschool Orion',
                'attempts' => 'derde poging',
                'firstDrive' => 'meteen naar oma in Delft',
                'drivingMoment' => 'examinator floot bij het parkeren',
            ],
            'geslaagd' => [
                'school' => 'Stedelijk Lyceum',
                'studyLevel' => 'VWO',
                'nextStep' => 'biologie studeren in Leiden',
                'examStory' => 'nachtwerk voor wiskunde',
            ],
            'eigen-huis' => [
                'place' => 'Utrecht Oost',
                'firstHome' => 'Ja de eerste',
                'homeType' => 'jaren dertig kluswoning',
                'favoriteRoom' => 'de zonnige daktuin',
            ],
            'vaderdag' => [
                'nickname' => 'Pap Beer',
                'hobby' => 'barbecueën met houtskool',
                'dadQuote' => 'niet lullen maar poetsen',
                'thanksFor' => 'alle nachtelijke autoritten',
            ],
            'moederdag' => [
                'nickname' => 'Mams',
                'memory' => 'zondagse kaneelpannenkoeken',
                'momTrait' => 'chaotisch gezellig appen',
                'thanksFor' => 'luisteren zonder oordeel',
            ],
            'kind-geboren' => [
                'babyName' => 'Liv',
                'birthDate' => '2026-06-14',
                'parents' => 'Sam en Noor',
                'birthDetails' => 'grote broer Mees woog de knuffels',
            ],
            'verjaardag' => [
                'age' => 'vijftig',
                'party' => 'surprise in de oude kroeg',
                'personality' => 'familiemens en druktemaker',
                'partyMoment' => 'binnenkomst met sterretjes',
            ],
            'voetbalclubs' => [
                'teamType' => 'kampioenswedstrijd',
                'clubName' => 'VV Orion JO17',
                'colors' => 'groen wit',
                'players' => 'keeper Sem, trainer Mo',
                'clubCulture' => 'slechte warming-up en sterke derde helft',
                'chant' => 'Orion vooruit',
            ],
            'bouwbedrijven' => [
                'companyName' => 'Bouwbedrijf Atlas',
                'contactName' => 'Jeff',
                'discipline' => 'renovatie',
                'foundingYear' => '1998',
                'slogan' => 'bouwen op vertrouwen',
                'occasion' => 'jubileum',
            ],
            'anders' => [
                'occasion' => 'pensioen',
            ],
        ];

        foreach ($categoryDetails as $category => $details) {
            $intake = $details + [
                'recipientName' => 'Robin',
                'fromName' => 'familie Nova',
                'additionalRecipientNames' => 'Saar (zus), Mo (vriend)',
                'additionalSenderNames' => 'Team West (collega’s)',
                'anecdotes' => "oude dubbele waarde die niet apart mag meetellen\nnog een dubbele waarde",
                'anecdotesItems' => [
                    'de lekke fiets bij station Zuid',
                    'elke vrijdag appeltaart bakken',
                ],
                'mustMention' => 'oude dubbele must-have',
                'mustMentionItems' => [
                    'de rode koffiemok',
                    'vakantie naar Texel',
                ],
                'tone' => 'warm en grappig',
                'vocals' => 'duet',
                'musicStyle' => 'Nederlandstalige pop',
                'tempo' => 'medium tempo',
                'avoid' => 'superhelden',
                'email' => 'niet-in-de-lyrics@example.com',
            ];

            $facts = $generator->requiredFacts($category, $intake);
            $values = array_column($facts, 'value');

            foreach ($details as $value) {
                foreach (preg_split('/\s*,\s*/u', $value) ?: [] as $part) {
                    $this->assertContains($part, $values, "{$category} mist formulierwaarde {$part}");
                }
            }

            foreach ([
                'Robin',
                'familie Nova',
                'Saar (zus)',
                'Mo (vriend)',
                'Team West (collega’s)',
                'de lekke fiets bij station Zuid',
                'elke vrijdag appeltaart bakken',
                'de rode koffiemok',
                'vakantie naar Texel',
            ] as $value) {
                $this->assertContains($value, $values, "{$category} mist universele formulierwaarde {$value}");
            }

            $this->assertNotContains('niet-in-de-lyrics@example.com', $values);
            $this->assertNotContains('oude dubbele waarde die niet apart mag meetellen', $values);
            $this->assertNotContains('oude dubbele must-have', $values);
            $this->assertCount(count($facts), $generator->missingFacts($category, $intake, ''));
            $this->assertSame([], $generator->missingFacts($category, $intake, implode(' ', $values)));
        }

        $strictIntake = [
            'recipientName' => 'Robin',
            'fromName' => 'familie Nova',
            'additionalRecipientNames' => 'Saar (zus), Mo (vriend)',
            'mustMentionItems' => ['de rode koffiemok'],
        ];
        $missing = $generator->missingFacts(
            'verjaardag',
            $strictIntake,
            'Robin krijgt dit lied van de familie, samen met zijn zus en vriend bij een koffiemok.',
        );
        $missingValues = array_column($missing, 'value');

        $this->assertContains('familie Nova', $missingValues);
        $this->assertContains('Saar (zus)', $missingValues);
        $this->assertContains('Mo (vriend)', $missingValues);
        $this->assertContains('de rode koffiemok', $missingValues);
    }

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
            'ai.song_lyrics_rotations' => 4,
            'ai.lyrics_critic_enabled' => true,
            'ai.lyrics_min_words' => 100,
            'ai.lyrics_max_words' => 190,
        ]);

        Http::fakeSequence()
            ->push($this->deepSeekResponse($this->weakLyrics()))
            ->push($this->deepSeekResponse("AFKEUREN\n- De regels zijn te kort en te algemeen.\n- Gebruik de concrete examendetails."))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse('GOED'))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse('GOED'))
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

        Http::assertSentCount(8);
        Http::assertSent(fn ($request) => str_contains(
            $request['messages'][0]['content'],
            'Je bent de strenge eindredacteur',
        ));

        $requests = Http::recorded();
        $this->assertStringContainsString(
            'De regels zijn te kort en te algemeen',
            $requests[2][0]['messages'][0]['content'],
        );
        $this->assertStringContainsString(
            '[F',
            $requests[0][0]['messages'][0]['content'],
        );
        $this->assertStringContainsString(
            'Verplichte vermelding',
            $requests[0][0]['messages'][0]['content'],
        );
    }

    public function test_missing_sender_and_extra_person_trigger_dedicated_coverage_repair(): void
    {
        config([
            'ai.default' => 'deepseek',
            'ai.providers.deepseek.key' => 'test-key',
            'ai.song_lyrics_rotations' => 4,
            'ai.lyrics_critic_enabled' => false,
            'ai.lyrics_coverage_repair_attempts' => 2,
            'ai.lyrics_min_words' => 100,
            'ai.lyrics_max_words' => 190,
        ]);

        Http::fakeSequence()
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse($this->strongLyrics()))
            ->push($this->deepSeekResponse($this->strongLyricsWithSenderAndExtraPerson()));

        $result = app(LyricsGenerator::class)->generate('geslaagd', [
            'recipientName' => 'Lara',
            'fromName' => 'familie Nova',
            'additionalRecipientNames' => 'Saar (zus)',
            'studyLevel' => 'vwo',
            'nextStep' => 'een tussenjaar',
            'school' => 'Utrecht',
            'examStory' => 'de laatste wiskundetoets',
            'anecdotes' => 'Voor wiskunde sliep Lara nauwelijks en haar samenvattingen lagen door de kamer.',
            'mustMention' => 'De trein naar het tussenjaar en haar fiets.',
        ]);

        $this->assertTrue($result['used_ai']);
        $this->assertStringContainsString('Familie Nova', $result['lyrics']);
        $this->assertStringContainsString('zus Saar', $result['lyrics']);
        Http::assertSentCount(5);

        $requests = Http::recorded();
        $repairPrompt = $requests[4][0]['messages'][0]['content'];
        $this->assertStringContainsString('laatste lyrics-reparateur', $repairPrompt);
        $this->assertStringContainsString('familie Nova', $repairPrompt);
        $this->assertStringContainsString('Saar (zus)', $repairPrompt);
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

    private function strongLyricsWithSenderAndExtraPerson(): string
    {
        return str_replace(
            [
                'Lara zet de vlag maar buiten vandaag',
                'Neem mee hoe vaak je eigen twijfel kantelde',
            ],
            [
                'Familie Nova zet de vlag maar buiten vandaag',
                'Je zus Saar zag hoe vaak jouw twijfel kantelde',
            ],
            $this->strongLyrics(),
        );
    }
}
