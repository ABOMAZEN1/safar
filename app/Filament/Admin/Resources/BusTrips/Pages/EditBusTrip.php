<?php

namespace App\Filament\Admin\Resources\BusTrips\Pages;

use App\Filament\Admin\Resources\BusTrips\BusTripResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\DeleteRecord;

class EditBusTrip extends EditRecord
{
    protected static string $resource = BusTripResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure relationships are loaded for the title attribute
        $this->record->load(['fromCity', 'toCity']);
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewRecord::getAction($this->record),
           
        ];
    }
}
