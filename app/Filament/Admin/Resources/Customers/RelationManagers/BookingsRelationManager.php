<?php

namespace App\Filament\Admin\Resources\Customers\RelationManagers;
use Illuminate\Database\Eloquent\Model; // ✅ أضف هذا بالأعلى

use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'الحجوزات';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
         
                TextColumn::make('busTrip.departure_datetime')->label('تاريخ المغادرة')->dateTime('Y-m-d H:i'),
                TextColumn::make('reserved_seat_numbers')->label('أرقام المقاعد المحجوزة'),
                TextColumn::make('booking_status')->label('حالة الحجز'),
                TextColumn::make('total_price')->label('إجمالي السعر'),
                TextColumn::make('created_at')->label('تاريخ الحجز')->dateTime('Y-m-d H:i'),
            ])
            ->filters([]);
    }
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
	{
		return is_subclass_of($pageClass, ViewRecord::class);
	}
}