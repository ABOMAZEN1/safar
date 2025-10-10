<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\BusType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use App\Http\Resources\Api\V1\Bus\BusResource;

final class BusTypeResource extends JsonResource
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
            'name' => $this->name,
            'buses' => $this->whenLoaded('buses', fn () => BusResource::collection($this->buses)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
