<?php

namespace App\Filament\Admin\Resources\TravelCompanies\RelationManagers;

use App\Models\Bus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class BusesRelationManager extends RelationManager
{
	protected static string $relationship = 'buses';
	protected static ?string $title = 'الحافلات';

	public function table(Table $table): Table
	{
		return $table
			->columns([
 				TextColumn::make('busType.name')->label('نوع الحافلة')->sortable()->searchable(),
				TextColumn::make('capacity')->label('السعة')->sortable(),
				TextColumn::make('details')->label('التفاصيل')->limit(50),
			])
			->emptyStateHeading('لا توجد حافلات')
			->emptyStateDescription('لا توجد حافلات مرتبطة بهذه الشركة.');
	}


	public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
	{
		return is_subclass_of($pageClass, ViewRecord::class);
	}
}