<?php

namespace App\Filament\Admin\Resources\BusTrips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusTripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_city_id')
                    ->relationship('fromCity', 'id')
                    ->required(),
                Select::make('to_city_id')
                    ->relationship('toCity', 'id')
                    ->required(),
                Select::make('bus_id')
                    ->relationship('bus', 'id')
                    ->required(),
                Select::make('bus_driver_id')
                    ->relationship('busDriver', 'id')
                    ->required(),
                Select::make('travel_company_id')
                    ->relationship('travelCompany', 'id')
                    ->required(),
                DateTimePicker::make('departure_datetime')
                    ->required(),
                DateTimePicker::make('return_datetime'),
                TextInput::make('duration_of_departure_trip')
                    ->required()
                    ->numeric(),
                TextInput::make('duration_of_return_trip')
                    ->numeric()
                    ->default(null),
                TextInput::make('trip_type')
                    ->required(),
                TextInput::make('number_of_seats')
                    ->required()
                    ->numeric(),
                TextInput::make('remaining_seats')
                    ->required()
                    ->numeric(),
                TextInput::make('ticket_price')
                    ->required()
                    ->numeric(),
                FileUpload::make('image')
                    ->image(),
            ]);
    }
}
