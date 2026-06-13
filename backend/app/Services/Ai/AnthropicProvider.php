<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Anthropic Messages API. Zie config/ai.php voor key/model/versie.
 */
class AnthropicProvider implements AiProvider
{
    public function __construct(private array $config)
    {
    }

    public function complete(string $prompt, array $options = []): string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->config['key'],
                'anthropic-version' => $this->config['version'] ?? '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post(rtrim($this->config['base_url'], '/') . '/messages', [
                'model' => $options['model'] ?? $this->config['model'],
                'max_tokens' => $this->config['max_tokens'] ?? 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                Log::warning('Anthropic request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            return trim((string) $response->json('content.0.text', ''));
        } catch (\Throwable $e) {
            Log::warning('Anthropic request threw', ['message' => $e->getMessage()]);
            return '';
        }
    }
}
