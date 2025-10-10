<?php

namespace App\Filament\Admin\Resources\Buses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bus_type_id')
                    ->relationship('busType', 'name')
                    ->required(),
                Select::make('travel_company_id')
                    ->relationship('travelCompany', 'id')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('details')
                    ->required(),
            ]);
    }
}
