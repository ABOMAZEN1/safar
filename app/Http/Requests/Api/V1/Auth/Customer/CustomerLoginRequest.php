<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth\Customer;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Auth\PasswordRule;
use App\Rules\Phone\PhoneNumberRule;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class CustomerLoginRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string|Password>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'phone_number' => PhoneNumberRule::make()
                ->exists(UserTypeEnum::CUSTOMER)
                ->rules(),
            'password' => PasswordRule::make()
                ->rules(),
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'phone_number' => 'Phone Number',
            'password' => 'Password',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'phone_number.required' => 'The :attribute is required.',
            'phone_number.string' => 'The :attribute must be a string.',
            'phone_number.max' => 'The :attribute must be at most 20 characters.',
            'password.required' => 'The :attribute is required.',
            'password.string' => 'The :attribute must be a string.',
            'password.min' => 'The :attribute must be at least 8 characters.',
            'password.mixed_case' => 'The :attribute must include both uppercase and lowercase letters.',
            'password.letters' => 'The :attribute must include at least one letter.',
            'password.numbers' => 'The :attribute must include at least one number.',
            'password.symbols' => 'The :attribute must include at least one symbol.',
            'password.uncompromised' => 'The :attribute is too common.',
        ];
    }
}
