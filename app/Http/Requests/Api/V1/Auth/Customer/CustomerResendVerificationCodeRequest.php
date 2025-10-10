<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth\Customer;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Phone\PhoneNumberRule;
use App\Http\Requests\Api\BaseApiFormRequest;

final class CustomerResendVerificationCodeRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'phone_number' => PhoneNumberRule::make()
                ->exists(UserTypeEnum::CUSTOMER)
                ->rules(),
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return ['phone_number' => 'Phone Number'];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'phone_number.required' => 'The :attribute is required.',
            'phone_number.string' => 'The :attribute must be a string.',
            'phone_number.max' => 'The :attribute must be ≤ 20 chars.',
            'phone_number.exists' => 'The :attribute is invalid.',
        ];
    }
}
