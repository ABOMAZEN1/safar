<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTripBooking\CreateBookBusTripBookingDto;
use Illuminate\Contracts\Validation\ValidationRule;

final class BookRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'bus_trip_id' => [
                'required',
                'exists:bus_trips,id',
                'numeric',
            ],
            'reserved_seat_count' => [
                'required',
                'numeric',
                'min:1',
            ],
            'companions' => [
                'array',
                'required_if:reserved_seat_count,>1',
            ],
            'companions.*' => [
                'required_with:companions',
                'string',
                'max:255'
            ],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'bus_trip_id' => 'trip id',
            'reserved_seat_count' => 'number of reserved seats',
            'companions' => 'companions list',
            'companions.*' => 'companion name',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'bus_trip_id.required' => __('messages.errors.generic.validation.failed'),
            'bus_trip_id.exists' => __('messages.errors.bus_trip.not_found'),
            'bus_trip_id.numeric' => __('messages.errors.generic.validation.failed'),
            'reserved_seat_count.required' => __('messages.errors.generic.validation.failed'),
            'reserved_seat_count.numeric' => __('messages.errors.generic.validation.failed'),
            'reserved_seat_count.min' => __('messages.errors.generic.validation.failed'),
            'companions.required_if' => __('messages.errors.booking.companions_required_array'),
            'companions.array' => __('messages.errors.booking.companions_required_array'),
            'companions.*.required_with' => __('messages.errors.generic.validation.failed'),
            'companions.*.string' => __('messages.errors.generic.validation.failed'),
            'companions.*.max' => __('messages.errors.generic.validation.failed'),
        ];
    }

    /**
     * Convert the request to a DTO.
     */
    public function toDto(): CreateBookBusTripBookingDto
    {
        return CreateBookBusTripBookingDto::fromArray($this->validated());
    }
}
