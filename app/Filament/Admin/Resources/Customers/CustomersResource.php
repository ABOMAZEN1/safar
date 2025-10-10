<?php

namespace App\Filament\Admin\Resources\Customers;

use App\Filament\Admin\Resources\Customers\Pages\EditCustomers;
use App\Filament\Admin\Resources\Customers\Pages\ListCustomers;
use App\Filament\Admin\Resources\Customers\Pages\ViewCustomers;
use App\Filament\Admin\Resources\Customers\RelationManagers\BookingsRelationManager;
use App\Filament\Admin\Resources\Customers\Schemas\CustomersForm;
use App\Filament\Admin\Resources\Customers\Schemas\CustomersInfolist;
use App\Filament\Admin\Resources\Customers\Tables\CustomersTable;
use App\Models\Customer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Tables\Columns\TextColumn;

class CustomersResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'الزبائن';

    public static function infolist(Schema $schema): Schema
    {
        return CustomersInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return CustomersForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'view' => ViewCustomers::route('/{record}'),
            'edit' => EditCustomers::route('/{record}/edit'),
            // تم إزالة خيار إضافة زبون
        ];
    }

    public static function getPluralLabel(): string
    {
        return 'الزبائن';
    }

    /**
     * Relation manager to show all bookings for a customer.
     */
    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }
}

 
