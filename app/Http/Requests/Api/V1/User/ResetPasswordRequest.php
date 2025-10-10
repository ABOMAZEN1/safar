<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use Override;
use App\Rules\Auth\PasswordRule;
use App\Rules\Phone\PhoneNumberRule;
use App\DataTransferObjects\User\ResetPasswordDto;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class ResetPasswordRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'phone_number' => PhoneNumberRule::make()->rules(),
            'current_password' => [
                'required',
                'string',
            ],
            'new_password' => PasswordRule::make()->rules(),
            'new_password_confirmation' => [
                'required',
                'string',
                'same:new_password',
            ],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'phone_number' => 'Phone Number',
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'new_password_confirmation' => 'Confirm New Password',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'current_password.required' => 'The current password is required.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.mixed_case' => 'The new password must include both uppercase and lowercase letters.',
            'new_password.letters' => 'The new password must include at least one letter.',
            'new_password.numbers' => 'The new password must include at least one number.',
            'new_password.symbols' => 'The new password must include at least one symbol.',
            'new_password.uncompromised' => 'The new password is too common.',
            'new_password_confirmation.same' => 'The confirmation password does not match.',
        ];
    }

    /**
     * Convert the validated data to a DTO.
     */
    public function toDTO(): ResetPasswordDto
    {
        return ResetPasswordDto::fromArray($this->validated());
    }
}
