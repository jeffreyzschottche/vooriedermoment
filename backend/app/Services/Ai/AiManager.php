<?php

namespace App\Services\Ai;

/**
 * Kiest per categorie de juiste AI-provider op basis van config/ai.php.
 * Zonder geldige API-key valt alles terug op de NullProvider, zodat de
 * applicatie zonder configuratie blijft werken.
 */
class AiManager
{
    public function for(?string $category = null): AiProvider
    {
        $override = $category ? config("ai.category_overrides.$category") : null;

        $providerName = $override['provider'] ?? config('ai.default', 'null');
        $config = config("ai.providers.$providerName");

        if (! is_array($config)) {
            return new NullProvider();
        }

        if (! empty($override['model'])) {
            $config['model'] = $override['model'];
        }

        return match ($config['driver'] ?? 'null') {
            'anthropic' => filled($config['key'] ?? null) ? new AnthropicProvider($config) : new NullProvider(),
            'openai' => filled($config['key'] ?? null) ? new OpenAiProvider($config) : new NullProvider(),
            default => new NullProvider(),
        };
    }
}
