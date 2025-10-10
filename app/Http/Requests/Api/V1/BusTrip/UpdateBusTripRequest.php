<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusTrip;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;
use App\DataTransferObjects\BusTrip\UpdateBusTripDto;

final class UpdateBusTripRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'from_city_id' => [
                'numeric',
                'exists:cities,id',
            ],
            'to_city_id' => [
                'numeric',
                'exists:cities,id',
            ],
            'bus_id' => [
                'numeric',
                'exists:buses,id',
            ],
            'bus_driver_id' => [
                'numeric',
                'exists:bus_drivers,id',
            ],
            'departure_date' => [
                'date',
                'after_or_equal:today',
            ],
            'departure_time' => [
                'date_format:H:i',
            ],
            'return_date' => [
                'nullable',
                'date',
                'after_or_equal:departure_date',
                'required_with:return_time',
            ],
            'return_time' => [
                'nullable',
                'date_format:H:i',
                'required_with:return_date',
                'prohibited_unless:return_date,null',
            ],
            'departure_datetime' => [
                'date',
            ],
            'return_datetime' => [
                'nullable',
                'date',
                'after_or_equal:departure_datetime',
            ],
            'departure_trip_duration' => [
                'numeric',
                'min:0',
                'max:999.9',
            ],
            'return_trip_duration' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.9',
            ],
            'trip_type' => [
                'string',
                'in:one_way,two_way',
            ],
            'number_of_seats' => [
                'numeric',
                'min:1',
                'max:999',
            ],
            'ticket_price' => [
                'numeric',
                'min:0',
                'max:99999999.99',
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
            'from_city_id' => 'From City',
            'to_city_id' => 'To City',
            'bus_id' => 'Bus',
            'bus_driver_id' => 'Bus Driver',
            'departure_datetime' => 'Departure Date and Time',
            'return_datetime' => 'Return Date and Time',
            'departure_trip_duration' => 'Departure Trip Duration',
            'return_trip_duration' => 'Return Trip Duration',
            'departure_date' => 'Departure Date',
            'departure_time' => 'Departure Time',
            'return_date' => 'Return Date',
            'return_time' => 'Return Time',
            'trip_type' => 'Trip Type',
            'number_of_seats' => 'Number of Seats',
            'ticket_price' => 'Ticket Price',
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
            'from_city_id.numeric' => 'The :attribute must be a number.',
            'from_city_id.exists' => 'The :attribute does not exist.',
            'to_city_id.numeric' => 'The :attribute must be a number.',
            'to_city_id.exists' => 'The :attribute does not exist.',
            'bus_id.numeric' => 'The :attribute must be a number.',
            'bus_id.exists' => 'The :attribute does not exist.',
            'bus_driver_id.numeric' => 'The :attribute must be a number.',
            'bus_driver_id.exists' => 'The :attribute does not exist.',
            'departure_date.date' => 'The :attribute must be a valid date.',
            'departure_date.after_or_equal' => 'The :attribute must be today or a future date.',
            'departure_time.date_format' => 'The :attribute must be in the format HH:MM.',
            'return_date.date' => 'The :attribute must be a valid date.',
            'return_date.after_or_equal' => 'The :attribute must be after or equal to the departure date.',
            'return_date.required_with' => 'The :attribute field is required when return time is present.',
            'return_time.date_format' => 'The :attribute must be in the format HH:MM.',
            'return_time.required_with' => 'The :attribute field is required when return date is present.',
            'return_time.prohibited_unless' => 'The :attribute field is prohibited unless return date is null.',
            'departure_datetime.date' => 'The :attribute must be a valid date.',
            'return_datetime.date' => 'The :attribute must be a valid date.',
            'return_datetime.after_or_equal' => 'The :attribute must be after or equal to the departure date and time.',
            'departure_trip_duration.numeric' => 'The :attribute must be a number.',
            'departure_trip_duration.min' => 'The :attribute must be at least 0.',
            'departure_trip_duration.max' => 'The :attribute must be less than or equal to 999.9.',
            'return_trip_duration.numeric' => 'The :attribute must be a number.',
            'return_trip_duration.min' => 'The :attribute must be at least 0.',
            'return_trip_duration.max' => 'The :attribute must be less than or equal to 999.9.',
            'trip_type.string' => 'The :attribute must be a string.',
            'trip_type.in' => 'The :attribute must be one of the following types: one_way, two_way.',
            'number_of_seats.numeric' => 'The :attribute must be a number.',
            'number_of_seats.min' => 'The :attribute must be at least 1.',
            'number_of_seats.max' => 'The :attribute must be less than or equal to 999.',
            'ticket_price.numeric' => 'The :attribute must be a number.',
            'ticket_price.min' => 'The :attribute must be at least 0.',
            'ticket_price.max' => 'The :attribute must be less than or equal to 99999999.99.',
        ];
    }

    /**
     * Convert the validated request data to a DTO.
     */
    public function toDto(): UpdateBusTripDto
    {
        return UpdateBusTripDto::fromArray($this->validated());
    }

    /**
     * Prepare the data for validation.
     *
     * This method processes the request data by combining separate date and time fields
     * into a single datetime field for both departure and return trips. It then removes
     * the original date and time fields from the request data.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if ($this->has(['departure_date', 'departure_time'])) {
            $data['departure_datetime'] = Carbon::parse($this->input('departure_date'))
                ->setTimeFromTimeString($this->input('departure_time'))
                ->format('Y-m-d H:i:s');
        }

        if ($this->has(['return_date', 'return_time'])) {
            $data['return_datetime'] = Carbon::parse($this->input('return_date'))
                ->setTimeFromTimeString($this->input('return_time'))
                ->format('Y-m-d H:i:s');
        }

        unset($data['departure_date'], $data['departure_time'], $data['return_date'], $data['return_time']);

        $this->replace($data);
    }
}
