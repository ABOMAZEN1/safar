<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use Override;
use App\Models\User;
use App\Enum\CustomerGenderEnum;
use App\Rules\Customer\GenderRule;
use Illuminate\Support\Facades\Auth;
use App\Rules\Customer\NationalIdRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\Customer\UpdateCustomerInformationDto;
use Illuminate\Contracts\Validation\ValidationRule;

final class UpdateInformationRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $currentNationalId = $user->customer?->national_id;

        return [
            'name' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
            'birth_date' => [
                'sometimes',
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'national_id' => NationalIdRule::rules(
                exceptId: $currentNationalId,
                required: true,
                sometimes: true
            ),
            'gender' => GenderRule::rules(required: true, sometimes: true),
            'address' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'mother_name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'national_id.unique' => trans('validation.custom.national_id.unique'),
            'national_id.required_with' => trans('validation.custom.national_id.required_with'),
            'national_id.digits' => trans('validation.custom.national_id.digits'),
            'birth_date.date' => trans('validation.custom.birth_date.date'),
            'birth_date.date_format' => trans('validation.custom.birth_date.date_format'),
            'birth_date.before_or_equal' => trans('validation.custom.birth_date.before_or_equal'),
            'gender.in' => trans('validation.custom.gender.in', [
                'values' => CustomerGenderEnum::MALE->value . ' or ' . CustomerGenderEnum::FEMALE->value,
            ]),
            'address.string' => trans('validation.custom.address.string'),
            'mother_name.string' => trans('validation.custom.mother_name.string'),
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'national_id' => trans('validation.attributes.national_id'),
            'birth_date' => trans('validation.attributes.birth_date'),
            'gender' => trans('validation.attributes.gender'),
            'address' => trans('validation.attributes.address'),
            'mother_name' => trans('validation.attributes.mother_name'),
        ];
    }

    /**
     * Convert the validated data to a DTO.
     */
    public function toDTO(): UpdateCustomerInformationDto
    {
        return UpdateCustomerInformationDto::fromArray($this->validated());
    }
}
