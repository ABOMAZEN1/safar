<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Bus;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class CreateBusRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'bus_type_id' => [
                'required',
                'exists:bus_types,id',
            ],
            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],
            'details' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'bus_type_id' => 'Bus Type',
            'capacity' => 'Capacity',
            'details' => 'Details',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'bus_type_id.required' => 'The bus type is required.',
            'bus_type_id.exists' => 'The bus type does not exist.',
            'capacity.required' => 'The capacity is required.',
            'capacity.integer' => 'The capacity must be an integer.',
            'capacity.min' => 'The capacity must be at least 1.',
            'details.required' => 'The details are required.',
            'details.string' => 'The details must be a string.',
            'details.max' => 'The details must be less than 255 characters.',
        ];
    }
}
