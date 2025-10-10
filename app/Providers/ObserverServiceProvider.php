<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\{
    AppNotification,
    BusDriver,
    BusTrip,
    BusTripBooking,
    Customer,
    TravelCompanyCommission,
};
use App\Observers\{
    AppNotificationObserver,
    BusDriverObserver,
    BusTripObserver,
    BusTripBookingObserver,
    CustomerObserver,
    TravelCompanyCommissionObserver,
};
use Illuminate\Support\ServiceProvider;

final class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AppNotification::observe(AppNotificationObserver::class);
        Customer::observe(CustomerObserver::class);
        TravelCompanyCommission::observe(TravelCompanyCommissionObserver::class);
        BusDriver::observe(BusDriverObserver::class);
        BusTrip::observe(BusTripObserver::class);
        BusTripBooking::observe(BusTripBookingObserver::class);
    }
}
