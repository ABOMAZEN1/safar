<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BusTrip;
use App\Models\User;

final class TripPolicy
{
    public function edit(User $user, BusTrip $busTrip): bool
    {
        return $user->company->id === $busTrip->travel_company_id;
    }

    public function view(User $user, BusTrip $busTrip): bool
    {
        return $user->company->id === $busTrip->travel_company_id;
    }
}
