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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'api_url' => env('CLAUDE_API_URL', 'https://api.anthropic.com/v1/messages'),
        'model_sonnet' => 'claude-3-5-sonnet-20241022',
        'model_haiku' => 'claude-3-haiku-20240307',
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY', env('CLAUDE_API_KEY')),
        'api_url' => env('ANTHROPIC_API_URL', 'https://api.anthropic.com/v1/messages'),
        'model_sonnet' => 'claude-3-5-sonnet-20241022',
        'model_haiku' => 'claude-3-haiku-20240307',
    ],

    'cohere' => [
        'api_key' => env('COHERE_API_KEY'),
        'api_url' => env('COHERE_API_URL', 'https://api.cohere.ai'),
    ],

    'supabase' => [
        'project_id' => env('SUPABASE_PROJECT_ID'),
        'service_key' => env('SUPABASE_SERVICE_KEY'),
        'anon_key' => env('SUPABASE_ANON_KEY'),
        'url' => env('SUPABASE_URL'),
        'bucket' => env('SUPABASE_BUCKET', 'kkngo-storage'),
    ],

];
