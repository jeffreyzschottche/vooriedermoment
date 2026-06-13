<?php

namespace App\Services\Lyrics;

use App\Services\Ai\AiManager;

/**
 * Bouwt een complete songtekst op uit:
 *   - kant-en-klare, al rijmende bouwstenen (config/lyrics.php), en
 *   - één gepersonaliseerd, rijmend AI-couplet (of een rijmende fallback
 *     uit de batch wanneer er geen AI-provider/key is).
 *
 * Structuur: [Couplet 1] -> [Refrein] -> [Couplet 2 (AI)] -> [Refrein]
 */
class LyricsGenerator
{
    public function __construct(private AiManager $ai)
    {
    }

    /**
     * @return array{lyrics: string, preview: string, used_ai: bool}
     */
    public function generate(string $category, array $intake): array
    {
        $cfg = config("lyrics.categories.$category", config('lyrics.default'));

        $placeholders = $this->resolvePlaceholders($cfg, $intake);

        $verse1 = $this->fillLines($cfg['verse'] ?? [], $placeholders);
        $chorus = $this->fillLines($cfg['chorus'] ?? [], $placeholders);
        $intro = $this->fillLines($cfg['intro'] ?? [], $placeholders);

        [$verse2, $usedAi] = $this->personalisedVerse($category, $cfg, $intake, $placeholders);

        $blocks = [];
        if ($intro) {
            $blocks[] = "[Intro]\n" . implode("\n", $intro);
        }
        $blocks[] = "[Couplet 1]\n" . implode("\n", $verse1);
        $blocks[] = "[Refrein]\n" . implode("\n", $chorus);
        $blocks[] = "[Couplet 2]\n" . implode("\n", $verse2);
        $blocks[] = "[Refrein]\n" . implode("\n", $chorus);

        $lyrics = implode("\n\n", $blocks);

        return [
            'lyrics' => $lyrics,
            'preview' => $this->preview($lyrics),
            'used_ai' => $usedAi,
        ];
    }

    /** Map placeholder-namen op concrete waarden uit de intake (met defaults). */
    private function resolvePlaceholders(array $cfg, array $intake): array
    {
        $out = [];
        foreach (($cfg['placeholders'] ?? []) as $key => $field) {
            $value = trim((string) ($intake[$field] ?? ''));
            if ($value === '') {
                $value = $cfg['defaults'][$key] ?? '';
            }
            $out[$key] = $value;
        }
        return $out;
    }

    /** @param string[] $lines */
    private function fillLines(array $lines, array $placeholders): array
    {
        return array_map(fn (string $line) => $this->substitute($line, $placeholders), $lines);
    }

    private function substitute(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        // Restplaceholders netjes leegmaken.
        return trim(preg_replace('/\{[a-z0-9_]+\}/i', '', $text));
    }

    /**
     * Vraag het gepersonaliseerde couplet bij de AI op; val anders terug op de batch.
     *
     * @return array{0: string[], 1: bool}
     */
    private function personalisedVerse(string $category, array $cfg, array $intake, array $placeholders): array
    {
        $ai = $cfg['ai'] ?? null;
        $fallback = $this->fillLines($ai['fallback'] ?? [], $placeholders);

        if (! $ai || empty($ai['instruction'])) {
            return [$fallback, false];
        }

        // Bouw de prompt: placeholders + intake-detailvelden zoals {anecdotes}, {tone}.
        $promptVars = $placeholders + [
            'anecdotes' => trim((string) ($intake['anecdotes'] ?? '')),
            'tone' => trim((string) ($intake['tone'] ?? 'passend bij het moment')),
        ];
        $prompt = $this->substitute($ai['instruction'], $promptVars);

        $result = $this->ai->for($category)->complete($prompt);

        $lines = $this->cleanLines($result);
        if (count($lines) >= 1) {
            return [$lines, true];
        }

        return [$fallback, false];
    }

    /** Maak AI-output schoon tot losse tekstregels. */
    private function cleanLines(string $text): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }
        $lines = preg_split('/\r?\n/', $text) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), fn ($l) => $l !== ''));
        return array_slice($lines, 0, 4);
    }

    private function preview(string $lyrics): string
    {
        $lines = preg_split('/\r?\n/', $lyrics) ?: [];
        return implode("\n", array_slice($lines, 0, 8));
    }
}
