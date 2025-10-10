<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Book;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class RefundBookingRequest extends BaseApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'booking_id' => [
                'required',
                'integer',
                'exists:bus_trip_bookings,id',
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
            'booking_id' => 'Booking ID',
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
            'booking_id.required' => 'The :attribute is required.',
            'booking_id.integer' => 'The :attribute must be an integer.',
            'booking_id.exists' => 'The :attribute does not exist.',
        ];
    }
}
