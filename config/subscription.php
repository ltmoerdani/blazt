<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define the available subscription plans with their features and limits.
    | This configuration will be used for subscription management and billing.
    |
    */

    'plans' => [
        'trial' => [
            'name' => 'Trial',
            'description' => 'Free trial untuk mencoba fitur dasar selama 7 hari',
            'price' => 0,
            'currency' => 'IDR',
            'duration' => 7, // days
            'stripe_price_id' => null,
            'features' => [
                'whatsapp_accounts' => 1,
                'messages_daily' => 100,
                'messages_monthly' => 1000,
                'ai_requests_daily' => 20,
                'ai_requests_monthly' => 200,
                'contacts_limit' => 500,
                'campaigns_limit' => 5,
                'analytics_retention_days' => 7,
                'support_level' => 'email',
                'features' => [
                    'basic_messaging',
                    'contact_management',
                    'basic_analytics',
                    'ai_chatbot',
                ],
            ],
        ],

        'starter' => [
            'name' => 'Starter',
            'description' => 'Paket starter untuk UMKM dan bisnis kecil',
            'price' => 99000,
            'currency' => 'IDR',
            'duration' => 30, // days
            'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID'),
            'features' => [
                'whatsapp_accounts' => 1,
                'messages_daily' => 500,
                'messages_monthly' => 10000,
                'ai_requests_daily' => 50,
                'ai_requests_monthly' => 1000,
                'contacts_limit' => 5000,
                'campaigns_limit' => 20,
                'analytics_retention_days' => 30,
                'support_level' => 'email',
                'features' => [
                    'basic_messaging',
                    'bulk_messaging',
                    'contact_management',
                    'contact_import',
                    'campaign_scheduling',
                    'basic_analytics',
                    'ai_chatbot',
                    'auto_reply',
                ],
            ],
        ],

        'pro' => [
            'name' => 'Professional',
            'description' => 'Paket profesional untuk bisnis menengah',
            'price' => 299000,
            'currency' => 'IDR',
            'duration' => 30, // days
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
            'features' => [
                'whatsapp_accounts' => 3,
                'messages_daily' => 2000,
                'messages_monthly' => 50000,
                'ai_requests_daily' => 200,
                'ai_requests_monthly' => 5000,
                'contacts_limit' => 25000,
                'campaigns_limit' => 100,
                'analytics_retention_days' => 90,
                'support_level' => 'priority',
                'features' => [
                    'basic_messaging',
                    'bulk_messaging',
                    'contact_management',
                    'contact_import',
                    'contact_segmentation',
                    'campaign_scheduling',
                    'campaign_automation',
                    'advanced_analytics',
                    'ai_chatbot',
                    'multi_ai_providers',
                    'auto_reply',
                    'custom_templates',
                    'api_access',
                ],
            ],
        ],

        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Paket enterprise untuk perusahaan besar',
            'price' => 999000,
            'currency' => 'IDR',
            'duration' => 30, // days
            'stripe_price_id' => env('STRIPE_ENTERPRISE_PRICE_ID'),
            'features' => [
                'whatsapp_accounts' => 10,
                'messages_daily' => 10000,
                'messages_monthly' => 250000,
                'ai_requests_daily' => 1000,
                'ai_requests_monthly' => 25000,
                'contacts_limit' => 100000,
                'campaigns_limit' => 500,
                'analytics_retention_days' => 365,
                'support_level' => 'dedicated',
                'features' => [
                    'basic_messaging',
                    'bulk_messaging',
                    'contact_management',
                    'contact_import',
                    'contact_segmentation',
                    'campaign_scheduling',
                    'campaign_automation',
                    'advanced_analytics',
                    'ai_chatbot',
                    'multi_ai_providers',
                    'auto_reply',
                    'custom_templates',
                    'api_access',
                    'webhook_integration',
                    'white_label',
                    'custom_branding',
                    'sso_integration',
                    'dedicated_support',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for billing, invoicing, and payment processing.
    |
    */

    'billing' => [
        'currency' => 'IDR',
        'tax_rate' => 0.11, // 11% PPN Indonesia
        'grace_period_days' => 3, // Days after subscription expires
        'auto_suspend_after_days' => 7, // Auto suspend after grace period
        'invoice_prefix' => 'BLAZT',
        'payment_methods' => [
            'stripe' => [
                'enabled' => true,
                'currencies' => ['USD', 'IDR'],
            ],
            'midtrans' => [
                'enabled' => true,
                'currencies' => ['IDR'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Tracking
    |--------------------------------------------------------------------------
    |
    | Configuration for tracking usage metrics and overage billing.
    |
    */

    'usage_tracking' => [
        'enabled' => true,
        'track_messages' => true,
        'track_ai_requests' => true,
        'track_api_calls' => true,
        'overage_billing' => [
            'enabled' => true,
            'message_overage_rate' => 50, // IDR per message over limit
            'ai_request_overage_rate' => 100, // IDR per AI request over limit
        ],
        'alerts' => [
            'usage_threshold' => 0.8, // Alert at 80% usage
            'overage_threshold' => 1.1, // Alert at 110% usage
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial & Free Tier
    |--------------------------------------------------------------------------
    |
    | Configuration for trial periods and free tier limitations.
    |
    */

    'trial' => [
        'duration_days' => 7,
        'require_payment_method' => false,
        'auto_upgrade_to' => 'starter',
        'trial_extensions' => [
            'max_extensions' => 1,
            'extension_duration_days' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Features
    |--------------------------------------------------------------------------
    |
    | Define what features are available and their internal identifiers.
    |
    */

    'available_features' => [
        'basic_messaging' => 'Pengiriman pesan dasar',
        'bulk_messaging' => 'Pengiriman pesan massal',
        'contact_management' => 'Manajemen kontak',
        'contact_import' => 'Import kontak dari file',
        'contact_segmentation' => 'Segmentasi kontak lanjutan',
        'campaign_scheduling' => 'Penjadwalan campaign',
        'campaign_automation' => 'Otomasi campaign',
        'basic_analytics' => 'Analytics dasar',
        'advanced_analytics' => 'Analytics lanjutan',
        'ai_chatbot' => 'AI Chatbot',
        'multi_ai_providers' => 'Multiple AI Providers',
        'auto_reply' => 'Auto-reply otomatis',
        'custom_templates' => 'Template pesan kustom',
        'api_access' => 'Akses API',
        'webhook_integration' => 'Integrasi Webhook',
        'white_label' => 'White Label Solution',
        'custom_branding' => 'Branding Kustom',
        'sso_integration' => 'Single Sign-On',
        'dedicated_support' => 'Dedicated Support',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upgrade/Downgrade Rules
    |--------------------------------------------------------------------------
    |
    | Rules for handling plan changes, prorations, and feature access.
    |
    */

    'plan_changes' => [
        'allow_upgrades' => true,
        'allow_downgrades' => true,
        'proration' => true,
        'immediate_access' => true, // Grant access to new features immediately
        'preserve_data_on_downgrade' => true,
        'downgrade_restrictions' => [
            // If user has more data than new plan allows
            'block_if_exceeds_limits' => false,
            'archive_excess_data' => true,
        ],
    ],

];
