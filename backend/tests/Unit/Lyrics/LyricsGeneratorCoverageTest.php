<?php

namespace Tests\Unit\Lyrics;

use App\Services\Lyrics\LyricsGenerator;
use ReflectionMethod;
use Tests\TestCase;

class LyricsGeneratorCoverageTest extends TestCase
{
    public function test_lyrics_are_addressed_to_the_recipient_not_written_as_their_inner_monologue(): void
    {
        $generator = app(LyricsGenerator::class);
        $writeForRecipient = new ReflectionMethod($generator, 'writeForRecipient');

        $sections = $writeForRecipient->invoke($generator, [[
            'section' => 'verse1',
            'lines' => [
                'Bijna tegen de bus, maar ik bleef koel.',
                'Mijn hart zat in mijn keel, maar niemand hield mij tegen.',
            ],
        ]]);

        $this->assertSame('Bijna tegen de bus, maar je bleef koel.', $sections[0]['lines'][0]);
        $this->assertSame('Jouw hart zat in jouw keel, maar niemand hield jou tegen.', $sections[0]['lines'][1]);
    }

    /**
     * Dit is een statische flowtest: er wordt geen AI-provider aangeroepen.
     * Iedere waarde is uniek, zodat we bewijzen dat hij in de briefing staat
     * én (behalve stijl/administratie) door de dekkingcontrole wordt bewaakt.
     */
    public function test_every_category_passes_every_form_value_to_the_ai_briefing_and_coverage_gate(): void
    {
        $generator = app(LyricsGenerator::class);
        $briefing = new ReflectionMethod($generator, 'completeLyricsBriefing');
        $missing = new ReflectionMethod($generator, 'missingIntakeRequirements');

        foreach ($this->categoryIntakes() as $category => $intake) {
            $context = $generator->buildContext($category, $intake);
            $prompt = implode("\n", $briefing->invoke($generator, $context, $intake));
            $requirements = $missing->invoke($generator, '', $context, $intake);
            $requiredValues = array_column($requirements, 'value');

            foreach ($intake as $field => $value) {
                if ($field === 'email') {
                    continue;
                }

                foreach (is_array($value) ? $value : [$value] as $item) {
                    if ($item === '') {
                        continue;
                    }

                    $this->assertStringContainsString(
                        (string) $item,
                        $prompt,
                        "{$category}.{$field} ontbreekt in de AI-briefing.",
                    );
                }
            }

            foreach ($this->nonLiteralFields() as $field) {
                if (array_key_exists($field, $intake)) {
                    $this->assertNotContains(
                        $intake[$field],
                        $requiredValues,
                        "{$category}.{$field} is een stijlinstructie en mag geen letterlijk lyricfeit zijn.",
                    );
                }
            }

            foreach ($this->literalValues($intake) as $value) {
                $this->assertContains(
                    $value,
                    $requiredValues,
                    "{$category}: {$value} wordt niet hard op lyricdekking gecontroleerd.",
                );
            }
        }
    }

    /** @return array<string, array<string, string|array<int, string>>> */
    private function categoryIntakes(): array
    {
        $base = fn (string $prefix): array => [
            'recipientName' => "{$prefix} Hoofdpersoon",
            'fromName' => "{$prefix} Afzender",
            'tone' => "{$prefix} Toon",
            'vocals' => "{$prefix} Stem",
            'musicStyle' => "{$prefix} Genre",
            'tempo' => "{$prefix} Tempo",
            'anecdotes' => "{$prefix} Anekdote",
            'anecdotesItems' => ["{$prefix} Anekdote"],
            'mustMention' => "{$prefix} Verplicht",
            'mustMentionItems' => ["{$prefix} Verplicht"],
            'email' => 'klant@example.test',
        ];

        return [
            'rijbewijs' => $base('Rijbewijs') + [
                'instructor' => 'Rijbewijs Instructeur', 'attempts' => 'Rijbewijs Pogingen',
                'firstDrive' => 'Rijbewijs EersteRit', 'drivingMoment' => 'Rijbewijs Examendag',
            ],
            'geslaagd' => $base('Geslaagd') + [
                'school' => 'Geslaagd School', 'studyLevel' => 'Geslaagd Niveau',
                'nextStep' => 'Geslaagd VolgendeStap', 'examStory' => 'Geslaagd Examenverhaal',
            ],
            'eigen-huis' => $base('Huis') + [
                'place' => 'Huis Buurt', 'firstHome' => 'Huis EersteWoning',
                'homeType' => 'Huis Woningtype', 'favoriteRoom' => 'Huis FavorieteKamer',
            ],
            'vaderdag' => $base('Vader') + [
                'nickname' => 'Vader Koosnaam', 'hobby' => 'Vader Hobby',
                'dadQuote' => 'Vader Uitspraak', 'thanksFor' => 'Vader Dankbaar',
            ],
            'moederdag' => $base('Moeder') + [
                'nickname' => 'Moeder Koosnaam', 'memory' => 'Moeder Herinnering',
                'momTrait' => 'Moeder Eigenschap', 'thanksFor' => 'Moeder Dankbaar',
            ],
            'kind-geboren' => $base('Geboorte') + [
                'babyName' => 'Geboorte Baby', 'birthDate' => '2026-09-04',
                'parents' => 'Geboorte Ouders', 'birthDetails' => 'Geboorte Details',
            ],
            'verjaardag' => $base('Verjaardag') + [
                'age' => 'Verjaardag Leeftijd', 'party' => 'Verjaardag Feest',
                'personality' => 'Verjaardag Karakter', 'partyMoment' => 'Verjaardag Draaimoment',
            ],
            'voetbalclubs' => $base('Club') + [
                'teamType' => 'Club HeleGroep', 'clubName' => 'Club Naam', 'colors' => 'Club Kleuren',
                'players' => 'Club Spelers', 'clubCulture' => 'Club Cultuur', 'chant' => 'Club Leus',
            ],
            'bouwbedrijven' => $base('Bouw') + [
                'companyName' => 'Bouw Bedrijf', 'contactName' => 'Bouw Contact',
                'discipline' => 'Bouw Specialisme', 'foundingYear' => 'Bouw Oprichtingsjaar',
                'slogan' => 'Bouw Slogan', 'occasion' => 'Bouw Gelegenheid',
            ],
            'anders' => $base('Anders') + [
                'occasion' => 'Anders Gelegenheid', 'avoid' => 'Anders Vermijden',
            ],
        ];
    }

    /** @return array<int, string> */
    private function nonLiteralFields(): array
    {
        return ['tone', 'vocals', 'musicStyle', 'tempo', 'avoid', 'teamType'];
    }

    /** @param array<string, string|array<int, string>> $intake
     *  @return array<int, string> */
    private function literalValues(array $intake): array
    {
        $values = [];
        foreach ($intake as $field => $value) {
            if (in_array($field, [...$this->nonLiteralFields(), 'email', 'anecdotes', 'mustMention'], true)) {
                continue;
            }
            foreach (is_array($value) ? $value : [$value] as $item) {
                $values[] = $item;
            }
        }

        return $values;
    }
}
