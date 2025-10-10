<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\TravelCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

final class CompanyInformationResource extends JsonResource
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
            'name' => $this->company_name,
            'address' => $this->address,
            'contact_number' => $this->contact_number,
            'image' => $this->image_path,
        ];
    }
}
