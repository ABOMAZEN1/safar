<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\BusTrip;

use Override;
use App\Models\BusTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enum\BusTripStatusEnum;
use App\Http\Resources\Api\V1\Bus\BusResource;
use App\Http\Resources\Api\V1\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\BusType\BusTypeResource;
use App\Http\Resources\Api\V1\BusDriver\BusDriverResource;
use App\Http\Resources\Api\V1\TravelCompany\TravelCompanyResource;

/**
 * @property BusTrip $resource
 */
final class BusTripResource extends JsonResource
{
    private bool $isDriverView = false;

    private bool $isCompanyView = false;

    private bool $isCreateDetails = false;

    public function asDriverView(): self
    {
        $this->isDriverView = true;

        return $this;
    }

    public function asCompanyView(): self
    {
        $this->isCompanyView = true;

        return $this;
    }

    public function asCreateDetails(): self
    {
        $this->isCreateDetails = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        if ($this->isCreateDetails) {
            return $this->toCreateDetailsArray();
        }

        $baseAttributes = $this->getBaseAttributes();

        if ($this->isDriverView) {
            return array_merge($baseAttributes, $this->getDriverViewAttributes());
        }

        if ($this->isCompanyView) {
            return array_merge($baseAttributes, $this->getCompanyViewAttributes());
        }

        return array_merge($baseAttributes, $this->getDefaultAttributes());
    }

    /**
     * @return array<string, int|string|float|null>
     */
    private function getBaseAttributes(): array
    {
        $departureDateTime = $this->resource->departure_datetime ? Carbon::parse($this->resource->departure_datetime) : null;
        $returnDateTime = $this->resource->return_datetime ? Carbon::parse($this->resource->return_datetime) : null;

        return [
            'id' => $this->resource->id,
            'from_city_id' => $this->resource->from_city_id,
            'to_city_id' => $this->resource->to_city_id,
            'bus_id' => $this->resource->bus_id,
            'bus_driver_id' => $this->resource->bus_driver_id,
            'travel_company_id' => $this->resource->travel_company_id,
            'departure_date' => $departureDateTime?->format('Y-m-d'),
            'departure_time' => $departureDateTime?->format('H:i:s'),
            'return_date' => $returnDateTime?->format('Y-m-d'),
            'return_time' => $returnDateTime?->format('H:i:s'),
            'duration_of_departure_trip' => $this->resource->duration_of_departure_trip,
            'duration_of_return_trip' => $this->resource->duration_of_return_trip,
            'trip_type' => $this->resource->trip_type,
            'number_of_seats' => $this->resource->number_of_seats,
            'remaining_seats' => $this->resource->remaining_seats,
            'ticket_price' => (float) $this->resource->ticket_price,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getDriverViewAttributes(): array
    {
        return [
            'from_city' => $this->whenLoaded(
                'fromCity',
                fn(): CityResource => new CityResource($this->resource->fromCity),
            ),
            'to_city' => $this->whenLoaded(
                'toCity',
                fn(): CityResource => new CityResource($this->resource->toCity),
            ),
            'reserved_seats' => $this->resource->number_of_seats - $this->resource->remaining_seats,
            'total_bookings' => $this->whenLoaded(
                'bookings',
                fn(): int => $this->resource->bookings->count(),
            ),
            'departure_confirmed_bookings' => $this->whenLoaded(
                'bookings',
                fn(): int => $this->resource->bookings->where('is_departure_confirmed', true)->count(),
            ),
            'return_confirmed_bookings' => $this->whenLoaded(
                'bookings',
                fn(): int => $this->resource->bookings->where('is_return_confirmed', true)->count(),
            ),
        ];
    }

    /**
     * Get attributes specific to company view.
     *
     * @return array<string, mixed>
     */
    private function getCompanyViewAttributes(): array
    {
        return [
            'ticket_price' => (float) $this->resource->ticket_price,
            'status' => $this->getTripStatus(),
            'bus_type' => $this->whenLoaded(
                'bus',
                fn(): string => $this->resource->bus->busType->name,
            ),
            'from_city' => $this->whenLoaded(
                'fromCity',
                fn(): CityResource => new CityResource($this->resource->fromCity),
            ),
            'to_city' => $this->whenLoaded(
                'toCity',
                fn(): CityResource => new CityResource($this->resource->toCity),
            ),
            'travel_company' => $this->whenLoaded(
                'travelCompany',
                fn(): TravelCompanyResource => new TravelCompanyResource($this->resource->travelCompany),
            ),
            'bus' => $this->whenLoaded(
                'bus',
                fn(): BusResource => new BusResource($this->resource->bus),
            ),
            'bus_driver' => $this->whenLoaded(
                'busDriver',
                fn(): BusDriverResource => new BusDriverResource($this->resource->busDriver),
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultAttributes(): array
    {
        return [
            'from_city' => $this->whenLoaded('fromCity', fn(): CityResource => new CityResource($this->resource->fromCity)),
            'to_city' => $this->whenLoaded('toCity', fn(): CityResource => new CityResource($this->resource->toCity)),
            'travel_company' => $this->whenLoaded('travelCompany', fn(): TravelCompanyResource => new TravelCompanyResource($this->resource->travelCompany)),
            'bus_type' => $this->whenLoaded('bus', fn(): BusTypeResource => new BusTypeResource($this->resource->bus->busType)),
            'ticket_price' => (float) $this->resource->ticket_price,
        ];
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function toCreateDetailsArray(): array
    {
        return [
            'cities' => collect($this->resource['cities'])->map(static fn($city): array => [
                'id' => $city->id,
                'name' => app()->getLocale() === 'ar' ? $city->name_ar : $city->name_en,
            ])->all(),
            'buses' => collect($this->resource['buses'])->map(static fn($bus): array => [
                'id' => $bus->id,
                'details' => $bus->details,
            ])->all(),
            'drivers' => collect($this->resource['drivers'])->map(static fn($driver): array => [
                'id' => $driver->id,
                'name' => $driver->user->name,
            ])->all(),
            'commission' => [
                'commission_amount' => $this->resource['commission']['commission_amount'] ?? 0,
            ],
        ];
    }

    private function getTripStatus(): string
    {
        if ($this->resource->departure_datetime < now()) {
            return BusTripStatusEnum::COMPLETED->value;
        }

        if ($this->resource->remaining_seats === 0) {
            return BusTripStatusEnum::CANCELED->value;
        }

        return BusTripStatusEnum::ACTIVE->value;
    }
}
