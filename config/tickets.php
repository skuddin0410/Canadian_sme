<?php
    
return [
    
    // Default ticket settings
    'defaults' => [
        'currency' => 'USD',
        'currency_symbol' => '$',
        'tax_rate' => 0.0, // 0% by default, set per event
        'booking_fee' => 0.0,
        'max_tickets_per_order' => 10,
        'reservation_timeout' => 15, // minutes
    ],
    
    // Inventory management
    'inventory' => [
        'low_stock_threshold' => 0.1, // 10% of total quantity
        'sold_out_threshold' => 0,
        'auto_release_expired_reservations' => true,
        'track_inventory_changes' => true,
    ],
    
    // Pricing rules
    'pricing' => [
        'allowed_discount_types' => [
            'percentage',
            'fixed_amount',
            'buy_x_get_y',
        ],
        'max_discount_percentage' => 100,
        'early_bird_default_days' => 30,
        'late_bird_default_days' => 7,
    ],
    
    // Ticket categories
    'categories' => [
        'default_colors' => [
            '#007bff', // Primary blue
            '#28a745', // Success green
            '#dc3545', // Danger red
            '#ffc107', // Warning yellow
            '#17a2b8', // Info cyan
            '#6f42c1', // Purple
            '#e83e8c', // Pink
            '#fd7e14', // Orange
        ],
        'max_name_length' => 50,
        'allow_custom_colors' => true,
    ],
    
    // Promo codes
    'promo_codes' => [
        'code_length' => 8,
        'code_format' => 'alphanumeric', // alphanumeric, alphabetic, numeric
        'case_sensitive' => false,
        'max_usage_per_user' => 1,
        'default_expiry_days' => 30,
    ],
    
    // Notifications
    'notifications' => [
        'low_stock_notification' => true,
        'sold_out_notification' => true,
        'inventory_change_notification' => false,
        'new_booking_notification' => true,
    ],
    
    // Export formats
    'exports' => [
        'allowed_formats' => ['csv', 'xlsx', 'pdf'],
        'max_export_rows' => 10000,
        'include_metadata' => true,
    ],
    
    // Performance settings
    'performance' => [
        'cache_pricing_rules' => true,
        'cache_duration' => 300, // 5 minutes
        'enable_query_logging' => env('TICKET_QUERY_LOGGING', false),
    ],
];