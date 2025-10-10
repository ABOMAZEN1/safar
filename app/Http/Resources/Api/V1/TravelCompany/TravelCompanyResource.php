<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\TravelCompany;

use Override;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Bus\BusResource;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\BusTrip\BusTripResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\TravelCompany;

final class TravelCompanyResource extends JsonResource
{
    #[Override]
    /**
     * Summary of toArray
     * @param \Illuminate\Http\Request $request
     * @return array{address: mixed, bus_trips: mixed|\Illuminate\Http\Resources\MissingValue, buses: mixed|\Illuminate\Http\Resources\MissingValue, company_name: mixed, contact_number: mixed, id: mixed, image: mixed, user: mixed|\Illuminate\Http\Resources\MissingValue}
     */
    public function toArray(Request $request): array
    {
        /** @var TravelCompany $this->resource */
        return [
            'id' => $this->resource->id,
            'company_name' => $this->resource->company_name,
            'contact_number' => $this->resource->contact_number,
            'address' => $this->resource->address,
            'image' => $this->resource->image_path,

            // Relations
            'user' => $this->whenLoaded(
                relationship: 'user',
                value: fn(): UserResource => new UserResource($this->user),
            ),

            'bus_trips' => $this->whenLoaded(
                relationship: 'busTrips',
                value: fn(): AnonymousResourceCollection => BusTripResource::collection($this->trips),
            ),

            'buses' => $this->whenLoaded(
                relationship: 'buses',
                value: fn(): AnonymousResourceCollection => BusResource::collection($this->buses),
            ),
        ];
    }
}
