<?php

namespace App\Filament\Admin\Resources\TravelCompanies\RelationManagers;

 
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
class BusTripsRelationManager extends RelationManager
{
	protected static string $relationship = 'busTrips';
	protected static ?string $title = 'الرحلات';

	public function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('fromCity.name')->label('من مدينة')->default('—'),
				TextColumn::make('toCity.name')->label('إلى مدينة')->default('—'),
				TextColumn::make('departure_datetime')->label('وقت الانطلاق')->default('—'),
				TextColumn::make('ticket_price')->label('سعر التذكرة')->default('—'),
				TextColumn::make('remaining_seats')->label('المقاعد المتبقية')->default('0'),
			])
			->emptyStateHeading('لا توجد رحلات')
			->emptyStateDescription('لا توجد رحلات مرتبطة بهذه الشركة.');
	}
	public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
	{
		return is_subclass_of($pageClass, ViewRecord::class);
	}
    
}