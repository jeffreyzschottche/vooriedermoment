<?php

namespace App\Services\Lyrics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Controleert of twee woorden écht op elkaar rijmen volgens het Van Dale
 * rijmwoordenboek (https://rijmwoordenboek.vandale.nl/rijm/<woord>), dat op
 * uitspraak rijmt i.p.v. spelling. Rijmlijsten worden (incl. paginering)
 * opgehaald en gecachet.
 *
 * Best-effort: bij twijfel (woord onbekend / site onbereikbaar) geeft de
 * checker `null` terug zodat de aanroeper niets onterecht afkeurt.
 */
class RhymeChecker
{
    private bool $enabled;
    private string $baseUrl;
    private int $maxPages;
    private int $cacheDays;

    private string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36';

    public function __construct()
    {
        $this->enabled = (bool) config('rhyme.enabled', true);
        $this->baseUrl = rtrim((string) config('rhyme.base_url', 'https://rijmwoordenboek.vandale.nl/rijm'), '/');
        $this->maxPages = (int) config('rhyme.max_pages', 10);
        $this->cacheDays = (int) config('rhyme.cache_days', 30);
    }

    /**
     * Rijmt $b op $a volgens Van Dale?
     *   true  = ja, $b staat in de rijmlijst van $a
     *   false = nee, $a heeft een rijmlijst maar $b zit er niet in
     *   null  = onbekend (uitgeschakeld, leeg, of niet te bepalen)
     */
    public function rhymesWith(string $a, string $b): ?bool
    {
        if (! $this->enabled) {
            return null;
        }

        $a = $this->normalize($a);
        $b = $this->normalize($b);

        if ($a === '' || $b === '') {
            return null;
        }

        if ($a === $b) {
            return false; // zelfde woord rijmt niet op zichzelf
        }

        $words = $this->rhymeWords($a);

        // Geen (bruikbare) rijmlijst gevonden -> niet te bepalen.
        if (empty($words)) {
            return null;
        }

        return in_array($b, $words, true);
    }

    /**
     * Alle rijmwoorden voor $word (over alle pagina's), genormaliseerd en gecachet.
     *
     * @return array<int, string> Lege array = geen rijmlijst gevonden / fout.
     */
    public function rhymeWords(string $word): array
    {
        $word = $this->normalize($word);
        if ($word === '') {
            return [];
        }

        return Cache::remember('rijm:'.$word, now()->addDays($this->cacheDays), function () use ($word) {
            try {
                $first = $this->fetch($word, 1);
                if ($first === null) {
                    return [];
                }

                $words = $this->parseWords($first);
                if (empty($words)) {
                    return [];
                }

                $lastPage = min($this->parseLastPage($first), $this->maxPages);
                for ($page = 2; $page <= $lastPage; $page++) {
                    $html = $this->fetch($word, $page);
                    if ($html === null) {
                        break;
                    }
                    $words = array_merge($words, $this->parseWords($html));
                }

                return array_values(array_unique($words));
            } catch (\Throwable $e) {
                Log::warning('Rijmcheck faalde', ['word' => $word, 'error' => $e->getMessage()]);

                return [];
            }
        });
    }

    private function fetch(string $word, int $page): ?string
    {
        $url = $this->baseUrl.'/'.rawurlencode($word).($page > 1 ? '/'.$page : '');

        $response = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->timeout(8)
            ->retry(1, 200, throw: false)
            ->get($url);

        return $response->successful() ? $response->body() : null;
    }

    /**
     * Rijmwoorden uit de HTML. Alleen de echte resultaten: anchors met class="w"
     * en een /rijm/-link, en NIET in een <aside> (daar staan zijbalk-widgets als
     * "Rijmwoorden andere bezoekers" en "begint-met/eindigt-op" die dezelfde
     * opmaak gebruiken maar niet rijmen).
     */
    private function parseWords(string $html): array
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $nodes = (new \DOMXPath($doc))->query('//a[@class="w"][not(ancestor::aside)]');
        if ($nodes === false) {
            return [];
        }

        $words = [];
        foreach ($nodes as $node) {
            if (! str_starts_with($node->getAttribute('href'), '/rijm/')) {
                continue;
            }
            $word = $this->normalize($node->textContent);
            if ($word !== '') {
                $words[] = $word;
            }
        }

        return $words;
    }

    /** Hoogste paginanummer uit de paginering (/rijm/woord/2", /rijm/woord/3" ...). */
    private function parseLastPage(string $html): int
    {
        preg_match_all('#/rijm/[^"/]+/(\d+)"#', $html, $matches);
        $pages = array_map('intval', $matches[1] ?? []);

        return $pages ? max($pages) : 1;
    }

    /** Lowercase + alleen letters (incl. accenten), zodat we netjes kunnen vergelijken. */
    private function normalize(string $word): string
    {
        $word = mb_strtolower(trim($word));

        return (string) preg_replace('/[^a-zà-ÿ]/u', '', $word);
    }
}
