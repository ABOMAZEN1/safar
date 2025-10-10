<?php

declare(strict_types=1);

namespace App\Rules\Customer;

use Illuminate\Validation\Rule;

final class NationalIdRule
{
    /**
     * Get validation rules for national ID.
     *
     * @param string|null $exceptId The national ID to exclude from unique validation
     * @param bool $required Whether the field is required
     * @param bool $sometimes Whether the field is optional in updates
     * @return array<int, mixed> Array of validation rules
     */
    public static function rules(?string $exceptId = null, bool $required = true, bool $sometimes = false): array
    {
        $rules = [];

        if ($sometimes) {
            $rules[] = 'sometimes';
        }

        if ($sometimes && $required) {
            $rules[] = 'required_with:national_id';
        } elseif ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $rules[] = 'digits:11';

        $rules[] = Rule::unique('customers', 'national_id')->ignore($exceptId ?: null, 'national_id');

        return $rules;
    }
}
