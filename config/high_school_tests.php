<?php

/*
|--------------------------------------------------------------------------
| Tests diagnostiques — Soutien Lycée
|--------------------------------------------------------------------------
|
| Ces tests sont des exemples internes et peuvent être modifiés ici
| sans toucher au contrôleur ni à la base de données.
|
*/

return [
    'mathematiques' => [
        'title' => 'Test diagnostic BAC — Mathématiques',
        'subtitle' =>
            'Fonctions, équations, dérivation et probabilités',
        'duration_minutes' => 45,
        'instructions' => [
            'Répondez sur une ou plusieurs feuilles.',
            'Détaillez les calculs et les étapes importantes.',
            'Écrivez votre nom sur chaque feuille.',
            'Photographiez les réponses de manière nette et lisible.',
        ],
        'questions' => [
            [
                'title' => 'Exercice 1 — Fonction polynomiale',
                'points' => 5,
                'statement' =>
                    'On considère la fonction '
                    . 'f(x) = x² − 4x + 3.',
                'items' => [
                    'Factoriser f(x).',
                    'Résoudre l’équation f(x) = 0.',
                    'Déterminer les coordonnées du sommet de la parabole.',
                ],
            ],
            [
                'title' => 'Exercice 2 — Équation du second degré',
                'points' => 5,
                'statement' =>
                    'Résoudre dans ℝ : 2x² − 5x − 3 = 0.',
                'items' => [
                    'Calculer le discriminant.',
                    'Donner les deux solutions exactes.',
                    'Vérifier une solution dans l’équation initiale.',
                ],
            ],
            [
                'title' => 'Exercice 3 — Dérivation',
                'points' => 5,
                'statement' =>
                    'Soit g(x) = x³ − 3x² + 2.',
                'items' => [
                    'Calculer g′(x).',
                    'Étudier le signe de g′(x).',
                    'Dresser le tableau de variations de g.',
                ],
            ],
            [
                'title' => 'Exercice 4 — Probabilités',
                'points' => 5,
                'statement' =>
                    'Une urne contient 5 boules rouges, '
                    . '3 boules bleues et 2 boules vertes. '
                    . 'On tire une boule au hasard.',
                'items' => [
                    'Calculer la probabilité de tirer une boule rouge.',
                    'Calculer la probabilité de ne pas tirer une boule verte.',
                    'Exprimer les résultats sous forme de fractions simplifiées.',
                ],
            ],
        ],
    ],

    'physique-chimie' => [
        'title' => 'Test diagnostic BAC — Physique-Chimie',
        'subtitle' =>
            'Mécanique, électricité, solutions et ondes',
        'duration_minutes' => 45,
        'instructions' => [
            'Répondez sur une ou plusieurs feuilles.',
            'Indiquez les unités dans tous les calculs.',
            'Écrivez votre nom sur chaque feuille.',
            'Photographiez les réponses de manière nette et lisible.',
        ],
        'questions' => [
            [
                'title' => 'Exercice 1 — Énergie cinétique',
                'points' => 5,
                'statement' =>
                    'Une voiture de masse 1 200 kg roule '
                    . 'à la vitesse de 20 m·s⁻¹.',
                'items' => [
                    'Calculer son énergie cinétique.',
                    'Rappeler la formule utilisée et préciser les unités.',
                    'Expliquer qualitativement ce qui arrive à cette énergie pendant un freinage.',
                ],
            ],
            [
                'title' => 'Exercice 2 — Circuit électrique',
                'points' => 5,
                'statement' =>
                    'Une résistance de 6 Ω est branchée '
                    . 'sous une tension de 12 V.',
                'items' => [
                    'Calculer l’intensité du courant.',
                    'Calculer la puissance électrique reçue.',
                    'Calculer l’énergie consommée pendant 10 minutes.',
                ],
            ],
            [
                'title' => 'Exercice 3 — Chimie des solutions',
                'points' => 5,
                'statement' =>
                    'On dispose d’une solution mère de concentration '
                    . 'C₁ = 0,50 mol·L⁻¹. On veut préparer '
                    . '100 mL d’une solution de concentration '
                    . 'C₂ = 0,10 mol·L⁻¹.',
                'items' => [
                    'Calculer le volume de solution mère à prélever.',
                    'Décrire brièvement le protocole de dilution.',
                    'Nommer la verrerie principale utilisée.',
                ],
            ],
            [
                'title' => 'Exercice 4 — Ondes',
                'points' => 5,
                'statement' =>
                    'Une onde périodique possède une période '
                    . 'T = 0,020 s.',
                'items' => [
                    'Calculer sa fréquence.',
                    'Donner l’unité de la fréquence.',
                    'Calculer la longueur d’onde si la célérité vaut 340 m·s⁻¹.',
                ],
            ],
        ],
    ],
];
