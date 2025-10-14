<?php

namespace App\Filament\Admin\Resources\CompanyPayments\Pages;

use App\Filament\Admin\Resources\CompanyPayments\CompanyPaymentsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyPayments extends ViewRecord
{
    protected static string $resource = CompanyPaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
