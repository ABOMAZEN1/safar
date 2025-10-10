<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Bus;
use App\Models\BusTrip;
use App\Models\BusTripBooking;
use App\Policies\BusPolicy;
use App\Policies\TripPolicy;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Bus::class => BusPolicy::class,
        BusTrip::class => TripPolicy::class,
        BusTripBooking::class => BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
