<?php

return [
    'enforce_https' => (bool) env('ENFORCE_HTTPS', false),
    'admin_ip_allowlist' => array_filter(array_map('trim', explode(',', (string) env('ADMIN_IP_ALLOWLIST', '')))),
    'api_rate_limit_per_minute' => (int) env('API_RATE_LIMIT_PER_MINUTE', 240),
    'payouts_frozen' => (bool) env('PAYOUTS_FROZEN', false),
    'monitor' => [
        'max_failed_jobs' => (int) env('MONITOR_MAX_FAILED_JOBS', 10),
        'max_dead_letters' => (int) env('MONITOR_MAX_DEAD_LETTERS', 10),
        'max_stuck_deposit_minutes' => (int) env('MONITOR_MAX_STUCK_DEPOSIT_MINUTES', 30),
        'max_stuck_payout_minutes' => (int) env('MONITOR_MAX_STUCK_PAYOUT_MINUTES', 30),
    ],
];
