<?php

namespace App\Filament\Admin\Resources\Buses\Pages;

use App\Filament\Admin\Resources\Buses\BusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBus extends EditRecord
{
    protected static string $resource = BusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
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
