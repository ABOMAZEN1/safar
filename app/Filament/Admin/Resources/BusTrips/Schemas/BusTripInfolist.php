<?php

namespace App\Filament\Admin\Resources\BusTrips\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BusTripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('fromCity.id')
                    ->label('From city'),
                TextEntry::make('toCity.id')
                    ->label('To city'),
                TextEntry::make('bus.id')
                    ->label('Bus'),
                TextEntry::make('busDriver.id')
                    ->label('Bus driver'),
                TextEntry::make('travelCompany.id')
                    ->label('Travel company'),
                TextEntry::make('departure_datetime')
                    ->dateTime(),
                TextEntry::make('return_datetime')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('duration_of_departure_trip')
                    ->numeric(),
                TextEntry::make('duration_of_return_trip')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('trip_type'),
                TextEntry::make('number_of_seats')
                    ->numeric(),
                TextEntry::make('remaining_seats')
                    ->numeric(),
                TextEntry::make('ticket_price')
                    ->numeric(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
