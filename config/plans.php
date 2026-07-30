<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Offres commerciales
    |--------------------------------------------------------------------------
    |
    | amount_minor correspond au montant dans la plus petite unité :
    | - 20000 = 200 EUR
    | - 100000 = 1000 MAD
    |
    */

    'default' => 'premium',

    'offers' => [

        'premium' => [
            'code' => 'premium',
            'name' => 'Premium',
            'subtitle' => 'Accès complet à toute la plateforme',
            'scope' => 'Tous les parcours',
            'amount_display' => '200',
            'amount_minor' => 20000,
            'currency' => 'eur',
            'currency_symbol' => '€',
            'period' => 'an',
            'badge' => 'Recommandé',
            'icon' => 'bi-stars',
            'features' => [
                'Tous les cours Arabe, Coran et Soutien Lycée',
                'Lives interactifs illimités',
                'Chat avec les professeurs',
                'Devoirs, quiz et tests',
                'Suivi pédagogique complet',
                'Téléchargement des ressources PDF',
                'Certificats et accompagnement personnalisé',
            ],
        ],

        'soutien_lycee' => [
            'code' => 'soutien_lycee',
            'name' => 'Soutien Lycée',
            'subtitle' => 'Formule dédiée aux élèves du BAC',
            'scope' => 'Soutien Lycée uniquement',
            'amount_display' => '1000',
            'amount_minor' => 100000,
            'currency' => 'mad',
            'currency_symbol' => 'DH',
            'period' => 'an',
            'badge' => 'Offre BAC',
            'icon' => 'bi-mortarboard-fill',
            'features' => [
                'Mathématiques BAC',
                'Physique-Chimie BAC',
                'Test diagnostic avec correction',
                'Cours, vidéos et documents PDF',
                'Lives avec les professeurs',
                'Devoirs, exercices et suivi des notes',
                'Chat et accompagnement pédagogique',
            ],
        ],

    ],

];
