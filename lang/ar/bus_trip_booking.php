<?php

declare(strict_types=1);

return [
    'attributes' => [
        'id' => 'المعرف',
        'customer_id' => 'العميل',
        'bus_trip_id' => 'الرحلة',
        'reserved_seat_count' => 'المقاعد المحجوزة',
        'qr_code_path' => 'رمز QR',
        'is_departure_confirmed' => 'تأكيد المغادرة',
        'is_return_confirmed' => 'تأكيد العودة',
        'booking_status' => 'الحالة',
        'total_price' => 'السعر الإجمالي',
        'reserved_seat_numbers' => 'أرقام المقاعد',
    ],
    'relationships' => [
        'bus_trip' => [
            'departure_datetime' => 'موعد المغادرة',
            'duration_of_departure_trip' => 'مدة رحلة الذهاب',
            'trip_type' => 'نوع الرحلة',
            'return_datetime' => 'موعد العودة',
            'duration_of_return_trip' => 'مدة رحلة العودة',
        ],
        'travel_company' => [
            'id' => 'معرف الشركة',
            'company_name' => 'اسم الشركة',
        ],
    ],
];
