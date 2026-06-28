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
            'max_tokens' => (int) env('DEEPSEEK_MAX_TOKENS', 1024),
            'temperature' => (float) env('DEEPSEEK_TEMPERATURE', 0.7),
        ],

    ],

    /*
    | Aantal AI-pogingen per lyric-sectie. DeepSeek is goedkoop genoeg om wat
    | vaker te itereren; vanaf de latere pogingen mag de provider zijn fallback-
    | model gebruiken als dat is geconfigureerd.
    */
    'lyrics_attempts' => (int) env('LYRICS_AI_ATTEMPTS', env('DEEPSEEK_LYRICS_ATTEMPTS', 5)),
    'lyrics_fallback_after_attempt' => (int) env('LYRICS_AI_FALLBACK_AFTER_ATTEMPT', 3),

    /*
    | Per-categorie override van provider en/of model. Voorbeeld:
    | 'geslaagd'   => ['provider' => 'anthropic', 'model' => 'claude-opus-4-8'],
    | 'verjaardag' => ['provider' => 'openai',    'model' => 'gpt-4o-mini'],
    */
    'category_overrides' => [
        // slug => ['provider' => '...', 'model' => '...'],
    ],

];
