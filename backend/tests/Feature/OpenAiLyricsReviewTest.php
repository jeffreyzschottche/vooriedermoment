<?php

namespace Tests\Feature;

use App\Services\Lyrics\LyricsGenerator;
use App\Services\Lyrics\OpenAiLyricsReviewer;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class OpenAiLyricsReviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['ai.providers.openai.key' => 'test-key', 'ai.final_review.enabled' => true]);
        Http::preventStrayRequests();
    }

    public function test_review_uses_sol_python_and_returns_only_assistant_lyrics(): void
    {
        Http::fake(['*/responses' => Http::response($this->response('Nieuwe lyrics'))]);
        $result = app(OpenAiLyricsReviewer::class)->review('Oude lyrics', 'Naam: Henk');
        $this->assertSame('Nieuwe lyrics', $result);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['model'] === 'gpt-5.6-sol'
            && $request['tool_choice'] === 'required'
            && $request['tools'][0]['type'] === 'code_interpreter'
            && $request['store'] === false
            && str_contains($request['input'], 'Oude lyrics')
            && str_contains($request['input'], 'Naam: Henk'));
    }

    public function test_missing_python_execution_is_rejected(): void
    {
        $response = $this->response('Nieuwe lyrics');
        array_shift($response['output']);
        Http::fake(['*/responses' => Http::response($response)]);
        $this->expectException(RuntimeException::class);
        app(OpenAiLyricsReviewer::class)->review('Oud', 'Context');
    }

    public function test_both_generation_paths_apply_one_final_review(): void
    {
        config(['ai.default' => 'deepseek', 'ai.providers.deepseek.key' => 'test-key']);
        foreach (['verjaardag', 'anders'] as $category) {
            Http::fake([
                'api.deepseek.com/*' => Http::response(['choices' => [['message' => ['content' => $this->lyrics('Henk')]]]]),
                '*/responses' => Http::response($this->response(str_replace('we zingen voor jou', 'dit lied klinkt voor jou', $this->lyrics('Henk')))),
            ]);
            $result = app(LyricsGenerator::class)->generate($category, ['name' => 'Henk']);
            $this->assertStringContainsString('dit lied klinkt voor jou', $result['lyrics']);
            $this->assertStringContainsString('dit lied klinkt voor jou', $result['preview']);
            $this->assertSame($result['lyrics'], $result['formatted']);
            $this->assertCount(1, Http::recorded(fn ($request) => str_ends_with($request->url(), '/responses')));
        }
    }

    public function test_incomplete_response_is_rejected(): void
    {
        $response = $this->response('Afgebroken lyrics');
        $response['status'] = 'incomplete';
        Http::fake(['*/responses' => Http::response($response)]);
        $this->expectException(RuntimeException::class);
        app(OpenAiLyricsReviewer::class)->review('Oud', 'Context');
    }

    public function test_missing_key_fails_without_request(): void
    {
        config(['ai.providers.openai.key' => '']);
        try {
            app(OpenAiLyricsReviewer::class)->review('Oud', 'Context');
            $this->fail('Missing key should fail.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('OPENAI_API_KEY', $e->getMessage());
        }
        Http::assertNothingSent();
    }

    public function test_final_stage_preserves_category_chorus_and_excludes_email(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('verjaardag', ['name' => 'Henk']);
        $raw = $this->lyrics('Henk');
        Http::fake(['*/responses' => Http::response($this->response($raw))]);
        $sections = $generator->review($draft['sections'], 'verjaardag', $draft['context'], ['name' => 'Henk', 'email' => 'private@example.com']);
        $this->assertSame(['verse1', 'chorus', 'verse2', 'chorus', 'bridge', 'chorus_final'], array_column($sections, 'section'));
        Http::assertSent(fn ($request) => str_contains($request['input'], 'Henk') && ! str_contains($request['input'], 'private@example.com'));
    }

    public function test_review_that_keeps_dropping_form_details_is_rejected(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        Http::fake(['*/responses' => Http::response($this->response($this->lyrics('Piet')))]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Formulierfeit verloren');
        $generator->review($this->sections(), 'anders', $draft['context'], ['name' => 'Henk']);
    }

    public function test_review_repairs_bad_rhymes_with_specific_feedback(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        $broken = str_replace('Omdat ik op je bouw', 'Omdat ik naar je kijk', $this->lyrics('Henk'));
        Http::fake(['*/responses' => Http::sequence()
            ->push($this->response($broken))
            ->push($this->response($this->lyrics('Henk')))]);

        $this->assertSame($this->sections(), $generator->review($this->sections(), 'anders', $draft['context'], ['name' => 'Henk']));
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => str_contains($request['input'], 'RIJMSCHEMA: AABB'));
        Http::assertSent(fn ($request) => str_contains($request['input'], 'HERSTELRONDE')
            && str_contains($request['input'], "'jou' en 'kijk'"));
    }

    public function test_unchanged_bad_rhymes_are_rejected_after_one_repair_round(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        $broken = str_replace('Omdat ik op je bouw', 'Omdat ik naar je kijk', $this->lyrics('Henk'));
        $sections = array_map(fn ($section) => [
            'section' => $section['section'],
            'lines' => str_replace('Omdat ik op je bouw', 'Omdat ik naar je kijk', $section['lines']),
        ], $this->sections());
        Http::fake(['*/responses' => Http::response($this->response($broken))]);

        try {
            $generator->review($sections, 'anders', $draft['context'], ['name' => 'Henk']);
            $this->fail('Unchanged rhyme failures must not be accepted.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('afgekeurd na herstelronde', $e->getMessage());
            $this->assertStringContainsString("'jou' en 'kijk'", $e->getMessage());
        }
        Http::assertSentCount(2);
    }

    public function test_self_rhyme_is_rejected(): void
    {
        $generator = $this->generator();
        Http::fake(['*/responses' => Http::response($this->response(
            str_replace('Omdat ik op je bouw', 'Dit lied is ook voor jou', $this->lyrics('Henk')),
        ))]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("'jou' en 'jou'");
        $generator->review($this->sections(), 'anders', [], []);
    }

    public function test_api_failure_does_not_silently_use_the_draft(): void
    {
        Http::fake(['*/responses' => Http::response([], 401)]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HTTP 401');
        $this->generator()->review($this->sections(), 'anders', [], []);
    }

    public function test_review_without_python_does_not_silently_use_the_draft(): void
    {
        $response = $this->response($this->lyrics('Henk'));
        array_shift($response['output']);
        Http::fake(['*/responses' => Http::response($response)]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Python-uitvoering');
        $this->generator()->review($this->sections(), 'anders', [], []);
    }

    public function test_review_receives_missing_facts_and_may_add_them(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        $draft['sections'] = array_map(fn ($section) => ['section' => $section['section'], 'lines' => str_replace('Henk', 'jij', $section['lines'])], $draft['sections']);
        Http::fake(['*/responses' => Http::response($this->response($this->lyrics('Henk')))]);
        $sections = $generator->review($draft['sections'], 'anders', $draft['context'], ['name' => 'Henk']);
        $this->assertStringContainsString('Henk', implode(' ', array_merge(...array_column($sections, 'lines'))));
        Http::assertSent(fn ($request) => str_contains($request['input'], 'NOG NIET VERWERKT'));
    }

    public function test_malformed_output_is_rejected_instead_of_truncated_or_ignored(): void
    {
        $generator = $this->generator();
        Http::fake(['*/responses' => Http::response($this->response($this->lyrics('Henk')."\nExtra uitleg"))]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Ongeldige structuur');
        $generator->review($this->sections(), 'anders', [], []);
    }

    public function test_disabled_review_and_draft_make_no_requests(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        Http::assertNothingSent();
        config(['ai.final_review.enabled' => false]);
        $this->assertSame($draft['sections'], $generator->review($draft['sections'], 'anders', [], []));
        Http::assertNothingSent();
    }

    private function generator(): LyricsGenerator
    {
        return new class extends LyricsGenerator
        {
            public function review(array $sections, string $category, array $context, array $intake): array
            {
                return $this->reviewFinalLyrics($sections, $category, $context, $intake);
            }
        };
    }

    private function lyrics(string $name): string
    {
        return implode("\n", array_map(fn ($heading) => $heading."\n{$name} we zingen voor jou\nOmdat ik op je bouw\nSamen staan we klaar\nVandaag zijn wij bij elkaar", ['[Verse 1]', '[Chorus]', '[Verse 2]', '[Bridge]', '[Final Chorus]']));
    }

    private function sections(): array
    {
        return array_map(
            fn ($section) => ['section' => $section, 'lines' => ['Henk we zingen voor jou', 'Omdat ik op je bouw', 'Samen staan we klaar', 'Vandaag zijn wij bij elkaar']],
            ['verse1', 'chorus', 'verse2', 'bridge', 'chorus_final'],
        );
    }

    private function response(string $lyrics): array
    {
        return ['status' => 'completed', 'output' => [
            ['type' => 'code_interpreter_call', 'status' => 'completed', 'code' => 'print("checked")'],
            ['type' => 'message', 'role' => 'assistant', 'content' => [['type' => 'output_text', 'text' => $lyrics]]],
        ]];
    }
}
