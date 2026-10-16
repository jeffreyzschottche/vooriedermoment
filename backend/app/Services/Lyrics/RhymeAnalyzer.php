<?php

namespace App\Services\Lyrics;

/**
 * Lokale (gratis) kwaliteitscontrole van een complete songtekst: kloppend
 * eindrijm volgens het vaste AABB-schema en zingbare regellengte in
 * lettergrepen. Geen AI-calls; de uitkomst gaat als concrete feedback naar
 * de herschrijfstap en dient als vangnet na de eindreview.
 *
 * Rijm wordt eerst op klank-uitgang beoordeeld (vanaf de laatste beklemtoonde
 * klinker, met Nederlandse uitspraakregels zoals eindklank d=t, g=ch, ei=ij).
 * Alleen bij een afwijkende uitgang raadplegen we Van Dale, dat op uitspraak
 * rijmt en zo bijvoorbeeld onregelmatig gespelde paren alsnog goedkeurt.
 */
class RhymeAnalyzer
{
    /** Rijmparen per sectie van vier regels: 1-2 en 3-4 (AABB). */
    public const PAIRS = [[0, 1], [2, 3]];

    public const MIN_SYLLABLES = 5;

    public const MAX_SYLLABLES = 14;

    /** Maximaal verschil in lettergrepen tussen twee rijmende regels. */
    public const MAX_PAIR_DIFFERENCE = 4;

    /** Van Dale-pagina's per woord voor de controle en het aantal suggesties. */
    private const VAN_DALE_PAGES = 3;

    private const SUGGESTIONS = 12;

    private const LABELS = [
        'verse1' => 'Verse 1',
        'chorus' => 'Chorus',
        'verse2' => 'Verse 2',
        'bridge' => 'Bridge',
        'chorus_final' => 'Final Chorus',
    ];

    public function __construct(private RhymeChecker $rhyme) {}

    /**
     * Rijmparen die niet rijmen, met Van Dale-alternatieven voor het tweede woord.
     *
     * @param  array<int, array{section: string, lines: array<int, string>}>  $sections
     * @return array<int, array{section: string, a: string, b: string, suggestions: array<int, string>}>
     */
    public function rhymeFailures(array $sections, bool $withSuggestions = false): array
    {
        $failures = [];

        foreach ($this->uniqueSections($sections) as $section) {
            foreach (self::PAIRS as [$i, $j]) {
                $a = $this->lastWord($section['lines'][$i] ?? '');
                $b = $this->lastWord($section['lines'][$j] ?? '');
                if ($a === '' || $b === '' || $this->rhymes($a, $b)) {
                    continue;
                }

                $failures[] = [
                    'section' => $section['section'],
                    'a' => $a,
                    'b' => $b,
                    'suggestions' => $withSuggestions ? $this->suggestions($a, $b) : [],
                ];
            }
        }

        return $failures;
    }

    /**
     * Concrete, oplosbare feedback voor de herschrijfprompt.
     *
     * @param  array<int, array{section: string, lines: array<int, string>}>  $sections
     * @return array<int, string>
     */
    public function issues(array $sections): array
    {
        $issues = [];

        foreach ($this->rhymeFailures($sections, true) as $failure) {
            $label = self::LABELS[$failure['section']] ?? $failure['section'];
            $issue = "{$label}: '{$failure['b']}' rijmt niet op '{$failure['a']}'";
            $issue .= $failure['a'] === $failure['b'] || $this->isSelfRhyme($failure['a'], $failure['b'])
                ? ' (zelfrijm telt niet)'
                : '';
            if ($failure['suggestions'] !== []) {
                $issue .= '; rijmwoorden op \''.$failure['a'].'\': '.implode(', ', $failure['suggestions']);
            }
            $issues[] = $issue;
        }

        return array_merge($issues, array_slice($this->meterIssues($sections), 0, 4));
    }

    /**
     * @param  array<int, array{section: string, lines: array<int, string>}>  $sections
     * @return array<int, string>
     */
    public function meterIssues(array $sections): array
    {
        $issues = [];

        foreach ($this->uniqueSections($sections) as $section) {
            $label = self::LABELS[$section['section']] ?? $section['section'];
            $counts = array_map(fn (string $line) => $this->lineSyllables($line), $section['lines']);

            foreach ($counts as $index => $count) {
                if ($count < self::MIN_SYLLABLES || $count > self::MAX_SYLLABLES) {
                    $issues[] = sprintf(
                        '%s regel %d heeft %d lettergrepen; maak er %d–%d van zodat hij zingbaar is',
                        $label, $index + 1, $count, self::MIN_SYLLABLES + 1, self::MAX_SYLLABLES - 2,
                    );
                }
            }

            foreach (self::PAIRS as [$i, $j]) {
                if (isset($counts[$i], $counts[$j]) && abs($counts[$i] - $counts[$j]) > self::MAX_PAIR_DIFFERENCE) {
                    $issues[] = sprintf(
                        '%s regel %d en %d rijmen maar verschillen te veel in lengte (%d en %d lettergrepen)',
                        $label, $i + 1, $j + 1, $counts[$i], $counts[$j],
                    );
                }
            }
        }

        return $issues;
    }

    public function rhymes(string $a, string $b): bool
    {
        $a = $this->normalizeWord($a);
        $b = $this->normalizeWord($b);
        if ($a === '' || $b === '' || $this->isSelfRhyme($a, $b)) {
            return false;
        }

        if ($this->rhymeTail($a) === $this->rhymeTail($b)) {
            return true;
        }

        // Afwijkende uitgang: Van Dale (op uitspraak) mag het paar nog redden.
        return in_array($b, $this->rhyme->rhymeWords($a, self::VAN_DALE_PAGES), true)
            || in_array($a, $this->rhyme->rhymeWords($b, self::VAN_DALE_PAGES), true);
    }

    public function lineSyllables(string $line): int
    {
        $count = 0;
        // Samentrekkingen als z'n en m'n blijven één woord; 't en 's tellen niet mee.
        preg_match_all("/[\\pL']+/u", $line, $matches);
        foreach ($matches[0] as $word) {
            $groups = count($this->vowelGroups($this->normalizeWord($word)));
            $count += $groups > 0 ? $groups : (in_array(trim($word, "'"), ['t', 's', 'k'], true) ? 0 : 1);
        }

        return $count;
    }

    /**
     * Klank-uitgang vanaf de laatste beklemtoonde klinker. Een slot-sjwa
     * ("lopen", "samen", "beter") is onbeklemtoond; dan telt de klinker ervoor mee.
     */
    public function rhymeTail(string $word): string
    {
        $word = $this->phonetic($this->normalizeWord($word));
        preg_match_all('/[aeiouy\x{0133}]+/u', $word, $matches, PREG_OFFSET_CAPTURE);
        $groups = $matches[0];
        if ($groups === []) {
            return $word;
        }

        $last = end($groups);
        $start = $last[1];
        $rest = substr($word, $start + strlen($last[0]));
        if ($last[0] === 'e' && count($groups) > 1 && in_array($rest, ['', 'n', 'r', 'l', 'm', 'nd', 'ns', 'rs', 'ls'], true)) {
            $start = $groups[count($groups) - 2][1];
        }

        return substr($word, $start);
    }

    /** @return array<int, string> */
    private function suggestions(string $a, string $b): array
    {
        $words = array_filter(
            $this->rhyme->rhymeWords($a, self::VAN_DALE_PAGES),
            fn (string $word) => $word !== $b && ! $this->isSelfRhyme($a, $word) && mb_strlen($word) <= 12,
        );

        return array_slice(array_values($words), 0, self::SUGGESTIONS);
    }

    /**
     * Zelfde woord of samenstelling ervan (dag/verjaardag). Een voorvoegsel
     * van alleen medeklinkers is wél echt rijm (uur/muur, aan/staan).
     */
    private function isSelfRhyme(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }

        [$short, $long] = mb_strlen($a) <= mb_strlen($b) ? [$a, $b] : [$b, $a];
        if (! str_ends_with($long, $short)) {
            return false;
        }

        return (bool) preg_match('/[aeiouy]/', substr($long, 0, strlen($long) - strlen($short)));
    }

    /**
     * Het slotrefrein is meestal een kopie van het refrein; niet dubbel melden.
     *
     * @return array<int, array{section: string, lines: array<int, string>}>
     */
    private function uniqueSections(array $sections): array
    {
        $seen = [];
        $unique = [];
        foreach ($sections as $section) {
            $key = mb_strtolower(implode("\n", $section['lines'] ?? []));
            if (count($section['lines'] ?? []) !== 4 || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $section;
        }

        return $unique;
    }

    private function lastWord(string $line): string
    {
        preg_match_all('/[\pL]+/u', $line, $matches);
        $word = end($matches[0]);

        return $word ? $this->normalizeWord($word) : '';
    }

    private function normalizeWord(string $word): string
    {
        $word = mb_strtolower(trim($word));
        $word = strtr($word, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ÿ' => 'y',
        ]);

        return (string) preg_replace('/[^a-z]/', '', $word);
    }

    /** Spelling => klank, voor zover het voor eindrijm uitmaakt. */
    private function phonetic(string $word): string
    {
        $word = str_replace(['ij', 'ei', 'y'], "\u{0133}", $word);
        $word = str_replace('au', 'ou', $word);
        $word = (string) preg_replace('/ouw$/', 'ou', $word);
        $word = (string) preg_replace('/dt$|d$/', 't', $word);
        $word = (string) preg_replace('/(?<!c)g$/', 'ch', $word);

        return (string) preg_replace('/b$/', 'p', $word);
    }

    /** @return array<int, string> */
    private function vowelGroups(string $word): array
    {
        preg_match_all('/[aeiouy\x{0133}]+/u', str_replace('ij', "\u{0133}", $word), $matches);

        return $matches[0];
    }
}
