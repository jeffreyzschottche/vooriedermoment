<?php

namespace App\Services\Music;

use Illuminate\Support\Str;

/**
 * Vertaalt de Nederlandse formulierkeuzes (genre, stem, tempo) naar Suno-stijl-tags.
 *
 * De sleutels moeten exact gelijk zijn aan de opties in
 * frontend/app/data/musicOptions.ts; SunoStyleTagsTest bewaakt dat.
 * Een onbekende keuze gaat letterlijk mee als tag in plaats van stil weg te vallen.
 */
class SunoStyleTags
{
    public const DEFAULT_GENRE = 'dutch pop, catchy, radio-friendly';

    public const DEFAULT_VOICE = 'lead vocals';

    /** Keuzes die "kies jij maar" betekenen: geen eigen tag. */
    private const NO_PREFERENCE = ['Laat ons kiezen', 'Maakt niet uit'];

    public const GENRES = [
        // Pop
        'Nederlandstalige pop' => 'dutch pop, catchy, radio-friendly',
        'Radio pop' => 'radio pop, polished, catchy hook',
        'Popballad' => 'dutch pop ballad, emotional, melodic',
        'Piano ballad' => 'piano ballad, intimate, emotional',
        'Akoestisch en klein' => 'acoustic, intimate, warm, stripped-back',
        'Singer-songwriter' => 'singer-songwriter, acoustic, personal, warm',
        'Folk pop' => 'folk pop, acoustic guitars, warm, organic',
        'Indie pop' => 'indie pop, fresh, catchy, organic',
        'Dreampop' => 'dream pop, hazy, reverb-soaked, ethereal',
        'Synthpop' => 'synthpop, 80s synths, catchy',
        'Electropop' => 'electropop, glossy synths, punchy beat',
        'Dance pop' => 'dance pop, electronic, energetic, festival-ready',
        'Urban pop' => 'urban pop, modern beats, rhythmic',
        'Power pop' => 'power pop, bright guitars, big chorus',
        'Pop punk' => 'pop punk, fast guitars, energetic, youthful',
        'Bubblegum pop' => 'bubblegum pop, sugary, playful, bouncy',
        'K-pop-stijl' => 'k-pop style, polished, dynamic, catchy',
        'Lo-fi pop' => 'lo-fi pop, mellow, warm texture',
        'Orkestrale pop' => 'orchestral pop, cinematic, grand',
        'Italo pop' => 'italian pop, sunny, romantic, mediterranean',
        // Nederlands & feest
        'Feest / meezinger' => 'dutch party schlager, upbeat, sing-along, festive',
        'Nederlands levenslied' => 'dutch levenslied, emotional, sing-along',
        'Smartlap' => 'dutch smartlap, sentimental, accordion, sing-along',
        'Palingsound' => 'dutch palingsound, 60s beat, jangly guitars',
        'Nederpop jaren 80' => '80s dutch pop, retro synths, new wave',
        'Nederpop jaren 90' => '90s dutch pop, nostalgic, catchy',
        'Kleinkunst / cabaret' => 'dutch cabaret, theatrical, witty, piano',
        'Schlager / après-ski' => 'dutch schlager, apres-ski, festive, sing-along',
        'Carnaval' => 'dutch carnaval, brass, festive, sing-along',
        'Polka / hoempa' => 'polka, oompah brass, festive, bouncy',
        'Volksmuziek modern' => 'modern dutch folk, accordion accents, festive',
        'Zeemanslied / shanty' => 'sea shanty, stomping rhythm, group vocals',
        'Kinderlied / vrolijk' => 'cheerful kids song, simple catchy melody',
        'Slaapliedje' => 'lullaby, gentle, soft, soothing',
        'Kerstlied' => 'christmas song, sleigh bells, warm, festive',
        'Fanfare / mars' => 'marching band, brass fanfare, proud',
        'Koor / stadion' => 'stadium chant, choir vocals, anthemic',
        // Rock
        'Rock / anthem' => 'anthemic rock, guitar-driven, energetic',
        'Classic rock' => 'classic rock, 70s guitars, driving',
        'Soft rock' => 'soft rock, smooth, melodic guitars',
        'Rock & roll' => 'rock and roll, 50s, upbeat, twangy guitars',
        'Indie rock' => 'indie rock, jangly guitars, raw',
        'Alternative rock' => 'alternative rock, gritty guitars, dynamic',
        'Punkrock' => 'punk rock, fast, raw, energetic',
        'Hardrock' => 'hard rock, heavy riffs, powerful',
        'Metal' => 'metal, heavy distorted guitars, powerful drums',
        'Blues rock' => 'blues rock, gritty guitar, soulful',
        // Soul, jazz & blues
        'R&B / soul' => 'r&b soul, smooth, warm, emotional',
        'Motown / classic soul' => 'motown, classic soul, 60s, groovy',
        'Neo-soul' => 'neo-soul, laid-back groove, warm keys',
        'Gospel / soulkoor' => 'gospel soul choir, uplifting, rich harmonies',
        'Funk' => 'funk, slap bass, tight groove',
        'Disco / funk' => 'disco funk, groovy bass, upbeat',
        'Nu-disco' => 'nu-disco, modern disco, groovy, shimmering',
        'Blues' => 'blues, soulful guitar, slow groove',
        'Jazz' => 'jazz, swing feel, warm, sophisticated',
        'Swing / big band' => 'swing, big band, brass, upbeat',
        'Smooth jazz' => 'smooth jazz, mellow saxophone, relaxed',
        'Bossa nova' => 'bossa nova, gentle nylon guitar, relaxed',
        'Lounge' => 'lounge, chill, sophisticated, mellow',
        // Country & folk
        'Country pop' => 'country pop, warm guitars, sing-along',
        'Traditionele country' => 'traditional country, steel guitar, twang',
        'Americana' => 'americana, rootsy, acoustic, warm',
        'Bluegrass' => 'bluegrass, banjo, fiddle, upbeat',
        'Iers / Keltisch' => 'celtic folk, fiddle, tin whistle, lively',
        // Hiphop & urban
        'Hiphop / rapcoupletten' => 'dutch hip-hop verses, melodic chorus, modern beats',
        'Nederhop' => 'dutch hip-hop, nederhop, punchy beat',
        'Old school hiphop' => 'old school hip-hop, 90s boom bap, funky samples',
        'Boom bap' => 'boom bap, dusty drums, sampled',
        'Trap pop' => 'trap pop, modern 808s, melodic hook',
        'Drill' => 'drill, sliding 808s, dark, rhythmic',
        'Afroswing' => 'afroswing, bouncy, melodic, warm',
        'Afropop' => 'afropop, warm groove, rhythmic',
        'Dancehall' => 'dancehall, caribbean rhythm, bouncy',
        'Reggae' => 'reggae, offbeat guitar, laid-back',
        'Ska' => 'ska, upbeat horns, offbeat rhythm',
        // Latin & wereld
        'Latin pop' => 'latin pop, sunny, danceable',
        'Reggaeton pop' => 'reggaeton pop, latin rhythm, danceable',
        'Salsa' => 'salsa, latin percussion, brass, danceable',
        'Bachata' => 'bachata, romantic, latin guitar',
        'Samba' => 'samba, brazilian percussion, festive',
        'Flamenco pop' => 'flamenco pop, spanish guitar, handclaps',
        'Balkan brass' => 'balkan brass, wild horns, festive',
        'Franse chanson' => 'french chanson, accordion, romantic',
        // Elektronisch
        'EDM / festival' => 'edm festival, big drops, energetic',
        'House' => 'house, four-on-the-floor, danceable',
        'Deep house' => 'deep house, smooth, groovy, warm bass',
        'Tropical house' => 'tropical house, summery, marimba, chill',
        'Techno' => 'techno, driving beat, hypnotic',
        'Technopop' => 'techno pop, pulsing synths, modern',
        'Trance' => 'trance, euphoric synths, uplifting',
        'Eurodance jaren 90' => '90s eurodance, energetic, catchy synths',
        'Drum & bass pop' => 'drum and bass pop, fast breakbeats, melodic',
        'Hardstyle feest' => 'party hardstyle, energetic kick, festival',
        'Happy hardcore' => 'happy hardcore, fast, euphoric, energetic',
        'Synthwave' => 'synthwave, retro 80s, neon synths',
        // Overig
        'Musical / theater' => 'musical theatre, expressive, dramatic',
        'Filmmuziek / soundtrack' => 'cinematic soundtrack, epic, orchestral',
        'A capella' => 'a cappella, vocal harmonies, no instruments',
    ];

    public const VOICES = [
        'Mannenstem' => 'male vocals',
        'Vrouwenstem' => 'female vocals',
        'Duet' => 'duet, male and female vocals',
        'Warme mannenstem' => 'warm male vocals',
        'Warme vrouwenstem' => 'warm female vocals',
        'Jonge mannenstem' => 'young male vocals',
        'Jonge vrouwenstem' => 'young female vocals',
        'Oudere mannenstem' => 'mature male vocals',
        'Oudere vrouwenstem' => 'mature female vocals',
        'Krachtige mannenstem' => 'powerful male vocals',
        'Krachtige vrouwenstem' => 'powerful female vocals',
        'Hoge popstem' => 'high pop vocals',
        'Heldere popstem' => 'clear bright pop vocals',
        'Lage rustige stem' => 'low calm vocals',
        'Hese stem' => 'husky vocals',
        'Fluisterzachte stem' => 'soft breathy vocals',
        'Zwoele stem' => 'sultry vocals',
        'Rauwe rockstem' => 'raspy rock vocals',
        'Soulvolle stem' => 'soulful vocals',
        'Falsetto' => 'falsetto vocals',
        'Musicalstem' => 'broadway musical vocals',
        'Operastem' => 'operatic vocals',
        'Levensliedzanger' => 'male levenslied vocals, vibrato',
        'Levensliedzangeres' => 'female levenslied vocals, vibrato',
        'Countrystem' => 'country twang vocals',
        'Praat-zang' => 'spoken-sung vocals',
        'Rapcoupletten + gezongen refrein' => 'rap verses, sung chorus',
        'Alleen rap' => 'rap vocals',
        'Twee mannenstemmen' => 'two male vocalists, harmonies',
        'Twee vrouwenstemmen' => 'two female vocalists, harmonies',
        'Boyband-harmonieën' => 'boy band harmonies, male vocals',
        'Girlband-harmonieën' => 'girl group harmonies, female vocals',
        'Groep / koor' => 'group vocals, choir backing',
        'Koor in refrein' => 'choir in chorus',
        'Familiekoor' => 'family choir vocals',
        'Kinderkoor' => 'children choir vocals',
        'Kindvriendelijke stem' => 'friendly clear vocals',
        'Stadionkoor' => 'stadium choir vocals',
        'Instrumentaal intro met zang' => 'instrumental intro, lead vocals',
    ];

    public const TEMPOS = [
        'Heel rustig' => 'very slow tempo',
        'Rustig' => 'slow tempo',
        'Ontspannen groove' => 'relaxed mid-tempo groove',
        'Medium tempo' => 'mid-tempo',
        'Dansbaar' => 'danceable tempo',
        'Uptempo' => 'uptempo',
        'Snel en feestelijk' => 'fast, festive tempo',
        'Opbouwend van rustig naar groot' => 'slow build to big finale',
        'Rustige coupletten, knallend refrein' => 'calm verses, explosive chorus',
    ];

    public static function genre(?string $choice): string
    {
        return self::lookup(self::GENRES, $choice) ?? self::DEFAULT_GENRE;
    }

    public static function voice(?string $choice): string
    {
        return self::lookup(self::VOICES, $choice) ?? self::DEFAULT_VOICE;
    }

    public static function tempo(?string $choice): ?string
    {
        return self::lookup(self::TEMPOS, $choice);
    }

    /** Volledige Suno-stijlregel: genre, stem, tempo. */
    public static function style(array $intake): string
    {
        return implode(', ', array_filter([
            self::genre($intake['musicStyle'] ?? null),
            self::voice($intake['vocals'] ?? null),
            self::tempo($intake['tempo'] ?? null),
            'dutch lyrics',
            'professional production',
        ]));
    }

    public static function vocalGender(?string $choice): ?string
    {
        $vocals = Str::lower(trim((string) $choice));

        // Vrouw eerst: "zangeres" bevat ook "zanger".
        if (Str::contains($vocals, ['vrouwenstem', 'zangeres', 'girlband'])) {
            return 'female';
        }

        if (Str::contains($vocals, ['mannenstem', 'zanger', 'boyband'])) {
            return 'male';
        }

        return null;
    }

    private static function lookup(array $map, ?string $choice): ?string
    {
        $choice = trim((string) $choice);

        if ($choice === '' || in_array($choice, self::NO_PREFERENCE, true)) {
            return null;
        }

        return $map[$choice] ?? Str::lower($choice);
    }
}
