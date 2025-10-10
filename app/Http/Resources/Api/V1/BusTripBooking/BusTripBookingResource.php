<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\BusTripBooking;

use App\Http\Resources\Api\V1\BusTrip\BusTripResource;
use App\Http\Resources\Api\V1\TravelCompany\TravelCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class BusTripBookingResource extends JsonResource
{
    private bool $isDetailed = false;

    private bool $includePaymentDetails = false;

    private bool $includeIndexDetails = false;

    public function detailed(): self
    {
        $this->isDetailed = true;

        return $this;
    }

    public function withPaymentDetails(): self
    {
        $this->includePaymentDetails = true;

        return $this;
    }

    public function withIndexDetails(): self
    {
        $this->includeIndexDetails = true;

        return $this;
    }

    #[Override]
    public function toArray(Request $request): array
    {
        $baseAttributes = [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'reserved_seat_count' => $this->reserved_seat_count,
            'total_price' => $this->total_price,
            'reserved_seat_numbers' => $this->reserved_seat_numbers,
        ];

        if ($this->isDetailed) {
            return array_merge($baseAttributes, [
                'qr_code' => $this->qr_code_path,
                'bus_trip' => $this->whenLoaded('busTrip', fn(): BusTripResource => new BusTripResource($this->busTrip)),
                'travel_company' => $this->whenLoaded(
                    'busTrip.travelCompany',
                    fn(): TravelCompanyResource => new TravelCompanyResource($this->busTrip->travelCompany),
                ),
            ]);
        }

        if ($this->includePaymentDetails) {
            return array_merge($baseAttributes, [
                'name' => $this->whenLoaded('customer.user', fn() => $this->customer->user->name),
                'phone_number' => $this->whenLoaded('customer.user', fn() => $this->customer->user->phone_number),
                'status' => $this->booking_status,
            ]);
        }

        if ($this->includeIndexDetails) {
            return array_merge($baseAttributes, [
                'status' => $this->booking_status,
                'bus_trip' => $this->whenLoaded('busTrip', fn(): BusTripResource => new BusTripResource($this->busTrip->load([
                    'travelCompany',
                    'fromCity',
                    'toCity',
                ]))),
            ]);
        }

        return $baseAttributes;
    }
}
