<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTravelCompany extends EditRecord
{
    protected static string $resource = TravelCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    public static function getRelations(): array
    {
        return [];
    }

  
    protected function hasRelationManagers(): bool
    {
        return false;
    }
 
}
