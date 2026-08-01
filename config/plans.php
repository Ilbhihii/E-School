<?php

return [

    'default' => 'premium',

    'offers' => [

        'premium' => [
            'code' => 'premium',
            'name' => 'Premium',
            'subtitle' => 'Accès complet et illimité',
            'scope' => 'Tous les parcours',
            'amount_display' => '200',
            'amount_minor' => 20000,
            'currency' => 'eur',
            'currency_symbol' => '€',
            'period' => 'an',
            'badge' => 'Recommandé',
            'icon' => 'bi-stars',
            'restricted_to_high_school' => false,
            'features' => [
                'Arabe, Coran et Soutien Lycée',
                'Tous les cours et documents PDF',
                'Lives interactifs illimités',
                'Chat avec les professeurs',
                'Devoirs, quiz et tests',
                'Suivi pédagogique complet',
                'Accompagnement personnalisé',
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
            'restricted_to_high_school' => true,
            'features' => [
                'Mathématiques BAC',
                'Physique-Chimie BAC',
                'Cours, vidéos et documents PDF',
                'Lives avec les professeurs',
                'Devoirs, exercices et notes',
                'Chat et accompagnement pédagogique',
            ],
        ],

    ],

];
