<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;

class CustomerCountWidget extends ChartWidget
{
    protected ?string $heading = 'تزايد الزبائن الشهري';

    protected function getData(): array
    {
        // Get number of new customers per month for the last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->copy()->subMonths($i)->startOfMonth();
            $months->push($date);
        }

        $labels = [];
        $counts = [];

        foreach ($months as $date) {
            $year = $date->format('Y');
            $month = $date->format('m');

            // حساب عدد الزبائن المسجلين خلال هذا الشهر
            $count = Customer::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $labels[] = $date->translatedFormat('F Y');
            $counts[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الزبائن الجدد',
                    'data' => $counts,
                    'borderColor' => '#262262',
                    'fill' => false,
                ]
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
