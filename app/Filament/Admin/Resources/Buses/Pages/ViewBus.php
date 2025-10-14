<?php

namespace App\Filament\Admin\Resources\Buses\Pages;

use App\Filament\Admin\Resources\Buses\BusResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\ComponentContainer;
use Filament\Schemas\Schema;

class ViewBus extends ViewRecord
{
    protected static string $resource = BusResource::class;

    protected function getHeaderActions(): array
    {
        $bus = $this->record;

        return [
            Action::make('edit_bus')
                ->label($bus->assistant_driver_id ? 'تعديل (مرتبط بمعاون)' : 'تعديل (بدون معاون)')
                ->icon('heroicon-o-pencil')
                ->button()
                ->form(fn (Schema $form) => BusResource::form($form)
                    ->fill($bus->toArray())) // هنا نملأ form بالقيم الحالية
                ->modalHeading('تعديل الباص')
                ->modalSubmitActionLabel('حفظ التعديلات')
                ->action(function (array $data) use ($bus) {
                    $bus->update($data); // تحديث البيانات
                    $this->notify('success', 'تم تحديث بيانات الباص بنجاح');
                }),
        ];
    }
}
