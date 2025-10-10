<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Book;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class TripIdRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'bus_trip_id' => [
                'required',
                'integer',
                'exists:bus_trips,id',
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
            'bus_trip_id' => 'Trip ID',
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
            'bus_trip_id.required' => 'The :attribute is required.',
            'bus_trip_id.integer' => 'The :attribute must be an integer.',
            'bus_trip_id.exists' => 'The :attribute does not exist.',
        ];
    }
}
