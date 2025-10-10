<?php

declare(strict_types=1);

return [

    'title' => 'Zresetuj hasło',

    'heading' => 'Zapomniałeś hasła?',

    'actions' => [

        'login' => [
            'label' => 'wróć do logowania',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'actions' => [

            'request' => [
                'label' => 'Wyślij e-mail',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Zbyt wiele żądań',
            'body' => 'Spróbuj ponownie za :seconds sekund.',
        ],

    ],

];
