<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TravelCompany;

class CompanyTripsCountWidget extends ChartWidget
{
    protected ?string $heading = 'أكثر 3 شركات نقل لديها رحلات';

    protected function getData(): array
    {
        // جلب أكثر 3 شركات بعدد الرحلات (مع اسم الشركة وعدد رحلاتها)
        $topCompanies = TravelCompany::withCount('busTrips')
            ->orderByDesc('bus_trips_count')
            ->take(3)
            ->get();

        // labels: اسم الشركة
        $labels = $topCompanies->pluck('company_name')->toArray();
        // values: عدد الرحلات
        $data = $topCompanies->pluck('bus_trips_count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'عدد الرحلات',
                    'data' => $data,
                    'backgroundColor' => [
                        '#262262',
                        '#3b3f83',
                        '#7276aa'
                    ]
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
