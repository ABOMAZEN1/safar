<?php

namespace App\Filament\Admin\Widgets;

use App\Models\TravelCompany;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyNumbersWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $Companycount = TravelCompany::count();

        return [
            Stat::make('عدد شركات النقل ', $Companycount),
        ];
    }
}
