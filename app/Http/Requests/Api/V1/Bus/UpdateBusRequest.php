<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Bus;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class UpdateBusRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'bus_type_id' => [
                'sometimes',
                'exists:bus_types,id',
            ],
            'capacity' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'details' => [
                'sometimes',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'bus_type_id' => 'Bus Type',
            'capacity' => 'Capacity',
            'details' => 'Details',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'bus_type_id.exists' => 'The :attribute does not exist.',
            'capacity.integer' => 'The :attribute must be an integer.',
            'capacity.min' => 'The :attribute must be at least 1.',
            'details.string' => 'The :attribute must be a string.',
            'details.max' => 'The :attribute must be less than 255 characters.',
        ];
    }
}
