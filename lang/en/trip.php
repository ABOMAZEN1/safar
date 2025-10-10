<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Trips',
        'plural_label' => 'Trips',
        'singular_label' => 'Trip',
    ],
    'attributes' => [
        'departure_datetime' => 'Departure Date & Time',
        'return_datetime' => 'Return Date & Time',
        'duration_of_departure_trip' => 'Duration of Departure Trip (Hours)',
        'duration_of_return_trip' => 'Duration of Return Trip (Hours)',
        'from_city_id' => 'From City',
        'to_city_id' => 'To City',
        'bus_id' => 'Bus',
        'bus_driver_id' => 'Driver',
        'travel_company_id' => 'Travel Company',
        'number_of_seats' => 'Number of Seats',
        'remaining_seats' => 'Available Seats',
        'ticket_price' => 'Ticket Price',
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'departure_datetime' => 'Departure Date & Time',
            'return_datetime' => 'Return Date & Time',
            'from_city' => 'From City',
            'to_city' => 'To City',
            'bus' => 'Bus',
            'driver' => 'Driver',
            'travel_company' => 'Travel Company',
            'duration_of_departure_trip' => 'Departure Trip Duration',
            'duration_of_return_trip' => 'Return Trip Duration',
            'number_of_seats' => 'Total Seats',
            'remaining_seats' => 'Available Seats',
            'ticket_price' => 'Ticket Price',
        ],
    ],
    'filters' => [
        'trip_type' => [
            'label' => 'Trip Type',
            'options' => [
                'one_way' => 'One Way',
                'two_way' => 'Round Trip',
            ],
        ],
    ],
];
