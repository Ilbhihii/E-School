<?php

return [
    'enabled' => env(
        'SENSITIVE_CONTENT_SINGLE_DEVICE',
        true
    ),

    'course_ttl_seconds' => (int) env(
        'SENSITIVE_CONTENT_COURSE_TTL_SECONDS',
        120
    ),

    'heartbeat_seconds' => (int) env(
        'SENSITIVE_CONTENT_HEARTBEAT_SECONDS',
        30
    ),

    'live_grace_minutes' => (int) env(
        'SENSITIVE_CONTENT_LIVE_GRACE_MINUTES',
        2
    ),

    'device_cookie' => env(
        'SENSITIVE_CONTENT_DEVICE_COOKIE',
        'ssa_device_id'
    ),

    'device_cookie_days' => (int) env(
        'SENSITIVE_CONTENT_DEVICE_COOKIE_DAYS',
        730
    ),
];
