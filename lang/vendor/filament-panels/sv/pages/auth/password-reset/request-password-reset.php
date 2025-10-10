<?php

declare(strict_types=1);

return [

    'title' => 'Återställ ditt lösenord',

    'heading' => 'Glömt ditt lösenord?',

    'actions' => [

        'login' => [
            'label' => 'tillbaka till inloggningen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'actions' => [

            'request' => [
                'label' => 'Skicka mejlmeddelande',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'För många förfrågningar',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
