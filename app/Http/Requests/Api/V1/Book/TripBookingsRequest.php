<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Book;

use Override;
use App\Enum\UserTypeEnum;
use App\Enum\CustomerGenderEnum;
use App\Rules\Customer\GenderRule;
use App\Rules\Phone\PhoneNumberRule;
use App\Rules\Customer\BirthDateRule;
use App\Rules\Customer\NationalIdRule;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTripBooking\CreateCompanyTripBookingDto;

final class TripBookingsRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'bus_trip_id' => [
                'required',
                'integer',
                'exists:bus_trips,id',
            ],
            'customer_name' => [
                'required',
                'string',
                'max:100',
            ],
            'phone_number' => PhoneNumberRule::make()->exists(UserTypeEnum::CUSTOMER)->rules(),
            'reserved_seat_count' => [
                'required',
                'integer',
                'min:1',
            ],
            'national_id' => NationalIdRule::rules(),
            'gender' => GenderRule::rules(),
            'birth_date' => BirthDateRule::rules(),
            'address' => [
                'required',
                'string',
                'max:100',
            ],
            'mother_name' => [
                'required',
                'string',
                'max:100',
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

    #[Override]
    public function attributes(): array
    {
        return [
            'bus_trip_id' => 'BusTrip ID',
            'customer_name' => 'Customer Name',
            'phone_number' => 'Phone Number',
            'reserved_seat_count' => 'Number of Reserved Seats',
            'national_id' => 'National ID',
            'gender' => 'Gender',
            'birth_date' => 'Birth Date',
            'address' => 'Address',
            'mother_name' => 'Mother Name',
            'companions' => 'Companions',
            'companions.*' => 'Companion',
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'birth_date.before_today' => 'The :attribute must be before today.',
            'phone_number.unique' => 'The :attribute has already been taken.',
            'gender.in' => 'The gender must be either "' . CustomerGenderEnum::MALE->value . '" or "' . CustomerGenderEnum::FEMALE->value . '".',
            'national_id.unique' => 'The :attribute has already been taken.',
            'reserved_seat_count.min' => 'The :attribute must be at least 1.',
            'phone_number.string' => 'The :attribute must be a string.',
            'phone_number.max' => 'The :attribute must be at most 20 characters.',
            'reserved_seat_count.integer' => 'The :attribute must be an integer.',
            'national_id.string' => 'The :attribute must be a string.',
            'national_id.max' => 'The :attribute must be at most 20 characters.',
            'companions.required_if' => 'The :attribute is required when booking more than one seat.',
            'companions.array' => 'The :attribute must be an array.',
            'companions.min' => 'The :attribute must have at least :min item.',
            'companions.max' => 'The :attribute may not have more than :max items.',
            'companions.*.required' => 'Each companion name is required.',
            'companions.*.string' => 'Each companion name must be a string.',
        ];
    }

    /**
     * Convert the request to a DTO.
     */
    public function toDto(): CreateCompanyTripBookingDto
    {
        return CreateCompanyTripBookingDto::fromArray($this->validated());
    }
}
