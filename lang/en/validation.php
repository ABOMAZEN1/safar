<?php

declare(strict_types=1);

return [
    'attributes' => [
        'phone_number' => 'Phone Number',
        'password' => 'Password',
    ],

    'phone' => [
        'required' => 'Phone number is required.',
        'string' => 'Phone number must be a string.',
        'format' => 'Phone number must be a valid 10-digit number starting with 09.',
        'not_found' => 'No bus driver account found with this phone number.',
    ],

    'password' => [
        'required' => 'Password is required.',
        'string' => 'Password must be a string.',
        'min' => 'Password must be at least :min characters.',
        'mixed' => 'Password must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'Password must contain at least one number.',
        'symbols' => 'Password must contain at least one symbol.',
    ],
];
