<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Company;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTripBooking\CreateCompanyBusTripBookingDto;
use Illuminate\Contracts\Validation\ValidationRule;

final class TripBookingsRequest extends BaseApiFormRequest
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
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone_number' => [
                'required',
                'string',
                'unique:users,phone_number',
                'regex:/^[0-9]{10,15}$/',
            ],
            'national_id' => [
                'required',
                'string',
                'unique:customers,national_id',
                'min:10',
            ],
            'gender' => [
                'required',
                'string',
                'in:male,female',
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'mother_name' => [
                'required',
                'string',
                'max:255',
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
            'customer_name.required' => 'The customer name is required.',
            'customer_name.string' => 'The customer name must be a string.',
            'customer_name.max' => 'The customer name may not be longer than 255 characters.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'The phone number format is invalid. It should be between 10-15 digits.',
            'national_id.required' => 'The national ID is required.',
            'national_id.string' => 'The national ID must be a string.',
            'national_id.unique' => 'This national ID is already registered.',
            'national_id.min' => 'The national ID must be at least 10 characters.',
            'gender.required' => 'The gender is required.',
            'gender.string' => 'The gender must be a string.',
            'gender.in' => 'The gender must be either male or female.',
            'birth_date.required' => 'The birth date is required.',
            'birth_date.date' => 'The birth date must be a valid date.',
            'birth_date.before_or_equal' => 'The birth date must be before or equal to today.',
            'address.required' => 'The address is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be longer than 255 characters.',
            'mother_name.required' => "The mother's name is required.",
            'mother_name.string' => "The mother's name must be a string.",
            'mother_name.max' => "The mother's name may not be longer than 255 characters.",
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
            'customer_name' => 'customer name',
            'phone_number' => 'phone number',
            'national_id' => 'national ID',
            'gender' => 'gender',
            'birth_date' => 'birth date',
            'address' => 'address',
            'mother_name' => "mother's name",
            'reserved_seat_count' => 'number of reserved seats',
            'companions' => 'companions list',
            'companions.*' => 'companion name',
        ];
    }

    /**
     * Convert the request to a DTO.
     */
    public function toDto(): CreateCompanyBusTripBookingDto
    {
        return CreateCompanyBusTripBookingDto::fromArray($this->validated());
    }
}
