<?php

namespace App\Filament\Admin\Resources\TravelCompanies;

use App\Filament\Admin\Resources\TravelCompanies\Pages\CreateTravelCompany;
use App\Filament\Admin\Resources\TravelCompanies\Pages\EditTravelCompany;
use App\Filament\Admin\Resources\TravelCompanies\Pages\ListTravelCompanies;
use App\Filament\Admin\Resources\TravelCompanies\Pages\ViewTravelCompany;
use App\Filament\Admin\Resources\TravelCompanies\Schemas\TravelCompanyForm;
use App\Filament\Admin\Resources\TravelCompanies\Schemas\TravelCompanyInfolist;
use App\Filament\Admin\Resources\TravelCompanies\Tables\TravelCompaniesTable;
use App\Filament\Admin\Resources\TravelCompanies\RelationManagers\BusesRelationManager;
use App\Filament\Admin\Resources\TravelCompanies\RelationManagers\BusTripsRelationManager;
use App\Models\TravelCompany;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class TravelCompanyResource extends Resource
{
    protected static ?string $model = TravelCompany::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // ✅ حقل موجود في جدول الشركة لتفادي الخطأ
    protected static ?string $recordTitleAttribute = 'company_name';

    // اسم القائمة
    protected static ?string $navigationLabel = 'شركات النقل';

    public static function form(Schema $schema): Schema
    {
        return TravelCompanyForm::configure($schema);
    }
    public static function getPluralLabel(): string
    {
        return 'شركات النقل';
    }

    // عنوان السجل المفرد
    public static function getLabel(): string
    {
        return 'شركة نقل';
    }

    public static function infolist(Schema $schema): Schema
    {
        return TravelCompanyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TravelCompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BusesRelationManager::class,
            BusTripsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTravelCompanies::route('/'),
            'create' => CreateTravelCompany::route('/create'),
            'view' => ViewTravelCompany::route('/{record}'),
            'edit' => EditTravelCompany::route('/{record}/edit'),
        ];
    }
}
