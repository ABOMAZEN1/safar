<?php

namespace App\Filament\Admin\Resources\BusTrips\Pages;

use App\Filament\Admin\Resources\BusTrips\BusTripResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBusTrip extends ViewRecord
{
    protected static string $resource = BusTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
