<?php

namespace App\Services\Ai;

/**
 * Stub-provider: doet geen externe call. Wordt gebruikt zolang er geen API-key
 * is geconfigureerd. De LyricsGenerator valt dan terug op de rijm-batch.
 */
class NullProvider implements AiProvider
{
    public function complete(string $prompt, array $options = []): string
    {
        return '';
    }
}
