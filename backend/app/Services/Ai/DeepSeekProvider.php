<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * DeepSeek Chat Completions API. OpenAI-compatible, maar met eigen defaults.
 */
class DeepSeekProvider implements AiProvider
{
    public function __construct(private array $config)
    {
    }

    public function complete(string $prompt, array $options = []): string
    {
        $model = $this->modelFor($options);
        $result = $this->request($prompt, $model);

        if ($result !== '' || $model === $this->fallbackModel()) {
            return $result;
        }

        $fallback = $this->fallbackModel();
        if ($fallback === null) {
            return '';
        }

        return $this->request($prompt, $fallback);
    }

    private function modelFor(array $options): string
    {
        if (! empty($options['model'])) {
            return (string) $options['model'];
        }

        if (($options['use_fallback_model'] ?? false) && $this->fallbackModel() !== null) {
            return $this->fallbackModel();
        }

        return (string) $this->config['model'];
    }

    private function fallbackModel(): ?string
    {
        $model = trim((string) ($this->config['fallback_model'] ?? ''));

        return $model !== '' ? $model : null;
    }

    private function request(string $prompt, string $model): string
    {
        try {
            $response = Http::withToken($this->config['key'])
                ->timeout(45)
                ->post(rtrim($this->config['base_url'], '/') . '/chat/completions', [
                    'model' => $model,
                    'max_tokens' => $this->config['max_tokens'] ?? 1024,
                    'temperature' => $this->config['temperature'] ?? 0.7,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('DeepSeek request failed', [
                    'model' => $model,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return '';
            }

            return trim((string) $response->json('choices.0.message.content', ''));
        } catch (\Throwable $e) {
            Log::warning('DeepSeek request threw', ['model' => $model, 'message' => $e->getMessage()]);

            return '';
        }
    }
}
