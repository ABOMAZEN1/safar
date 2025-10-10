<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Bus;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class BusResource extends JsonResource
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
            'details' => $this->details,
            'bus_type' => $this->whenLoaded('busType', fn () => $this->busType->name),
            'capacity' => $this->capacity,
        ];
    }
}
