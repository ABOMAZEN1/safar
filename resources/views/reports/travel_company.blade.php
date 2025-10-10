<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير أداء الشركة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; direction: rtl; }
        .card { border: 1px solid #ddd; padding: 16px; margin-bottom: 16px; }
        .row { display: flex; gap: 16px; }
        .col { flex: 1; }
        h1 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: right; }
    </style>
    </head>
<body>
    <h1>تقرير أداء الشركة</h1>
    <div class="card">
        <div>اسم الشركة: {{ $company->company_name }}</div>
        <div>رقم الاتصال: {{ $company->contact_number }}</div>
        <div>العنوان: {{ $company->address }}</div>
    </div>
    <div class="row">
        <div class="col card">عدد الرحلات: {{ $metrics['trips_count'] }}</div>
        <div class="col card">عدد الباصات: {{ $metrics['buses_count'] }}</div>
        <div class="col card">عدد الزبائن: {{ $metrics['customers_count'] }}</div>
        <div class="col card">الإيرادات: {{ number_format($metrics['revenue'], 0) }}</div>
    </div>

    <h2>تفاصيل الرحلات</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>من</th>
                <th>إلى</th>
                <th>وقت الانطلاق</th>
                <th>نوع الرحلة</th>
                <th>الحافلة</th>
                <th>السعر</th>
                <th>المقاعد المتبقية</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trips as $i => $trip)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ optional($trip->fromCity)->name }}</td>
                    <td>{{ optional($trip->toCity)->name }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($trip->departure_datetime)->format('Y-m-d H:i') }}</td>
                    <td>{{ $trip->trip_type }}</td>
                    <td>{{ optional($trip->bus)->model }}</td>
                    <td>{{ number_format((float) $trip->ticket_price, 0) }}</td>
                    <td>{{ $trip->remaining_seats }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center">لا توجد رحلات</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
