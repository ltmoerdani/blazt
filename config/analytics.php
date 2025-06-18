<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for analytics data collection, processing, and reporting.
    |
    */

    'data_retention' => [
        'default_days' => 365,
        'user_analytics_days' => 365,
        'campaign_analytics_days' => 730, // 2 years
        'message_analytics_days' => 90,
        'raw_events_days' => 30, // Raw event data retention
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time Analytics
    |--------------------------------------------------------------------------
    |
    | Settings for real-time analytics processing and dashboard updates.
    |
    */

    'real_time' => [
        'enabled' => true,
        'update_interval_seconds' => 30,
        'batch_size' => 100,
        'queue_driver' => 'redis',
        'cache_ttl_seconds' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Metrics
    |--------------------------------------------------------------------------
    |
    | Configuration for dashboard metrics and KPIs.
    |
    */

    'dashboard' => [
        'default_period' => '7days',
        'available_periods' => [
            '24hours' => 'Last 24 Hours',
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            '90days' => 'Last 90 Days',
            '365days' => 'Last Year',
        ],
        'kpis' => [
            'messages_sent',
            'messages_delivered',
            'delivery_rate',
            'response_rate',
            'active_conversations',
            'ai_requests',
            'campaign_performance',
            'contact_growth',
        ],
        'charts' => [
            'message_volume_over_time',
            'delivery_rates_by_day',
            'response_times',
            'campaign_performance',
            'ai_usage_trends',
            'contact_engagement',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign Analytics
    |--------------------------------------------------------------------------
    |
    | Settings specific to campaign performance tracking.
    |
    */

    'campaigns' => [
        'track_opens' => true,
        'track_clicks' => true,
        'track_responses' => true,
        'track_conversions' => true,
        'attribution_window_days' => 7,
        'goals' => [
            'delivery_rate_target' => 0.95, // 95%
            'response_rate_target' => 0.15, // 15%
            'conversion_rate_target' => 0.05, // 5%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Behavior Analytics
    |--------------------------------------------------------------------------
    |
    | Configuration for tracking user behavior and engagement.
    |
    */

    'user_behavior' => [
        'track_login_frequency' => true,
        'track_feature_usage' => true,
        'track_page_views' => true,
        'track_time_spent' => true,
        'session_timeout_minutes' => 30,
        'anonymize_ip' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export & Reporting
    |--------------------------------------------------------------------------
    |
    | Settings for data export and automated reporting.
    |
    */

    'exports' => [
        'enabled' => true,
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_rows_per_export' => 10000,
        'temp_file_retention_hours' => 24,
        'email_reports' => [
            'enabled' => true,
            'frequencies' => ['daily', 'weekly', 'monthly'],
            'default_frequency' => 'weekly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for monitoring system performance and health.
    |
    */

    'performance' => [
        'track_response_times' => true,
        'track_error_rates' => true,
        'track_queue_health' => true,
        'track_database_performance' => true,
        'alert_thresholds' => [
            'response_time_ms' => 1000,
            'error_rate_percent' => 5,
            'queue_backlog_size' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Processing
    |--------------------------------------------------------------------------
    |
    | Settings for analytics data processing and aggregation.
    |
    */

    'processing' => [
        'batch_processing' => [
            'enabled' => true,
            'schedule' => '0 */6 * * *', // Every 6 hours
            'chunk_size' => 1000,
        ],
        'aggregation' => [
            'hourly' => true,
            'daily' => true,
            'weekly' => true,
            'monthly' => true,
        ],
        'data_quality' => [
            'validate_data' => true,
            'clean_duplicates' => true,
            'handle_missing_values' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy & Compliance
    |--------------------------------------------------------------------------
    |
    | Settings for data privacy and regulatory compliance.
    |
    */

    'privacy' => [
        'anonymize_personal_data' => true,
        'gdpr_compliance' => true,
        'data_retention_policy' => 'auto_delete',
        'consent_required' => false,
        'allow_data_export' => true,
        'allow_data_deletion' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Third-party Integrations
    |--------------------------------------------------------------------------
    |
    | Configuration for external analytics services.
    |
    */

    'integrations' => [
        'google_analytics' => [
            'enabled' => env('GA_ENABLED', false),
            'tracking_id' => env('GA_TRACKING_ID'),
            'enhanced_ecommerce' => true,
        ],
        'facebook_pixel' => [
            'enabled' => env('FB_PIXEL_ENABLED', false),
            'pixel_id' => env('FB_PIXEL_ID'),
        ],
        'mixpanel' => [
            'enabled' => env('MIXPANEL_ENABLED', false),
            'token' => env('MIXPANEL_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts & Notifications
    |--------------------------------------------------------------------------
    |
    | Configuration for analytics alerts and notifications.
    |
    */

    'alerts' => [
        'enabled' => true,
        'channels' => ['email', 'slack', 'webhook'],
        'rules' => [
            'low_delivery_rate' => [
                'threshold' => 0.8, // Below 80%
                'window_minutes' => 60,
                'severity' => 'warning',
            ],
            'high_error_rate' => [
                'threshold' => 0.05, // Above 5%
                'window_minutes' => 30,
                'severity' => 'critical',
            ],
            'unusual_traffic' => [
                'threshold_multiplier' => 3, // 3x normal traffic
                'window_minutes' => 15,
                'severity' => 'info',
            ],
        ],
    ],

];
