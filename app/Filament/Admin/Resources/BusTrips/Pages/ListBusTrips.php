<?php

namespace App\Filament\Admin\Resources\BusTrips\Pages;

use App\Filament\Admin\Resources\BusTrips\BusTripResource;
use App\Models\BusTrip;
use App\Models\Bus;
use App\Models\BusDriver;
use App\Services\Book\BookService;
use App\Services\BusTrip\BusTripUpdateService;
use Filament\Actions\Action;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Illuminate\Support\Collection;

class ListBusTrips extends ListRecords
{
    protected static string $resource = BusTripResource::class;

    protected function getTableActions(): array
    {
        return [

            // ----------------- تعديل الرحلة -----------------
            ActionsEditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square')
                ->modalHeading('تعديل بيانات الرحلة')
                ->modalWidth('lg')
                ->form($this->createOrUpdateFormSchema())
                ->action(function (BusTrip $record, array $data, BusTripUpdateService $service) {

                    // ✅ Validation: الباص ينتمي لشركة الرحلة
                    $bus = Bus::find($data['bus_id']);
                    if ($bus && $bus->travel_company_id !== $record->travel_company_id) {
                        Notification::make()
                            ->title('خطأ في اختيار الباص')
                            ->body('الباص المختار لا ينتمي لشركة الرحلة الحالية. يرجى اختيار باص تابع لنفس الشركة.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // ✅ Validation: السائق ينتمي لشركة الرحلة
                    $driver = BusDriver::with('user')->find($data['bus_driver_id']);
                    if ($driver && $driver->travel_company_id !== $record->travel_company_id) {
                        Notification::make()
                            ->title('خطأ في اختيار السائق')
                            ->body('السائق المختار لا ينتمي لشركة الرحلة الحالية.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // ✅ Validation: تاريخ العودة لا يمكن أن يكون قبل تاريخ المغادرة
                    if (!empty($data['return_datetime']) && $data['return_datetime'] < $data['departure_datetime']) {
                        Notification::make()
                            ->title('خطأ في المواعيد')
                            ->body('تاريخ العودة لا يمكن أن يكون قبل تاريخ المغادرة.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // كل شيء صحيح، يتم الحفظ
                    $service->updateFromArray($record->id, $data);

                    Notification::make()
                        ->title('تم تحديث الرحلة')
                        ->success()
                        ->send();
                }),

            // ----------------- زر تأجيل الرحلة -----------------
            Action::make('postpone')
                ->label('تأجيل')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->modalHeading('تأجيل الرحلة')
                ->modalWidth('md')
                ->form([
                    Forms\Components\DateTimePicker::make('new_departure')
                        ->required()
                        ->label('موعد الانطلاق الجديد')
                        ->displayFormat('Y-m-d H:i'),
                    Forms\Components\Textarea::make('reason')
                        ->rows(2)
                        ->label('سبب التأجيل')
                        ->placeholder('يرجى توضيح سبب التأجيل'),
                ])
                ->action(function (BusTrip $record, array $data, BusTripUpdateService $updateService, BookService $bookService) {
                    // Validation: التأجيل لا يمكن أن يكون قبل الآن
                    if ($data['new_departure'] < now()) {
                        Notification::make()
                            ->title('خطأ')
                            ->body('موعد الانطلاق الجديد يجب أن يكون بعد الوقت الحالي.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $updateService->updateFromArray($record->id, [
                        'departure_datetime' => $data['new_departure'],
                    ]);

                    $bookings = $bookService->getTripBookings($record->id);
                    $this->notifyCustomers($bookings, "تم تأجيل الرحلة إلى {$data['new_departure']}", $data['reason'] ?? null);

                    Notification::make()
                        ->title('تم تأجيل الرحلة وإشعار الزبائن')
                        ->success()
                        ->send();
                }),

            // ----------------- زر إلغاء الرحلة -----------------
            Action::make('cancelTrip')
                ->label('إلغاء')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تأكيد إلغاء الرحلة')
                ->modalWidth('md')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->rows(3)
                        ->label('سبب الإلغاء')
                        ->placeholder('يرجى توضيح سبب الإلغاء'),
                    Forms\Components\Toggle::make('refund')
                        ->default(true)
                        ->label('استرجاع المدفوعات (إن وجدت)'),
                ])
                ->action(function (BusTrip $record, array $data, BookService $bookService) {
                    $bookings = $bookService->getTripBookings($record->id);

                    $bookings->each(function ($booking) use ($bookService, $data) {
                        if (!($data['refund'] ?? true)) {
                            $bookService->cancel($booking->id);
                        } else {
                            $bookService->refund($booking->id);
                        }
                    });

                    $this->notifyCustomers($bookings, 'تم إلغاء الرحلة', $data['reason'] ?? null);

                    Notification::make()
                        ->title('تم إلغاء الرحلة وإشعار الزبائن')
                        ->success()
                        ->send();
                }),
        ];
    }

    // ----------------- Form Schema -----------------
    private function createOrUpdateFormSchema(): array
    {
        return [
            ComponentsGrid::make(2)->schema([
                Forms\Components\Select::make('from_city_id')->relationship('fromCity', 'name')->required()->label('من مدينة'),
                Forms\Components\Select::make('to_city_id')->relationship('toCity', 'name')->required()->label('إلى مدينة'),
                Forms\Components\Select::make('bus_id')->relationship('bus', 'name')->required()->label('الحافلة'),
                Forms\Components\Select::make('bus_driver_id')->relationship('busDriver.user', 'name')->required()->label('السائق'),
                Forms\Components\DateTimePicker::make('departure_datetime')->required()->label('وقت الانطلاق'),
                Forms\Components\DateTimePicker::make('return_datetime')->visible(fn ($get) => $get('trip_type') === 'two_way')->label('وقت العودة'),
                Forms\Components\TextInput::make('duration_of_departure_trip')->required()->label('مدة رحلة الذهاب'),
                Forms\Components\TextInput::make('duration_of_return_trip')->visible(fn ($get) => $get('trip_type') === 'two_way')->label('مدة رحلة الإياب'),
                Forms\Components\Select::make('trip_type')->required()->options(['one_way' => 'ذهاب فقط','two_way' => 'ذهاب وإياب'])->default('one_way')->label('نوع الرحلة'),
                Forms\Components\TextInput::make('number_of_seats')->numeric()->required()->minValue(1)->label('عدد المقاعد'),
                Forms\Components\TextInput::make('ticket_price')->numeric()->required()->minValue(0)->label('سعر التذكرة')->prefix('د.ع'),
            ]),
        ];
    }

    // ----------------- إشعار الزبائن -----------------
    private function notifyCustomers(Collection $bookings, string $title, ?string $reason = null): void
    {
        $bookings->each(function ($booking) use ($title, $reason) {
            $user = $booking->customer?->user;
            if (!$user || !$user->id) return;

            Notification::make()
                ->title($title)
                ->body($reason ? "السبب: {$reason}" : null)
                ->sendToDatabase($user);
        });
    }
}
