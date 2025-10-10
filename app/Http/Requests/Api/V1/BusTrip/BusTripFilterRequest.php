<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\BusTrip;

use App\DataTransferObjects\BusTrip\TripFilterDTO;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;
use App\Enum\TripsTypeEnum;
use Carbon\Carbon;
use App\Enum\TimeCategoryEnum;
use App\Enum\OrderByEnum;

final class BusTripFilterRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'from_city' => [
                'nullable',
                'integer',
                'exists:cities,id',
                'different:to_city',
            ],
            'to_city' => [
                'nullable',
                'integer',
                'exists:cities,id',
            ],
            'trip_type' => [
                'nullable',
                'string',
                'in:' . implode(',', array_column(TripsTypeEnum::cases(), 'value')),
            ],
            'departure_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'return_date' => [
                'nullable',
                'date',
                'after_or_equal:departure_date',
                'required_if:trip_type,' . TripsTypeEnum::TWO_WAY->value,
            ],
            'required_seats' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
            'min_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lt:max_price',
            ],
            'max_price' => [
                'nullable',
                'numeric',
                'gt:min_price',
                'max:10000',
            ],
            'time_category' => [
                'nullable',
                'string',
                'in:' . implode(',', array_column(TimeCategoryEnum::cases(), 'value')),
            ],
            'bus_type_id' => [
                'nullable',
                'integer',
                'exists:bus_types,id',
            ],
            'travel_company_id' => [
                'nullable',
                'integer',
                'exists:travel_companies,id',
            ],
            'order_by' => [
                'nullable',
                'string',
                'in:' . implode(',', array_column(OrderByEnum::cases(), 'value')),
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'from_city.required' => 'The :attribute is required.',
            'from_city.different' => 'The :attribute must be different from the destination city.',
            'to_city.required' => 'The :attribute is required.',
            'type.required' => 'The :attribute is required.',
            'departure_date.required' => 'The :attribute is required.',
            'departure_date.after_or_equal' => 'The :attribute must be today or a future date.',
            'return_date.after_or_equal' => 'The :attribute must be after or equal to departure date.',
            'return_date.required_if' => 'The :attribute is required for round trips.',
            'required_seats.required' => 'The :attribute is required.',
            'required_seats.min' => 'The :attribute must be at least :min.',
            'required_seats.max' => 'The :attribute cannot exceed :max.',
            'min_price.numeric' => 'The :attribute must be a number.',
            'min_price.min' => 'The :attribute must be positive.',
            'min_price.lt' => 'The :attribute must be less than the maximum price.',
            'max_price.numeric' => 'The :attribute must be a number.',
            'max_price.gt' => 'The :attribute must be greater than the minimum price.',
            'max_price.max' => 'The :attribute must not exceed 10000.',
            'time_category.in' => 'The :attribute must be a valid time category.',
            'bus_type_id.integer' => 'The :attribute must be an integer.',
            'travel_company_id.integer' => 'The :attribute must be an integer.',
            'order_by.in' => 'The :attribute must be a valid sort order.',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'from_city' => 'departure city',
            'to_city' => 'destination city',
            'type' => 'trip type',
            'departure_date' => 'departure date',
            'return_date' => 'return date',
            'required_seats' => 'number of required seats',
            'min_price' => 'minimum price',
            'max_price' => 'maximum price',
            'time_category' => 'time category',
            'bus_type_id' => 'bus type',
            'travel_company_id' => 'travel company',
            'order_by' => 'sort order',
        ];
    }

    public function toDto(): TripFilterDTO
    {
        return TripFilterDTO::fromArray($this->validated());
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if ($this->has('departure_date') && $this->input('departure_date') !== null) {
            $data['departure_datetime'] = $this->parseDateToDateTime($this->input('departure_date'));
        }

        if ($this->has('return_date') && $this->input('return_date') !== null) {
            $data['return_datetime'] = $this->parseDateToDateTime($this->input('return_date'));
        }

        $this->replace($data);
    }

    /**
     * Parse date to datetime with default time.
     */
    private function parseDateToDateTime(string $date): string
    {
        return Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s');
    }
}
