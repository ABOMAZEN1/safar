<?php

namespace App\Filament\Admin\Resources\Customers\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;

class CustomerNumbersWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $customersCount = Customer::count();

        return [
            Stat::make('عدد الزبائن', $customersCount),
        ];
    }
}
