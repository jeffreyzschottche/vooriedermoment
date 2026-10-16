<?php

namespace Tests\Feature;

use App\Services\Lyrics\RhymeAnalyzer;
use App\Services\Lyrics\RhymeChecker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RhymeAnalyzerTest extends TestCase
{
    public function test_dutch_end_rhyme_is_judged_on_sound(): void
    {
        $analyzer = new RhymeAnalyzer(new RhymeChecker);

        foreach ([['dag', 'lach'], ['samen', 'namen'], ['tijd', 'wijt'], ['jou', 'bouw'], ['uur', 'muur'], ['hoofd', 'belooft'], ['Saar', 'klaar']] as [$a, $b]) {
            $this->assertTrue($analyzer->rhymes($a, $b), "{$a}/{$b} zou moeten rijmen");
        }

        foreach ([['lopen', 'maken'], ['feest', 'dag'], ['dag', 'verjaardag'], ['vlag', 'vandaag'], ['huis', 'applaus']] as [$a, $b]) {
            $this->assertFalse($analyzer->rhymes($a, $b), "{$a}/{$b} zou niet moeten rijmen");
        }
    }

    public function test_van_dale_can_approve_a_pair_with_different_spelling(): void
    {
        config(['rhyme.enabled' => true]);
        Http::fake([
            'rijmwoordenboek.vandale.nl/rijm/team*' => Http::response('<main><a class="w" href="/rijm/droom">droom</a></main>'),
            'rijmwoordenboek.vandale.nl/*' => Http::response('', 404),
        ]);

        $this->assertTrue((new RhymeAnalyzer(new RhymeChecker))->rhymes('team', 'droom'));
    }

    public function test_issues_name_the_pair_and_flag_unsingable_lines(): void
    {
        $issues = (new RhymeAnalyzer(new RhymeChecker))->issues([
            ['section' => 'verse1', 'lines' => [
                'We vieren vandaag samen jouw feest',
                'Omdat je zo lief bent voor ons geweest',
                'Ja',
                'Met al die mensen die vandaag hier bij elkaar zijn gekomen voor jouw dag',
            ]],
        ]);

        $this->assertContains("Verse 1: 'dag' rijmt niet op 'ja'", $issues);
        $this->assertTrue(collect($issues)->contains(fn ($issue) => str_contains($issue, 'regel 3 heeft 1 lettergrepen')));
        $this->assertTrue(collect($issues)->contains(fn ($issue) => str_contains($issue, 'verschillen te veel in lengte')));
    }
}
