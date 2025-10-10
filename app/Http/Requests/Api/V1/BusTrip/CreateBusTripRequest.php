<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusTrip;

use Override;
use Carbon\Carbon;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\DataTransferObjects\BusTrip\CreateBusTripDto;

/**
 * Class CreateBusTripRequest.
 *
 * Handles the validation and preparation of data for creating a bus trip.
 *
 * @method bool has(array|string $key) Determine if the request contains a given input item.
 * @method void merge(array $input)    Merge new input into the request's existing input.
 */
final class CreateBusTripRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>> The array of validation rules.
     */
    public function rules(): array
    {
        return [
            'from_city_id' => [
                'required',
                'numeric',
                'exists:cities,id',
            ],
            'to_city_id' => [
                'required',
                'numeric',
                'exists:cities,id',
                'different:from_city_id',
            ],
            'bus_id' => [
                'required',
                'numeric',
                'exists:buses,id',
            ],
            'bus_driver_id' => [
                'required',
                'numeric',
                'exists:bus_drivers,id',
            ],
            'departure_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'departure_time' => [
                'required',
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
                'required',
                'date',
            ],
            'return_datetime' => [
                'nullable',
                'date',
                'after_or_equal:departure_datetime',
            ],
            'duration_of_departure_trip' => [
                'required',
                'numeric',
                'min:0',
                'max:999.9',
            ],
            'duration_of_return_trip' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.9',
            ],
            'trip_type' => [
                'required',
                'string',
                'in:one_way,two_way',
            ],
            'number_of_seats' => [
                'required',
                'numeric',
                'min:1',
                'max:999',
            ],
            'ticket_price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string> The array of attribute names.
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
            'duration_of_departure_trip' => 'Duration of Departure Trip',
            'duration_of_return_trip' => 'Duration of Return Trip',
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
     * @return array<string, string> The array of custom error messages.
     */
    #[Override]
    public function messages(): array
    {
        return [
            'from_city_id.required' => 'The :attribute field is required.',
            'from_city_id.numeric' => 'The :attribute must be a number.',
            'from_city_id.exists' => 'The :attribute does not exist.',
            'to_city_id.required' => 'The :attribute field is required.',
            'to_city_id.numeric' => 'The :attribute must be a number.',
            'to_city_id.exists' => 'The :attribute does not exist.',
            'bus_id.required' => 'The :attribute field is required.',
            'bus_id.numeric' => 'The :attribute must be a number.',
            'bus_id.exists' => 'The :attribute does not exist.',
            'bus_driver_id.required' => 'The :attribute field is required.',
            'bus_driver_id.numeric' => 'The :attribute must be a number.',
            'bus_driver_id.exists' => 'The :attribute does not exist.',
            'departure_date.required' => 'The :attribute field is required.',
            'departure_date.date' => 'The :attribute must be a valid date.',
            'departure_date.after_or_equal' => 'The :attribute must be today or a future date.',
            'departure_time.required' => 'The :attribute field is required.',
            'departure_time.date_format' => 'The :attribute must be in the format HH:MM.',
            'return_date.date' => 'The :attribute must be a valid date.',
            'return_date.after_or_equal' => 'The :attribute must be after or equal to the departure date.',
            'return_date.required_with' => 'The :attribute field is required when return time is present.',
            'return_time.date_format' => 'The :attribute must be in the format HH:MM.',
            'return_time.required_with' => 'The :attribute field is required when return date is present.',
            'return_time.prohibited_unless' => 'The :attribute field is prohibited unless return date is null.',
            'departure_datetime.required' => 'The :attribute field is required.',
            'departure_datetime.date' => 'The :attribute must be a valid date.',
            'return_datetime.date' => 'The :attribute must be a valid date.',
            'return_datetime.after_or_equal' => 'The :attribute must be after or equal to Departure Date and Time.',
            'duration_of_departure_trip.required' => 'The :attribute field is required.',
            'duration_of_departure_trip.numeric' => 'The :attribute must be a number.',
            'duration_of_departure_trip.min' => 'The :attribute must be at least 0.',
            'duration_of_departure_trip.max' => 'The :attribute must be less than or equal to 999.9.',
            'duration_of_return_trip.numeric' => 'The :attribute must be a number.',
            'duration_of_return_trip.min' => 'The :attribute must be at least 0.',
            'duration_of_return_trip.max' => 'The :attribute must be less than or equal to 999.9.',
            'trip_type.required' => 'The :attribute field is required.',
            'trip_type.string' => 'The :attribute must be a string.',
            'trip_type.in' => 'The :attribute must be one of the following types: one_way, two_way.',
            'number_of_seats.required' => 'The :attribute field is required.',
            'number_of_seats.numeric' => 'The :attribute must be a number.',
            'number_of_seats.min' => 'The :attribute must be at least 1.',
            'number_of_seats.max' => 'The :attribute must be less than or equal to 999.',
            'ticket_price.required' => 'The :attribute field is required.',
            'ticket_price.numeric' => 'The :attribute must be a number.',
            'ticket_price.min' => 'The :attribute must be at least 0.',
            'ticket_price.max' => 'The :attribute must be less than or equal to 99999999.99.',
        ];
    }

    public function toDto(): CreateBusTripDto
    {
        return CreateBusTripDto::fromArray($this->validated());
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

        $this->replace($data);
    }
}
