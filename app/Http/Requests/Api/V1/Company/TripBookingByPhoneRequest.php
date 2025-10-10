<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Company;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTripBooking\CreateBusTripBookingByPhoneDto;
use Illuminate\Contracts\Validation\ValidationRule;

final class TripBookingByPhoneRequest extends BaseApiFormRequest
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
            'phone_number' => [
                'required',
                'string',
                'exists:users,phone_number',
            ],
            'reserved_seat_count' => [
                'required',
                'numeric',
                'min:1',
            ],
            'companions' => [
                'array',
                'required_if:reserved_seat_count,>,1',
                'min:1',
                'max:' . ($this->input('reserved_seat_count') - 1 > 0 ? $this->input('reserved_seat_count') - 1 : 0),
            ],
            'companions.*' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'bus_trip_id.required' => 'The trip ID is required.',
            'bus_trip_id.exists' => 'The trip does not exist.',
            'bus_trip_id.numeric' => 'The trip ID must be a number.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.exists' => 'The phone number does not exist in our records.',
            'reserved_seat_count.required' => 'The number of reserved seats is required.',
            'reserved_seat_count.numeric' => 'The number of reserved seats must be a number.',
            'reserved_seat_count.min' => 'The number of reserved seats must be at least 1.',
            'companions.required_if' => 'The companions list is required when reserving more than 1 seat.',
            'companions.array' => 'The companions list must be an array.',
            'companions.min' => 'The companions list must have at least 1 item.',
            'companions.max' => 'The companions list may not have more than :max items.',
            'companions.*.required' => 'Each companion name is required.',
            'companions.*.string' => 'Each companion name must be a string.',
        ];
    }

    /**
     * Get the validation attributes.
     *
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'bus_trip_id' => 'trip ID',
            'phone_number' => 'phone number',
            'reserved_seat_count' => 'number of reserved seats',
            'companions' => 'companions list',
            'companions.*' => 'companion name',
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
