<?php

return [
    'enabled' =>
        env(
            'ADMIN_NOTIFICATIONS_ENABLED',
            true
        ),

    'email' => [
        'enabled' =>
            env(
                'ADMIN_EMAIL_NOTIFICATIONS_ENABLED',
                true
            ),

        'to' =>
            env(
                'ADMIN_NOTIFY_EMAIL'
            ),
    ],

    'whatsapp' => [
        'enabled' =>
            env(
                'ADMIN_WHATSAPP_NOTIFICATIONS_ENABLED',
                false
            ),

        'to' =>
            env(
                'ADMIN_NOTIFY_WHATSAPP'
            ),

        /*
         * Version Graph fournie par le tableau de bord Meta.
         * Exemple : vXX.X
         */
        'graph_version' =>
            env(
                'WHATSAPP_GRAPH_VERSION'
            ),

        'phone_number_id' =>
            env(
                'WHATSAPP_PHONE_NUMBER_ID'
            ),

        'access_token' =>
            env(
                'WHATSAPP_ACCESS_TOKEN'
            ),

        /*
         * text :
         * message texte simple.
         *
         * template :
         * modèle Meta approuvé, recommandé pour les notifications
         * proactives envoyées hors fenêtre de conversation.
         */
        'mode' =>
            env(
                'WHATSAPP_MESSAGE_MODE',
                'template'
            ),

        'template_name' =>
            env(
                'WHATSAPP_TEMPLATE_NAME',
                'admin_notification'
            ),

        'template_language' =>
            env(
                'WHATSAPP_TEMPLATE_LANGUAGE',
                'fr'
            ),
    ],
];