<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option defines the default AI provider that will be used when no
    | specific provider is requested. You can set this to any of the
    | providers listed in the "providers" array below.
    |
    */

    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure multiple AI providers for your application.
    | Each provider can have different models, pricing, and capabilities.
    |
    */

    'providers' => [

        'openai' => [
            'name' => 'OpenAI',
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'models' => [
                'gpt-3.5-turbo' => [
                    'name' => 'GPT-3.5 Turbo',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.002,
                ],
                'gpt-4' => [
                    'name' => 'GPT-4',
                    'max_tokens' => 8192,
                    'cost_per_1k_tokens' => 0.03,
                ],
                'gpt-4-turbo' => [
                    'name' => 'GPT-4 Turbo',
                    'max_tokens' => 128000,
                    'cost_per_1k_tokens' => 0.01,
                ],
            ],
            'default_model' => 'gpt-3.5-turbo',
            'default_temperature' => 0.7,
            'default_max_tokens' => 150,
        ],

        'deepseek' => [
            'name' => 'DeepSeek',
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
            'models' => [
                'deepseek-chat' => [
                    'name' => 'DeepSeek Chat',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.0014,
                ],
                'deepseek-coder' => [
                    'name' => 'DeepSeek Coder',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.0014,
                ],
            ],
            'default_model' => 'deepseek-chat',
            'default_temperature' => 0.7,
            'default_max_tokens' => 150,
        ],

        'claude' => [
            'name' => 'Claude (Anthropic)',
            'api_key' => env('CLAUDE_API_KEY'),
            'base_url' => env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'),
            'models' => [
                'claude-3-haiku' => [
                    'name' => 'Claude 3 Haiku',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.00025,
                ],
                'claude-3-sonnet' => [
                    'name' => 'Claude 3 Sonnet',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.003,
                ],
                'claude-3-opus' => [
                    'name' => 'Claude 3 Opus',
                    'max_tokens' => 4096,
                    'cost_per_1k_tokens' => 0.015,
                ],
            ],
            'default_model' => 'claude-3-haiku',
            'default_temperature' => 0.7,
            'default_max_tokens' => 150,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the AI chatbot functionality including context
    | management, response handling, and conversation settings.
    |
    */

    'chatbot' => [
        'context_limit' => 10, // Number of previous messages to keep in context
        'response_timeout' => 30, // Seconds to wait for AI response
        'max_retries' => 3, // Maximum retries on API failure
        'fallback_message' => 'Maaf, saya sedang mengalami gangguan. Silakan coba lagi nanti.',
        'enable_context_persistence' => true,
        'enable_conversation_history' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Reply Rules
    |--------------------------------------------------------------------------
    |
    | Default auto-reply rules and keyword detection settings.
    |
    */

    'auto_reply' => [
        'enable_keyword_detection' => true,
        'enable_intent_recognition' => true,
        'business_hours' => [
            'enabled' => true,
            'timezone' => 'Asia/Jakarta',
            'hours' => [
                'monday' => ['09:00', '17:00'],
                'tuesday' => ['09:00', '17:00'],
                'wednesday' => ['09:00', '17:00'],
                'thursday' => ['09:00', '17:00'],
                'friday' => ['09:00', '17:00'],
                'saturday' => ['09:00', '14:00'],
                'sunday' => null, // Closed
            ],
        ],
        'default_responses' => [
            'greeting' => 'Halo! Terima kasih telah menghubungi kami. Ada yang bisa saya bantu?',
            'after_hours' => 'Terima kasih telah menghubungi kami. Kami sedang di luar jam operasional. Kami akan membalas pesan Anda pada jam kerja berikutnya.',
            'fallback' => 'Terima kasih atas pesan Anda. Tim kami akan segera merespons.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for AI API calls to prevent abuse and
    | manage costs effectively.
    |
    */

    'rate_limiting' => [
        'enabled' => true,
        'per_user_daily_limit' => env('AI_USER_DAILY_LIMIT', 100),
        'per_user_monthly_limit' => env('AI_USER_MONTHLY_LIMIT', 1000),
        'burst_limit' => 10, // Max requests per minute
        'cost_limit_per_month' => 50.00, // USD
    ],

];
