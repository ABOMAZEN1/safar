<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use Override;
use App\Enum\CustomerGenderEnum;
use App\Rules\Customer\GenderRule;
use App\Rules\Customer\BirthDateRule;
use App\Rules\Customer\NationalIdRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class StoreCustomerInformationRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'birth_date' => BirthDateRule::rules(),
            'national_id' => NationalIdRule::rules(),
            'gender' => GenderRule::rules(),
            'address' => [
                'required',
                'string',
            ],
            'mother_name' => [
                'required',
                'string',
            ],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'birth_date' => 'Birth Date',
            'national_id' => 'National ID',
            'gender' => 'Gender',
            'address' => 'Address',
            'mother_name' => 'Mother Name',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'birth_date.before_today' => 'The :attribute must be before today.',
            'national_id.unique' => 'The :attribute has already been taken.',
            'gender.in' => 'The gender must be either "' . CustomerGenderEnum::MALE->value . '" or "' . CustomerGenderEnum::FEMALE->value . '".',
            'address.string' => 'The :attribute must be a string.',
            'mother_name.string' => 'The :attribute must be a string.',
        ];
    }
}
