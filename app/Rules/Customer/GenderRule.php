<?php

declare(strict_types=1);

namespace App\Rules\Customer;

use App\Enum\CustomerGenderEnum;
use Illuminate\Validation\Rule;

final class GenderRule
{
    public static function rules(bool $required = true, bool $sometimes = false): array
    {
        $rules = [];

        if ($sometimes) {
            $rules[] = 'sometimes';
        }

        $rules[] = $required ? 'required' : 'nullable';
        $rules[] = Rule::in([
            CustomerGenderEnum::MALE->value,
            CustomerGenderEnum::FEMALE->value,
        ]);

        return $rules;
    }
}
