<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use App\Filament\Admin\Resources\TravelCompanies\RelationManagers\BusesRelationManager;
use App\Filament\Admin\Resources\TravelCompanies\RelationManagers\BusTripsRelationManager;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Actions\EditAction;
use Filament\Actions\Action;

class ViewTravelCompany extends ViewRecord
{
    use HasRelationManagers;

    protected static string $resource = TravelCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('pdf_report')
                ->label('توليد تقرير PDF')
                ->icon('heroicon-o-document-text')
                ->openUrlInNewTab(),
        ];
    }

    protected function getRelations(): array
    {
        return [
            BusesRelationManager::class,
            BusTripsRelationManager::class,
        ];
    }
}