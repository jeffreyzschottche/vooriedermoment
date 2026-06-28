<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Suno AI Music Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for the Suno AI music generation API.
    | Uses the unofficial wrapper: https://github.com/gcui-art/suno-api
    |
    | Setup:
    | 1. Clone https://github.com/gcui-art/suno-api
    | 2. Run with docker-compose up
    | 3. Set your Suno cookie in the API's .env
    |
    */

    'suno' => [
        'base_url' => env('SUNO_API_BASE_URL', 'http://localhost:3000'),
        'api_key' => env('SUNO_API_KEY', ''),
        'sample_duration' => env('SUNO_SAMPLE_DURATION', 15),
        'sample_count' => env('SUNO_SAMPLE_COUNT', 4),
    ],

    /*
    |--------------------------------------------------------------------------
    | DistroKid/TuneCore Spotify Distribution
    |--------------------------------------------------------------------------
    |
    | For automatic Spotify releases, you can integrate with:
    | - DistroKid API (if available)
    | - TuneCore API
    | - Or manual upload process
    |
    | Note: Most distributors don't have public APIs.
    | Alternative: Use Spotify for Artists API after manual distribution.
    |
    */

    'spotify' => [
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
    ],

];
