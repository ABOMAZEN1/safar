<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Customer;

use Override;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

final class CustomerResource extends JsonResource
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
            'mother_name' => $this->mother_name,
            'birth_date' => $this->birth_date,
            'national_id' => $this->national_id,
            'gender' => $this->gender,
            'address' => $this->address,
            'user' => $this->whenLoaded('user', fn (): UserResource => UserResource::make($this->user), null),
        ];
    }
}
