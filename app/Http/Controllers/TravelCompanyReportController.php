<?php

namespace App\Http\Controllers;

use App\Models\TravelCompany;
use Mpdf\Mpdf;
use Illuminate\Http\Request;

class TravelCompanyReportController extends Controller
{
    public function generate(TravelCompany $company)
    {
        $company->load(['buses', 'busTrips.bookings', 'busTrips.bus', 'busTrips.fromCity', 'busTrips.toCity']);
        $trips = $company->busTrips;

        $trips_count = $trips->count();
        $buses_count = $company->buses->count();
        $customers_count = $trips->sum(fn($trip) => $trip->bookings->sum('reserved_seat_count'));
        $revenue = $trips->sum(fn($trip) => $trip->bookings->sum('total_price'));

        $html = view('reports.travel_company', compact('company', 'trips', 'trips_count', 'buses_count', 'customers_count', 'revenue'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Cairo',
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('report.pdf', 'I');
    }
}
