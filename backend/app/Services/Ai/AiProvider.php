<?php

namespace App\Services\Ai;

interface AiProvider
{
    /**
     * Voltooi een prompt en geef platte tekst terug.
     * Mag een lege string teruggeven (bv. NullProvider) — de aanroeper
     * voorziet dan in een fallback.
     */
    public function complete(string $prompt, array $options = []): string;
}
