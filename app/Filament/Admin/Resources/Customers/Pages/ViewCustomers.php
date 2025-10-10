<?php

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomersResource;
use App\Filament\Admin\Resources\Customers\RelationManagers\BookingsRelationManager;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomers extends ViewRecord
{
    protected static string $resource = CustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }
}
