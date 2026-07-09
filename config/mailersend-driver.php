<?php

return [
    /*
    | La clé API est lue depuis le fichier .env (MAILERSEND_API_KEY).
    | Ne pas hardcoder la clé ici pour des raisons de sécurité.
    */
    'api_key' => env('MAILERSEND_API_KEY'),
    'host' => 'api.mailersend.com',
    'protocol' => 'https',
];
