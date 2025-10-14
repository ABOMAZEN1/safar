<?php

namespace App\Filament\Admin\Resources\BusTrips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use App\Models\BusTrip;
use App\Services\BusTrip\BusTripUpdateService;
use App\Services\Book\BookService;
use Filament\Actions\Action as ActionsAction;
use Illuminate\Support\Facades\Auth;

class BusTripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromCity.name')->label('مدينة الانطلاق')->searchable(),
                TextColumn::make('toCity.name')->label('مدينة الوصول')->searchable(),
                TextColumn::make('busDriver.user.name')->label('السائق')->searchable(),
                TextColumn::make('travelCompany.company_name')->label('شركة النقل')->searchable(),
                TextColumn::make('departure_datetime')->dateTime()->label('تاريخ المغادرة')->sortable(),
                TextColumn::make('return_datetime')->dateTime()->label('تاريخ العودة')->sortable(),
                TextColumn::make('duration_of_departure_trip')->numeric()->label('مدة الذهاب (ساعات)')->sortable(),
                TextColumn::make('duration_of_return_trip')->numeric()->label('مدة العودة (ساعات)')->sortable(),
                TextColumn::make('trip_type')->label('نوع الرحلة')->searchable(),
                TextColumn::make('number_of_seats')->numeric()->label('عدد المقاعد')->sortable(),
                TextColumn::make('remaining_seats')->numeric()->label('المقاعد المتبقية')->sortable(),
                TextColumn::make('ticket_price')->numeric()->label('سعر التذكرة')->sortable(),
                ImageColumn::make('image')->label('صورة الرحلة'),
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
