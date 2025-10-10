<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\CompanyBooking;

use Override;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\TravelCompanion\BookingCompanionResource;

final class TravelCompanyBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->whenLoaded('customer', static fn ($customer): array => [
                'id' => $customer->id,
                'name' => $customer->user->name,
                'phone_number' => $customer->user->phone_number,
                'national_id' => $customer->national_id,
                'gender' => $customer->gender,
                'birth_date' => $customer->birth_date,
                'address' => $customer->address,
                'mother_name' => $customer->mother_name,
            ]),
            'reserved_seat_count' => $this->reserved_seat_count,
            'total_price' => $this->total_price,
            'reserved_seat_numbers' => $this->reserved_seat_numbers,
            'is_departure_confirmed' => $this->is_departure_confirmed,
            'is_return_confirmed' => $this->is_return_confirmed,
            'booking_status' => $this->booking_status,
            'companions' => $this->whenLoaded('companions', static fn ($companion) => BookingCompanionResource::collection($companion)),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
