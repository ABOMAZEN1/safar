<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth\BusDriver;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Auth\PasswordRule;
use App\Rules\Phone\PhoneNumberRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class BusDriverLoginRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'phone_number' => PhoneNumberRule::make()
                ->exists(UserTypeEnum::BUS_DRIVER)
                ->rules(),
            'password' => PasswordRule::make()
                ->rules(),
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'phone_number' => __('validation.attributes.phone_number'),
            'password' => __('validation.attributes.password'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'phone_number.required' => __('validation.phone.required'),
            'phone_number.string' => __('validation.phone.string'),
            'phone_number.regex' => __('validation.phone.format'),
            'phone_number.exists' => __('validation.phone.not_found'),
            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password.string'),
        ];
    }
}
