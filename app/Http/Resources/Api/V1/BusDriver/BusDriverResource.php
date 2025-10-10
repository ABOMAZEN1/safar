<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\BusDriver;

use App\Http\Resources\Api\V1\User\UserResource;
use App\Http\Resources\Api\V1\TravelCompany\TravelCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class BusDriverResource extends JsonResource
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
            'name' => $this->whenLoaded('user', fn() => $this->user->name),
            'phone_number' => $this->whenLoaded('user', fn() => $this->user->phone_number),
            'user' => $this->when(
                $this->relationLoaded('user') && $request->has('include_user_details'),
                fn(): UserResource => new UserResource($this->user)
            ),
            'travel_company' => $this->whenLoaded(
                'travelCompany',
                fn(): TravelCompanyResource => new TravelCompanyResource($this->travelCompany)
            ),
        ];
    }
}
