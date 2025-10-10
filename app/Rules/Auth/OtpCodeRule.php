<?php

declare(strict_types=1);

namespace App\Rules\Auth;

final class OtpCodeRule
{
    public static function rules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'numeric',
            'digits:4',
        ];
    }
}
