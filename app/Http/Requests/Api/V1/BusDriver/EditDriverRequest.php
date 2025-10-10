<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusDriver;

use Override;
use App\Rules\Auth\PasswordRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class EditDriverRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'phone_number' => [
                'sometimes',
                'string',
                'regex:/^09\d{8}$/',
            ],
            'password' => [
                'sometimes',
                ...PasswordRule::make()->rules(),
            ],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'phone_number' => 'Phone Number',
            'password' => 'Password',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The :attribute must be a valid Syrian phone number starting with 09 followed by 8 digits.',
            'password.min' => 'The :attribute must be at least 8 characters.',
            'password.mixed_case' => 'The :attribute must include both uppercase and lowercase letters.',
            'password.letters' => 'The :attribute must include at least one letter.',
            'password.numbers' => 'The :attribute must include at least one number.',
            'password.symbols' => 'The :attribute must include at least one symbol.',
            'password.uncompromised' => 'The :attribute is too common.',
        ];
    }
}
