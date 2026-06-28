<?php

namespace App\Services\Music;

use App\Models\SongRequest;

class MusicPromptBuilder
{
    public function build(SongRequest $songRequest, string $lyrics, array $intake): string
    {
        $style = $this->value($intake, 'musicStyle', 'Nederlandstalige pop');
        $tone = $this->value($intake, 'tone', 'persoonlijk en herkenbaar');
        $tempo = $this->value($intake, 'tempo');
        $vocals = $this->value($intake, 'vocals', 'passende stem');
        $mustMention = $this->value($intake, 'mustMention');
        $avoid = $this->value($intake, 'avoid');

        $context = array_filter([
            'Categorie: '.$songRequest->category_title,
            'Sfeer: '.$tone,
            'Muzikale richting: '.$style,
            $tempo ? 'Snelheid/tempo: '.$tempo : null,
            'Stem: '.$vocals,
            $mustMention ? 'Belangrijk om te behouden: '.$mustMention : null,
            $avoid ? 'Vermijd: '.$avoid : null,
        ]);

        return implode("\n", [
            'Maak een compleet Nederlandstalig nummer op basis van deze definitieve lyrics.',
            'Gebruik een duidelijke couplet/refrein-structuur, sterke hook en radiowaardige mix.',
            implode("\n", $context),
            '',
            'Definitieve lyrics:',
            $lyrics,
        ]);
    }

    private function value(array $intake, string $key, ?string $default = null): ?string
    {
        $value = trim((string) ($intake[$key] ?? ''));

        return $value !== '' ? $value : $default;
    }
}
