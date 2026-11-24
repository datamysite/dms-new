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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'pagespeed' => [
        'key' => env('PAGESPEED_API_KEY'),
        'default_base' => env('PAGESPEED_BASE_URL', 'https://staging.datamysite.com'),
        // Lighthouse category scores (0-100) used by `php artisan seo:pagespeed`:
        //   fail = below this the command exits with an error; target = the 95-100 goal; Core Web Vitals limits per strategy live in PageSpeedService::TARGETS_* and .claude/rules/performance.md (shown as a warning)
        'thresholds' => [
            'performance'    => ['fail' => 90, 'target' => 95],
            'accessibility'  => ['fail' => 95, 'target' => 100],
            'best-practices' => ['fail' => 90, 'target' => 95],
            'seo'            => ['fail' => 95, 'target' => 100],
        ],
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
    ],

    'hubspot' => [
        'token' => env('HUBSPOT_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
        'from' => env('LEAD_FROM_EMAIL'),
        'to' => env('LEAD_TO_EMAIL'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    /* The Ava chat widget is hidden while it is not working. Set CHATBOT_ENABLED=true in .env (then php artisan config:cache) to bring it back; no code change needed. */
    'chatbot' => [
        'enabled' => (bool) env('CHATBOT_ENABLED', false),
    ],

    'blog_api' => [
        'token'     => env('BLOG_API_TOKEN'),
        'author_id' => env('BLOG_API_AUTHOR_ID'),
    ],

];
