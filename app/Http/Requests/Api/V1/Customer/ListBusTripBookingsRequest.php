<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use Override;
use App\Enum\UserBookingsStatus;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use App\DataTransferObjects\BusTripBooking\ListBusTripBookingsDto;

final class ListBusTripBookingsRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'nullable',
                'string',
                'in:' . implode(',', array_column(UserBookingsStatus::cases(), 'value')),
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
            'status' => 'booking status',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'status.in' => 'The :attribute must be one of: ' . implode(', ', array_column(UserBookingsStatus::cases(), 'value')) . '.',
        ];
    }

    /**
     * Transform the validated data into a DTO.
     */
    public function toDto(): ListBusTripBookingsDto
    {
        return ListBusTripBookingsDto::fromArray($this->validated());
    }
}
