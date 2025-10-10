<?php

namespace App\Filament\Admin\Resources\BusTrips;

use App\Filament\Admin\Resources\BusTrips\Pages\CreateBusTrip;
use App\Filament\Admin\Resources\BusTrips\Pages\EditBusTrip;
use App\Filament\Admin\Resources\BusTrips\Pages\ListBusTrips;
use App\Filament\Admin\Resources\BusTrips\Pages\ViewBusTrip;
use App\Filament\Admin\Resources\BusTrips\Schemas\BusTripForm;
use App\Filament\Admin\Resources\BusTrips\Schemas\BusTripInfolist;
use App\Filament\Admin\Resources\BusTrips\Tables\BusTripsTable;
use App\Models\BusTrip;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables as Tables;
use Filament\Tables\Table;

class BusTripResource extends Resource
{
    protected static ?string $model = BusTrip::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'الرحلات';

    protected static ?string $recordTitleAttribute = 'trips';

    public static function form(Schema $schema): Schema
    {
        return BusTripForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BusTripInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('المعرف')->sortable(),
                Tables\Columns\TextColumn::make('fromCity.name')->label('من مدينة')->searchable(),
                Tables\Columns\TextColumn::make('toCity.name')->label('إلى مدينة')->searchable(),
                Tables\Columns\TextColumn::make('departure_datetime')->label('وقت الانطلاق')->dateTime(),
                Tables\Columns\TextColumn::make('ticket_price')->label('السعر')->formatStateUsing(fn($s) => $s !== null ? number_format((float)$s, 2) . ' SYP' : '—'),
                Tables\Columns\TextColumn::make('remaining_seats')->label('المقاعد المتبقية')->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusTrips::route('/'),
            'create' => CreateBusTrip::route('/create'),
            'view' => ViewBusTrip::route('/{record}'),
            'edit' => EditBusTrip::route('/{record}/edit'),
        ];
    }
}