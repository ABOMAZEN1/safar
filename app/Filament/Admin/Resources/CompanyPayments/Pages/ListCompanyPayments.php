<?php

namespace App\Filament\Admin\Resources\CompanyPayments\Pages;

use App\Filament\Admin\Resources\CompanyPayments\CompanyPaymentsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyPayments extends ListRecords
{
    protected static string $resource = CompanyPaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
  
        ];
    }
}
