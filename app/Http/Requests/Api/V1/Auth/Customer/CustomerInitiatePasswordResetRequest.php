<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth\Customer;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Phone\PhoneNumberRule;
use App\Http\Requests\Api\BaseApiFormRequest;

final class CustomerInitiatePasswordResetRequest extends BaseApiFormRequest
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
}
