<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\City;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class CityResource extends JsonResource
{
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en,
        ];
    }
}
