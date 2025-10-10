<?php

declare(strict_types=1);

return [

    'title' => 'Wachtwoord opnieuw instellen',

    'heading' => 'Wachtwoord vergeten?',

    'actions' => [

        'login' => [
            'label' => 'terug naar inloggen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'actions' => [

            'request' => [
                'label' => 'E-mail verzenden',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel pogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
