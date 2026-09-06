<?php

return [
    'name' => env('APP_NAME', 'Ajlan & Bros Warehouses'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Riyadh',
    'locale' => env('APP_LOCALE', 'ar'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'supported_locales' => ['ar', 'en', 'zh'],
    'contact_phone' => env('CONTACT_PHONE', '920011381'),
    'contact_email' => env('CONTACT_EMAIL', 'wh@ajlanbros.com'),
    'admin_password' => env('ADMIN_PASSWORD', ''),
];
