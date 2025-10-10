<?php

declare(strict_types=1);

namespace App\Rules\Customer;

final class BirthDateRule
{
    public static function rules(bool $required = true, bool $sometimes = false): array
    {
        $rules = [];

        if ($sometimes) {
            $rules[] = 'sometimes';
        }

        $rules[] = $required ? 'required' : 'nullable';
        $rules[] = 'date_format:d/m/Y';
        $rules[] = 'before_or_equal:' . now()->format('d/m/Y');

        return $rules;
    }
}
