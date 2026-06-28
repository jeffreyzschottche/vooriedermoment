<?php

namespace Tests\Unit;

use App\Services\Ai\DeepSeekProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeepSeekProviderTest extends TestCase
{
    public function test_it_uses_the_default_model(): void
    {
        Http::fake([
            'api.deepseek.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => "regel een\nregel twee"]],
                ],
            ]),
        ]);

        $provider = new DeepSeekProvider($this->config());

        $this->assertSame("regel een\nregel twee", $provider->complete('prompt'));

        Http::assertSent(fn ($request) => $request['model'] === 'deepseek-chat');
    }

    public function test_it_retries_with_the_fallback_model_after_an_empty_result(): void
    {
        Http::fakeSequence()
            ->push(['choices' => [['message' => ['content' => '']]]])
            ->push(['choices' => [['message' => ['content' => 'betere regels']]]]);

        $provider = new DeepSeekProvider($this->config());

        $this->assertSame('betere regels', $provider->complete('prompt'));

        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request['model'] === 'deepseek-reasoner');
    }

    private function config(): array
    {
        return [
            'key' => 'test-key',
            'model' => 'deepseek-chat',
            'fallback_model' => 'deepseek-reasoner',
            'base_url' => 'https://api.deepseek.com',
            'max_tokens' => 1024,
            'temperature' => 0.7,
        ];
    }
}
