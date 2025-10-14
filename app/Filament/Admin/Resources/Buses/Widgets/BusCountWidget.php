<?php

namespace App\Filament\Admin\Resources\Buses\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Bus;

class BusCountWidget extends StatsOverviewWidget
{       protected int|string|array $columnSpan = [
    'default' => 1,
    'md' => 1,
    'lg' => 1,
];// 👈 هذا يجعلها تأخذ عمود واحد فقط

    protected function getStats(): array
    {
        return [
            Stat::make('عدد الباصات', Bus::count()),
        ];
    }
}

