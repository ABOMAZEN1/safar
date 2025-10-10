<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\TravelCompany;

use App\DataTransferObjects\TravelCompany\CompanyFilterDTO;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;
use App\Enum\OrderByEnum;

final class TravelCompanyFilterRequest extends BaseApiFormRequest
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
                'nullable',
                'string',
                'max:100',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'order_by' => [
                'nullable',
                'string',
                'in:name_asc,name_desc',
            ],
            'has_buses' => [
                'nullable',
                'boolean',
            ],
            'has_active_trips' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'name.string' => 'The :attribute must be a string.',
            'name.max' => 'The :attribute must not exceed :max characters.',
            'address.string' => 'The :attribute must be a string.',
            'address.max' => 'The :attribute must not exceed :max characters.',
            'order_by.in' => 'The :attribute must be a valid sort order.',
            'has_buses.boolean' => 'The :attribute must be a boolean value.',
            'has_active_trips.boolean' => 'The :attribute must be a boolean value.',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'name' => 'company name',
            'address' => 'company address',
            'order_by' => 'sort order',
            'has_buses' => 'has buses',
            'has_active_trips' => 'has active trips',
        ];
    }

    public function toDto(): CompanyFilterDTO
    {
        return CompanyFilterDTO::fromArray($this->validated());
    }
}
