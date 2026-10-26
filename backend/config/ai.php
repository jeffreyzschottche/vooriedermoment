<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Modulaire AI-configuratie
    |--------------------------------------------------------------------------
    |
    | De lyrics-generator vraagt per categorie een provider op bij de AiManager.
    | Zonder API-key valt alles automatisch terug op de NullProvider, zodat de
    | applicatie out-of-the-box werkt (concept-lyrics uit de bouwstenen-batch).
    |
    | Later kun je dit eenvoudig vanuit een CMS overschrijven: dezelfde structuur
    | (default + providers + category_overrides) is dan je opslagmodel.
    |
    */

    'default' => env('AI_PROVIDER', 'null'),

    'final_review' => [
        'enabled' => (bool) env('OPENAI_LYRICS_REVIEW_ENABLED', true),
        'model' => env('OPENAI_LYRICS_REVIEW_MODEL', 'gpt-5.6-sol'),
        'timeout' => (int) env('OPENAI_LYRICS_REVIEW_TIMEOUT', 180),
        'max_output_tokens' => (int) env('OPENAI_LYRICS_REVIEW_MAX_OUTPUT_TOKENS', 12000),
    ],

    'providers' => [

        'null' => [
            'driver' => 'null',
        ],

        'anthropic' => [
            'driver' => 'anthropic',
            'key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-opus-4-8'),
            'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
            'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
            'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 1024),
        ],

        'openai' => [
            'driver' => 'openai',
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 1024),
        ],

        'deepseek' => [
            'driver' => 'deepseek',
            'key' => env('DEEPSEEK_API_KEY'),
            'model' => env('DEEPSEEK_DEFAULT_MODEL', 'deepseek-chat'),
            'fallback_model' => env('DEEPSEEK_FALLBACK_MODEL', 'deepseek-reasoner'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
            // Ruim genoeg voor deepseek-reasoner (denkstappen + volledige lyrics).
            'max_tokens' => (int) env('DEEPSEEK_MAX_TOKENS', 4096),
            'timeout' => (int) env('DEEPSEEK_TIMEOUT', 90),
            'temperature' => (float) env('DEEPSEEK_TEMPERATURE', 0.7),
        ],

    ],

    /*
    | Lyrics-flow per nummer (LyricsGenerator::generateAiLyrics): DeepSeek-chat
    | schrijft, DeepSeek-reasoner controleert + herschrijft in één call, en
    | alleen bij ontbrekende formulierfeiten volgt één reparatiecall. Daarna
    | één OpenAI-eindreview (final_review), met één herstelronde bij afgekeurde
    | output. Max. 3 DeepSeek-calls + 2 OpenAI-reviews (excl. HTTP-retries).
    |
    | lyrics_attempts/lyrics_fallback_after_attempt horen bij het oude per-
    | couplet-pad (generateAiLines) en worden door de flow hierboven niet gebruikt.
    */
    'lyrics_attempts' => (int) env('LYRICS_AI_ATTEMPTS', env('DEEPSEEK_LYRICS_ATTEMPTS', 5)),
    'lyrics_fallback_after_attempt' => (int) env('LYRICS_AI_FALLBACK_AFTER_ATTEMPT', 3),
    'lyrics_require_complete_coverage' => (bool) env('LYRICS_REQUIRE_COMPLETE_COVERAGE', true),
    'lyrics_require_ai_in_production' => (bool) env('LYRICS_REQUIRE_AI_IN_PRODUCTION', true),
    'lyrics_min_words' => (int) env('LYRICS_MIN_WORDS', 110),
    'lyrics_max_words' => (int) env('LYRICS_MAX_WORDS', 180),
    'lyrics_max_cliches' => (int) env('LYRICS_MAX_CLICHES', 2),

    /*
    | Per-categorie override van provider en/of model. Voorbeeld:
    | 'geslaagd'   => ['provider' => 'anthropic', 'model' => 'claude-opus-4-8'],
    | 'verjaardag' => ['provider' => 'openai',    'model' => 'gpt-4o-mini'],
    */
    'category_overrides' => [
        'anders' => ['provider' => 'deepseek', 'model' => env('DEEPSEEK_DEFAULT_MODEL', 'deepseek-chat')],
    ],

];
