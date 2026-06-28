<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAI Chat Completions API. Zie config/ai.php voor key/model.
 */
class OpenAiProvider implements AiProvider
{
    public function __construct(private array $config)
    {
    }

    public function complete(string $prompt, array $options = []): string
    {
        try {
            $response = Http::withToken($this->config['key'])
                ->timeout(30)
                ->post(rtrim($this->config['base_url'], '/') . '/chat/completions', [
                    'model' => $options['model'] ?? $this->config['model'],
                    // Nieuwere OpenAI-modellen (gpt-5.x e.d.) vereisen
                    // 'max_completion_tokens' i.p.v. het oude 'max_tokens'.
                    'max_completion_tokens' => $this->config['max_tokens'] ?? 1024,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('OpenAI request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            return trim((string) $response->json('choices.0.message.content', ''));
        } catch (\Throwable $e) {
            Log::warning('OpenAI request threw', ['message' => $e->getMessage()]);
            return '';
        }
    }
}
