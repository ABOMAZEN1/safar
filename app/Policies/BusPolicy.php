<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Bus;
use App\Models\User;

final class BusPolicy
{
    public function update(User $user, Bus $bus): bool
    {
        return $user->company?->id === $bus->travel_company_id;
    }
}
