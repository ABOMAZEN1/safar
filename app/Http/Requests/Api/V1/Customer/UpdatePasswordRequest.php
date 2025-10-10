<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use App\Http\Requests\Api\BaseApiFormRequest;

final class UpdatePasswordRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
