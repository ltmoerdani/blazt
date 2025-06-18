<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp Web integration using Baileys library.
    | This includes session management, API endpoints, and connection settings.
    |
    */

    'node_service' => [
        'enabled' => env('WHATSAPP_NODE_ENABLED', true),
        'host' => env('WHATSAPP_NODE_HOST', 'localhost'),
        'port' => env('WHATSAPP_NODE_PORT', 3001),
        'api_endpoint' => env('WHATSAPP_API_ENDPOINT', 'http://localhost:3001'),
        'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET', 'blazt-webhook-secret'),
        'timeout' => 30, // seconds
        'max_retries' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Management
    |--------------------------------------------------------------------------
    |
    | Configuration for managing WhatsApp Web sessions, including storage
    | paths, session timeouts, and health check intervals.
    |
    */

    'session' => [
        'storage_path' => storage_path('app/whatsapp-sessions'),
        'qr_code_path' => storage_path('app/public/qr-codes'),
        'media_path' => storage_path('app/public/whatsapp-media'),
        'session_timeout' => 3600, // 1 hour in seconds
        'qr_timeout' => 60, // QR code timeout in seconds
        'health_check_interval' => 300, // 5 minutes
        'auto_reconnect' => true,
        'max_reconnect_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for message sending, including rate limiting, retry logic,
    | and message formatting options.
    |
    */

    'messaging' => [
        'rate_limit' => [
            'messages_per_minute' => 20,
            'messages_per_hour' => 1000,
            'messages_per_day' => 10000,
            'burst_limit' => 5, // Messages in burst
            'burst_interval' => 10, // Seconds between bursts
        ],
        'retry' => [
            'max_attempts' => 3,
            'delay_seconds' => 5,
            'exponential_backoff' => true,
        ],
        'formatting' => [
            'enable_spintax' => true,
            'enable_variables' => true,
            'variable_prefix' => '{{',
            'variable_suffix' => '}}',
        ],
        'media' => [
            'max_file_size' => 16 * 1024 * 1024, // 16MB
            'allowed_types' => ['image', 'video', 'audio', 'document'],
            'allowed_extensions' => [
                'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'video' => ['mp4', 'avi', 'mov', 'mkv'],
                'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
                'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for receiving webhooks from the Node.js WhatsApp service
    | including authentication and event handling.
    |
    */

    'webhooks' => [
        'message_received' => '/api/webhooks/whatsapp/message-received',
        'message_status' => '/api/webhooks/whatsapp/message-status',
        'session_status' => '/api/webhooks/whatsapp/session-status',
        'qr_generated' => '/api/webhooks/whatsapp/qr-generated',
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'blazt-verify-token'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Limits
    |--------------------------------------------------------------------------
    |
    | Default limits for WhatsApp accounts per subscription plan.
    |
    */

    'limits' => [
        'trial' => [
            'accounts' => 1,
            'messages_per_day' => 100,
            'messages_per_month' => 1000,
        ],
        'starter' => [
            'accounts' => 1,
            'messages_per_day' => 500,
            'messages_per_month' => 10000,
        ],
        'pro' => [
            'accounts' => 3,
            'messages_per_day' => 2000,
            'messages_per_month' => 50000,
        ],
        'enterprise' => [
            'accounts' => 10,
            'messages_per_day' => 10000,
            'messages_per_month' => 250000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security & Validation
    |--------------------------------------------------------------------------
    |
    | Security settings for WhatsApp integration including phone number
    | validation and spam prevention.
    |
    */

    'security' => [
        'validate_phone_numbers' => true,
        'phone_number_regex' => '/^[0-9]{8,15}$/',
        'blocked_numbers' => [], // Array of blocked phone numbers
        'spam_detection' => [
            'enabled' => true,
            'max_identical_messages' => 5,
            'time_window_minutes' => 60,
        ],
        'require_opt_in' => false, // Require explicit opt-in for marketing messages
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for logging WhatsApp activities and monitoring
    | session health and message delivery.
    |
    */

    'logging' => [
        'enabled' => true,
        'log_channel' => 'whatsapp',
        'log_level' => env('WHATSAPP_LOG_LEVEL', 'info'),
        'log_messages' => true,
        'log_sessions' => true,
        'log_errors' => true,
        'retention_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development & Testing
    |--------------------------------------------------------------------------
    |
    | Settings specific to development and testing environments.
    |
    */

    'development' => [
        'mock_mode' => env('WHATSAPP_MOCK_MODE', false),
        'test_numbers' => [
            '6281234567890', // Test numbers for development
        ],
        'debug_webhooks' => env('WHATSAPP_DEBUG_WEBHOOKS', false),
    ],

];
