<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusDriver;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class ConfirmDepartureRequest extends BaseApiFormRequest
{
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

    #[Override]
    public function attributes(): array
    {
        return [
            'booking_id' => 'BusTripBooking ID',
        ];
    }

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
