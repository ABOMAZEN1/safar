<?php

declare(strict_types=1);

return [

    'title' => 'Salasana hukassa?',

    'heading' => 'Salasana hukassa?',

    'actions' => [

        'login' => [
            'label' => 'takaisin kirjautumiseen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'actions' => [

            'request' => [
                'label' => 'Lähetä sähköposti',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liian monta pyyntöä',
            'body' => 'Yritä uudelleen :seconds sekunnin kuluttua.',
        ],

    ],

];
