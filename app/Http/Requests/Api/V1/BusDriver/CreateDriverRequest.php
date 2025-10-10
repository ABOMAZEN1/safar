<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusDriver;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Auth\PasswordRule;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class CreateDriverRequest extends BaseApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    #[Override]
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->type === UserTypeEnum::TRAVEL_COMPANY->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^09\d{8}$/',
                'unique:users,phone_number',
            ],
            'password' => PasswordRule::make()->rules(),
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
            'phone_number.unique' => 'The :attribute has already been taken.',
            'password.min' => 'The :attribute must be at least 8 characters.',
            'password.mixed_case' => 'The :attribute must include both uppercase and lowercase letters.',
            'password.letters' => 'The :attribute must include at least one letter.',
            'password.numbers' => 'The :attribute must include at least one number.',
            'password.symbols' => 'The :attribute must include at least one symbol.',
            'password.uncompromised' => 'The :attribute is too common.',
        ];
    }
}
