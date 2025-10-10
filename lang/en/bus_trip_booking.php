<?php

declare(strict_types=1);

return [
    'attributes' => [
        'id' => 'ID',
        'customer_id' => 'Customer',
        'bus_trip_id' => 'Trip',
        'reserved_seat_count' => 'Reserved Seats',
        'qr_code_path' => 'QR Code',
        'is_departure_confirmed' => 'Departure Confirmed',
        'is_return_confirmed' => 'Return Confirmed',
        'booking_status' => 'Status',
        'total_price' => 'Total Price',
        'reserved_seat_numbers' => 'Seat Numbers',
    ],
    'relationships' => [
        'bus_trip' => [
            'departure_datetime' => 'Departure Date & Time',
            'duration_of_departure_trip' => 'Departure Trip Duration',
            'trip_type' => 'Trip Type',
            'return_datetime' => 'Return Date & Time',
            'duration_of_return_trip' => 'Return Trip Duration',
        ],
        'travel_company' => [
            'id' => 'Company ID',
            'company_name' => 'Company Name',
        ],
    ],
];
