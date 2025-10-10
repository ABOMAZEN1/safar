<?php

declare(strict_types=1);

return [

    'label' => 'Perfil',

    'form' => [

        'email' => [
            'label' => 'Dirección Email',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [
            'label' => 'Nueva contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar nueva contraseña',
        ],

        'actions' => [

            'save' => [
                'label' => 'Guardar cambios',
            ],

        ],

    ],

    'notifications' => [

        'saved' => [
            'title' => 'Cambios guardados',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Regresar',
        ],

    ],

];
