<?php

$configuredOrigins = trim(
    (string) env(
        'CORS_ALLOWED_ORIGINS',
        env('APP_URL', 'http://localhost')
    )
);

$allowedOrigins = $configuredOrigins === '*'
    ? ['*']
    : array_values(
        array_filter(
            array_map(
                fn (string $origin) =>
                    rtrim(trim($origin), '/'),
                explode(',', $configuredOrigins)
            )
        )
    );

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
     * En production, limitez CORS aux domaines explicitement autorisés.
     * L'application Flutter native n'est pas soumise aux règles CORS du
     * navigateur. Plusieurs origines peuvent être séparées par des virgules.
     */
    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 600,

    'supports_credentials' => false,
];
