<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Models\TravelCompany;
use Mpdf\Mpdf;
use Illuminate\Support\Carbon;

class ListTravelCompanies extends ListRecords
{
    protected static string $resource = TravelCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('pdf_report_all')
                ->label('توليد تقرير PDF لجميع الشركات')
                ->icon('heroicon-o-document-text')
                ->action(function () {

                    $companies = TravelCompany::with([
                        'buses',
                        'busTrips.bookings',
                        'busTrips.bus',
                        'busTrips.fromCity',
                        'busTrips.toCity'
                    ])->get();

                    $html = '
                    <!doctype html>
                    <html lang="ar" dir="rtl">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>تقرير أداء جميع الشركات</title>
                        <style>
                            @import url("https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;700&display=swap");
                            body { font-family: "Cairo", cairo, Arial, sans-serif; direction: rtl; line-height: 1.4; color: #333; }
                            .company { border: 1px solid #ddd; padding: 16px; margin-bottom: 32px; border-radius: 8px; }
                            .row { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 8px; }
                            .col { flex: 1; min-width: 150px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 12pt; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                            th { background-color: #f5f5f5; }
                        </style>
                    </head>
                    <body>
                        <h1>تقرير أداء جميع الشركات</h1>';

                    foreach ($companies as $company) {
                        $trips = $company->busTrips;

                        $trips_count = $trips->count();
                        $buses_count = $company->buses->count();
                        $customers_count = $trips->sum(fn($trip) => $trip->bookings->sum('reserved_seat_count'));
                        $revenue = $trips->sum(fn($trip) => $trip->bookings->sum('total_price'));

                        $html .= '<div class="company">
                            <h2>'.$company->company_name.'</h2>
                            <div><strong>رقم الاتصال:</strong> '.$company->contact_number.'</div>
                            <div><strong>العنوان:</strong> '.$company->address.'</div>
                            <div class="row">
                                <div class="col"><strong>عدد الرحلات:</strong> '.$trips_count.'</div>
                                <div class="col"><strong>عدد الباصات:</strong> '.$buses_count.'</div>
                                <div class="col"><strong>عدد الزبائن:</strong> '.$customers_count.'</div>
                                <div class="col"><strong>الإيرادات:</strong> '.number_format($revenue, 2).'</div>
                            </div>';

                        $html .= '<table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>وقت الانطلاق</th>
                                    <th>نوع الرحلة</th>
                                    <th>الحافلة</th>
                                    <th>عدد المقاعد</th>
                                    <th>المقاعد المتبقية</th>
                                    <th>السعر</th>
                                </tr>
                            </thead>
                            <tbody>';

                        foreach ($trips as $i => $trip) {
                            $tripType = ($trip->trip_type === 'one_way') ? 'ذهاب' : 'ذهاب واياب';
                            $html .= '<tr>
                                <td>'.($i+1).'</td>
                                <td>'.($trip->fromCity->name ?? '').'</td>
                                <td>'.($trip->toCity->name ?? '').'</td>
                                <td>'.Carbon::parse($trip->departure_datetime)->format('Y-m-d H:i').'</td>
                                <td>'.$tripType.'</td>
                                <td>'.($trip->bus->details ?? '').'</td>
                                <td>'.$trip->number_of_seats.'</td>
                                <td>'.$trip->remaining_seats.'</td>
                                <td>'.number_format((float)$trip->ticket_price, 2).'</td>
                            </tr>';
                        }

                        if ($trips->isEmpty()) {
                            $html .= '<tr><td colspan="9" style="text-align:center">لا توجد رحلات</td></tr>';
                        }

                        $html .= '</tbody></table></div>';
                    }

                    $html .= '</body></html>';

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'default_font' => 'Cairo',
                    ]);
                    $mpdf->WriteHTML($html);

                    return response()->streamDownload(function () use ($mpdf) {
                        echo $mpdf->Output('', 'S');
                    }, 'all_companies_report.pdf');
                }),
        ];
    }
}
