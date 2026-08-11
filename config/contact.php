<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Prise de contact publique
    |--------------------------------------------------------------------------
    */

    'recipient' => env(
        'CONTACT_RECIPIENT',
        'contact.smartschoolacademy@gmail.com'
    ),

    /*
     * Utilisé pour normaliser :
     * 0612345678 -> 212612345678
     * afin d'éviter les doublons selon le format du numéro.
     */
    'default_country_code' => env(
        'CONTACT_DEFAULT_COUNTRY_CODE',
        '212'
    ),

    /*
    |--------------------------------------------------------------------------
    | Tableau en ligne
    |--------------------------------------------------------------------------
    |
    | Laravel envoie une copie du contact principal après chaque
    | formulaire. Le webhook met à jour la même ligne si le contact
    | existe déjà, sinon il ajoute une nouvelle ligne.
    |
    | Compatible avec le script Google Sheets fourni dans docs/.
    | La même charge JSON peut aussi être reçue par Power Automate
    | pour Excel Online.
    |
    */

    'sheet' => [
        'enabled' => env(
            'CONTACT_SHEET_ENABLED',
            false
        ),

        'webhook_url' => env(
            'CONTACT_SHEET_WEBHOOK_URL'
        ),

        'secret' => env(
            'CONTACT_SHEET_SECRET'
        ),

        'timeout' => env(
            'CONTACT_SHEET_TIMEOUT',
            8
        ),
    ],
];
