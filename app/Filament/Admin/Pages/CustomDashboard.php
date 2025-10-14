<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\MainStatsWidget;
use App\Filament\Admin\Resources\TravelCompanies\Widgets\CompanyTripsCountWidget;
use App\Filament\Admin\Widgets\CustomerCountWidget;
use Filament\Pages\Dashboard;

class CustomDashboard extends Dashboard
{
    protected static ?string $resource = null;

    protected static ?string $navigationLabel = 'لوحة التحكم الرئيسية';
    protected static ?string $title = 'لوحة التحكم — إحصائيات الموقع';

    public function getWidgets(): array
    {
        return [
            MainStatsWidget::class, // 👈 هذا يعرض الثلاثة جنب بعض
            CustomerCountWidget::class,
            CompanyTripsCountWidget::class,
        ];
    }
}
