<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Bus;
use App\Models\Customer;
use App\Models\TravelCompany;

class MainStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عدد الباصات', Bus::count()),
            Stat::make('عدد الزبائن', Customer::count()),
            Stat::make('عدد شركات النقل', TravelCompany::count()),
        ];
    }
}
