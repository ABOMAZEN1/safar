<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Book;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class CancelBusTripBookingRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bus_trip_bookings', 'id')->where(function ($query): void {
                    $query->where('customer_id', Auth::user()->customer?->id ?? 0);
                }),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $bookingId = (int) $this->route('bookingId');
        $this->merge(['booking_id' => $bookingId]);
    }

    /**
     * Get custom error messages for validation failures.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'booking_id.required' => __('messages.errors.booking.booking_id_required'),
            'booking_id.exists' => __('messages.errors.booking.booking_not_found'),
        ];
    }
}
