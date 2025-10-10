<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Customer\Book;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BusTripBooking;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\BusTrip\BusTripResource;
use App\Http\Resources\Api\V1\Customer\CustomerResource;
use Override;

/**
 * @property BusTripBooking $resource
 */
final class BookQrResource extends JsonResource
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
            'id' => $this->resource->id,
            'qr_code' => $this->resource->qr_code_path,
            'is_departure_confirmed' => $this->resource->is_departure_confirmed,
            'is_return_confirmed' => $this->resource->is_return_confirmed,
            'booking_status' => $this->resource->booking_status,
            'reserved_seat_count' => $this->resource->reserved_seat_count,
            'reserved_seat_numbers' => $this->resource->reserved_seat_numbers,
            'total_price' => (float) $this->resource->total_price,
            'customer' => $this->whenLoaded(
                'customer',
                fn() => CustomerResource::make($this->resource->customer)
            ),
            'bus_trip' => $this->whenLoaded(
                'busTrip',
                fn() => BusTripResource::make($this->resource->busTrip)
            ),
        ];
    }
}
