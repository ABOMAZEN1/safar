<?php

namespace App\Filament\Admin\Resources\Buses\Pages;

use App\Filament\Admin\Resources\Buses\BusResource;
use App\Models\Bus;
use Filament\Resources\Pages\CreateRecord;

class CreateBus extends CreateRecord
{
    protected static string $resource = BusResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // لا تحذف مفتاح add_new_assistant
        return $data;
    }

    protected function handleRecordCreation(array $data): Bus
    {
        return BusResource::handleCreate($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
