<?php

$allowedOrigins = array_values(array_filter(array_map(
    static fn (string $origin) => trim($origin),
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', (string) env('FRONTEND_URL', 'http://localhost:5173')))
), static fn (string $origin) => filter_var($origin, FILTER_VALIDATE_URL) !== false));

$supportsCredentials = filter_var(
    env('CORS_SUPPORTS_CREDENTIALS', true),
    FILTER_VALIDATE_BOOLEAN,
    FILTER_NULL_ON_FAILURE
);
$isProduction = strtolower((string) env('APP_ENV', '')) === 'production';
$effectiveSupportsCredentials = $supportsCredentials ?? true;

// Cookie-based Sanctum SPA auth requires credentialed CORS.
// Force this on in production to avoid silent auth breakage from env drift.
if ($isProduction && $effectiveSupportsCredentials !== true) {
    $effectiveSupportsCredentials = true;
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => $effectiveSupportsCredentials,

];
