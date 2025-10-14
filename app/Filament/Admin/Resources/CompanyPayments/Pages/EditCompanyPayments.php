<?php

namespace App\Filament\Admin\Resources\CompanyPayments\Pages;

use App\Filament\Admin\Resources\CompanyPayments\CompanyPaymentsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyPayments extends EditRecord
{
    protected static string $resource = CompanyPaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
