<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'الرحلات',
        'plural_label' => 'الرحلات',
        'singular_label' => 'رحلة',
    ],
    'attributes' => [
        'departure_datetime' => 'موعد الانطلاق',
        'return_datetime' => 'موعد العودة',
        'duration_of_departure_trip' => 'فترة رحلة الذهاب (بالساعة)',
        'duration_of_return_trip' => 'فترة رحلة العودة (بالساعة)',
        'from_city_id' => 'من مدينة',
        'to_city_id' => 'الى مدينة',
        'bus_id' => 'الباص',
        'bus_driver_id' => 'السائق',
        'travel_company_id' => 'الشركة',
        'number_of_seats' => 'عدد المقاعد',
        'remaining_seats' => 'عدد المقاعد المتاحة',
        'ticket_price' => 'سعر التذكرة',
    ],
    'table' => [
        'columns' => [
            'id' => 'المعرف',
            'departure_datetime' => 'موعد الانطلاق',
            'return_datetime' => 'موعد العودة',
            'from_city' => 'من مدينة',
            'to_city' => 'الى مدينة',
            'bus' => 'الباص',
            'driver' => 'السائق',
            'travel_company' => 'الشركة',
            'duration_of_departure_trip' => 'فترة رحلة الذهاب',
            'duration_of_return_trip' => 'فترة رحلة العودة',
            'number_of_seats' => 'عدد المقاعد',
            'remaining_seats' => 'عدد المقاعد المتاحة',
            'ticket_price' => 'سعر التذكرة',
        ],
    ],
    'filters' => [
        'trip_type' => [
            'label' => 'نوع الرحلة',
            'options' => [
                'one_way' => 'ذهاب',
                'two_way' => 'ذهاب واياب',
            ],
        ],
    ],
];
