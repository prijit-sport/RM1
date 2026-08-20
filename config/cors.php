<?php
 
return [
 
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This app is primarily server-rendered (Blade), so cross-origin API
    | access should be an explicit opt-in, not the wide-open framework
    | default. Set FRONTEND_URL (and optionally MOBILE_APP_URL, etc.) in
    | .env when a separate-origin client (mobile app, SPA) needs to call
    | the /api/* routes with cookies/credentials.
    |
    | Until that need exists, 'allowed_origins' is empty on purpose —
    | same-origin requests are unaffected by CORS and continue to work.
    |
    */
 
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
 
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
 
    'allowed_origins' => array_filter(explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))),
 
    'allowed_origins_patterns' => [],
 
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-XSRF-TOKEN'],
 
    'exposed_headers' => [],
 
    'max_age' => 0,
 
    'supports_credentials' => true,
 
];
 