<?php

namespace App\Filament\Admin\Resources\BusTrips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BusTripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromCity.id')
                    ->searchable(),
                TextColumn::make('toCity.id')
                    ->searchable(),
                TextColumn::make('bus.id')
                    ->searchable(),
                TextColumn::make('busDriver.id')
                    ->searchable(),
                TextColumn::make('travelCompany.id')
                    ->searchable(),
                TextColumn::make('departure_datetime')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('return_datetime')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration_of_departure_trip')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_of_return_trip')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trip_type')
                    ->searchable(),
                TextColumn::make('number_of_seats')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_seats')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ticket_price')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
