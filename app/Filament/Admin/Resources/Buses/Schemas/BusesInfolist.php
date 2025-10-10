<?php

namespace App\Filament\Admin\Resources\Buses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BusesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('bus_number')
                    ->label('رقم الحافلة'),

                TextEntry::make('plate_number')
                    ->label('رقم اللوحة'),

                TextEntry::make('capacity')
                    ->label('السعة'),

                TextEntry::make('type')
                    ->label('النوع'),

                TextEntry::make('driver_name')
                    ->label('اسم السائق'),
            ]);
    }
}