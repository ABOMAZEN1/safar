<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BusTripBooking;
use App\Models\User;

final class BookingPolicy
{
    public function view(User $user, BusTripBooking $busTripBooking): bool
    {
        return $user->customer?->id === $busTripBooking->customer_id;
    }

    public function cancel(User $user, BusTripBooking $busTripBooking): bool
    {
        return $user->customer?->id === $busTripBooking->customer_id;
    }
}
