<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Book;

use Override;
use App\Enum\UserTypeEnum;
use App\Rules\Phone\PhoneNumberRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTripBooking\CreateBusTripBookingByPhoneDto;

final class TripBookingByPhoneRequest extends BaseApiFormRequest
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
            'phone_number' => PhoneNumberRule::make()
                ->exists(UserTypeEnum::CUSTOMER)
                ->rules(),
            'reserved_seat_count' => [
                'required',
                'integer',
                'min:1',
            ],
            'companions' => [
                'array',
                'required',
                'min:1',
                'max:' . ($this->input('reserved_seat_count') - 1)
            ],
            'companions.*' => ['required', 'string'],
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
            'phone_number' => 'Phone Number',
            'reserved_seat_count' => 'Reserved Seat Count',
            'companions' => 'Companions',
            'companions.*' => 'Companion name',
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
            'phone_number.required' => 'The :attribute is required.',
            'phone_number.string' => 'The :attribute must be a string.',
            'phone_number.max' => 'The :attribute must be less than 20 characters.',
            'phone_number.exists' => 'The :attribute does not exist.',
            'reserved_seat_count.required' => 'The :attribute is required.',
            'reserved_seat_count.integer' => 'The :attribute must be an integer.',
            'reserved_seat_count.min' => 'The :attribute must be at least 1.',
            'companions.required' => 'The :attribute is required',
            'companions.array' => 'The :attribute must be an array',
            'companions.min' => 'The :attribute must have at least 1 item',
            'companions.max' => 'The :attribute may not have more than :max items',
            'companions.*.required' => 'Each :attribute is required',
            'companions.*.string' => 'Each :attribute must be a string',
        ];
    }

    /**
     * Convert the request to a DTO.
     */
    public function toDto(): CreateBusTripBookingByPhoneDto
    {
        return CreateBusTripBookingByPhoneDto::fromArray($this->validated());
    }
}
