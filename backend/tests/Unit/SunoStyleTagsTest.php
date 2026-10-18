<?php

namespace Tests\Unit;

use App\Services\Music\SunoStyleTags;
use PHPUnit\Framework\TestCase;

class SunoStyleTagsTest extends TestCase
{
    private const NO_PREFERENCE = ['Laat ons kiezen', 'Maakt niet uit'];

    /**
     * Elke keuze in het formulier moet een eigen Suno-vertaling hebben,
     * anders valt die stil terug op een standaardtag.
     */
    public function test_every_form_option_has_a_suno_mapping(): void
    {
        $options = $this->frontendOptions();

        $this->assertCount(100, $options['musicStyleOptions']);
        $this->assertCount(40, $options['vocalOptions']);
        $this->assertCount(10, $options['tempoOptions']);
        $this->assertCount(100, $options['toneOptions']);

        foreach ([
            'musicStyleOptions' => SunoStyleTags::GENRES,
            'vocalOptions' => SunoStyleTags::VOICES,
            'tempoOptions' => SunoStyleTags::TEMPOS,
        ] as $list => $map) {
            $missing = array_diff($options[$list], array_keys($map), self::NO_PREFERENCE);
            $this->assertSame([], array_values($missing), "{$list} mist een Suno-vertaling");
        }
    }

    public function test_style_combines_genre_voice_and_tempo(): void
    {
        $this->assertSame(
            'dutch pop ballad, emotional, melodic, warm female vocals, slow tempo, dutch lyrics, professional production',
            SunoStyleTags::style(['musicStyle' => 'Popballad', 'vocals' => 'Warme vrouwenstem', 'tempo' => 'Rustig']),
        );
    }

    public function test_no_preference_falls_back_to_defaults(): void
    {
        $this->assertSame(
            SunoStyleTags::DEFAULT_GENRE.', '.SunoStyleTags::DEFAULT_VOICE.', dutch lyrics, professional production',
            SunoStyleTags::style(['musicStyle' => 'Laat ons kiezen', 'vocals' => 'Maakt niet uit', 'tempo' => 'Laat ons kiezen']),
        );
    }

    public function test_unknown_choice_is_passed_through(): void
    {
        $this->assertSame('vaporwave', SunoStyleTags::genre('Vaporwave'));
    }

    public function test_vocal_gender(): void
    {
        $this->assertSame('female', SunoStyleTags::vocalGender('Levensliedzangeres'));
        $this->assertSame('male', SunoStyleTags::vocalGender('Levensliedzanger'));
        $this->assertSame('male', SunoStyleTags::vocalGender('Twee mannenstemmen'));
        $this->assertSame('female', SunoStyleTags::vocalGender('Girlband-harmonieën'));
        $this->assertNull(SunoStyleTags::vocalGender('Duet'));
        $this->assertNull(SunoStyleTags::vocalGender('Maakt niet uit'));
    }

    /** @return array<string, list<string>> */
    private function frontendOptions(): array
    {
        $path = dirname(__DIR__, 3).'/frontend/app/data/musicOptions.ts';
        if (! is_file($path)) {
            $this->markTestSkipped('frontend/app/data/musicOptions.ts niet gevonden.');
        }

        preg_match_all('/export const (\w+) = \[(.*?)\];/s', file_get_contents($path), $lists, PREG_SET_ORDER);

        $options = [];
        foreach ($lists as [, $name, $body]) {
            preg_match_all("/^\s*'([^']+)',/m", $body, $items);
            $options[$name] = $items[1];
        }

        return $options;
    }
}
