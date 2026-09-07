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
        config(['ai.default' => 'deepseek', 'ai.providers.deepseek.key' => 'test-key', 'ai.lyrics_critic_enabled' => false]);
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

    public function test_removed_form_details_are_rejected(): void
    {
        $generator = $this->generator();
        $draft = $generator->generateDraft('anders', ['name' => 'Henk']);
        Http::fake(['*/responses' => Http::response($this->response($this->lyrics('Piet')))]);
        $this->expectExceptionMessage('formulierdetails weggelaten');
        $generator->review($draft['sections'], 'anders', $draft['context'], ['name' => 'Henk']);
    }

    public function test_malformed_output_is_rejected_instead_of_truncated(): void
    {
        $generator = $this->generator();
        Http::fake(['*/responses' => Http::response($this->response($this->lyrics('Henk')."\nExtra uitleg"))]);
        $this->expectExceptionMessage('ongeldige songstructuur');
        $generator->review([], 'anders', [], []);
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

    private function response(string $lyrics): array
    {
        return ['status' => 'completed', 'output' => [
            ['type' => 'code_interpreter_call', 'status' => 'completed', 'code' => 'print("checked")'],
            ['type' => 'message', 'role' => 'assistant', 'content' => [['type' => 'output_text', 'text' => $lyrics]]],
        ]];
    }
}
