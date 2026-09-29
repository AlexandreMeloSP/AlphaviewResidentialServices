<?php

// APP_URL pode conter path (ex.: https://host/alphaview) — origin válida é só scheme://host
$appUrl = env('APP_URL', 'http://localhost:8000');
$parts = parse_url($appUrl);
$appOrigin = isset($parts['scheme'], $parts['host'])
    ? $parts['scheme'].'://'.$parts['host']
    : $appUrl;

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração de CORS para o Alphaview Serviços Residenciais.
    | Permite requisições do SPA React via Sanctum cookies.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'),
        $appOrigin,
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-XSRF-TOKEN', 'Accept', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
