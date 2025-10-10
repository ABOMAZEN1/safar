<?php

namespace App\Filament\Admin\Resources\Buses;

use App\Filament\Admin\Resources\Buses\Schemas\BusesInfolist;
use App\Filament\Admin\Resources\Buses\Schemas\BusForm;
use App\Filament\Admin\Resources\Buses\Tables\BusesTable;
use App\Models\Bus;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BusResource extends Resource
{
    protected static ?string $model = Bus::class;
    // protected static ?string $navigationIcon = 'heroicon-o-bus';
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return BusForm::configure($schema);
    }   
    public static function table(Table $table): Table
    {
        return BusesTable::configure($table);
    }
    public static function infolist(Schema $schema): Schema
    {
        return BusesInfolist::configure($schema);
    }
 

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuses::route('/'),
            'create' => Pages\CreateBus::route('/create'),
            'edit' => Pages\EditBus::route('/{record}/edit'),
            // 'view' => Pages\ViewBus::route('/{record}'),
        ];
    }
}
