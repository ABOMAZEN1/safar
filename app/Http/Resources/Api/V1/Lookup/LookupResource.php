<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Lookup;

use App\Http\Resources\Api\V1\BusType\BusTypeResource;
use App\Http\Resources\Api\V1\City\CityResource;
use App\Http\Resources\Api\V1\TravelCompany\TravelCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class LookupResource extends JsonResource
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
            'active_companies' => TravelCompanyResource::collection($this->resource['active_companies']),
            'bus_types' => BusTypeResource::collection($this->resource['bus_types']),
            'cities' => CityResource::collection($this->resource['cities']),
        ];
    }
}
