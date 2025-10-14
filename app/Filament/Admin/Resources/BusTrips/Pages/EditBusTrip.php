<?php

namespace App\Filament\Admin\Resources\BusTrips\Pages;

use App\Filament\Admin\Resources\BusTrips\BusTripResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Illuminate\Support\Facades\Auth;

class EditBusTrip extends EditRecord
{
    protected static string $resource = BusTripResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure relationships are loaded for the title attribute
        $this->record->load(['fromCity', 'toCity', 'bus', 'busDriver', 'travelCompany']);
        return $data;
    }

    /**
     * Catch any exception from observers on update (validation errors)
     * and show the real reason/message to the user in a notification.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (Throwable $e) {
            $this->showDetailedError($e);
            throw new Halt();
        }
    }

    /**
     * Show detailed error message with specific reasons
     */
    private function showDetailedError(Throwable $e): void
    {
        $message = $e->getMessage();
        $title = 'حدث خطأ أثناء تعديل الرحلة';
        $body = $message;

        // Parse detailed error messages
        if (str_contains($message, 'تم رفض الوصول لهذه الرحلة')) {
            $title = 'تم رفض تعديل الرحلة';
            $body = $this->parseAccessDeniedMessage($message);
        }
        // Handle validation errors from services
        elseif (str_contains($message, 'must belong to your company')) {
            $title = 'خطأ في اختيار الموارد';
            $body = $this->parseCompanyOwnershipMessage($message);
        }
        // Handle capacity errors
        elseif (str_contains($message, 'cannot exceed the bus capacity')) {
            $title = 'خطأ في عدد المقاعد';
            $body = $this->parseCapacityMessage($message);
        }
        // Handle date/time errors
        elseif (str_contains($message, 'after_or_equal') || str_contains($message, 'must be after')) {
            $title = 'خطأ في التواريخ والأوقات';
            $body = $this->parseDateTimeMessage($message);
        }

        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }

    /**
     * Parse access denied messages and provide detailed Arabic explanations
     */
    private function parseAccessDeniedMessage(string $message): string
    {
        $details = [];
        
        // Check if user is Super Admin
        $user = Auth::user();
        $isSuperAdmin = $user && $user->isSuperAdmin();
        
        if ($isSuperAdmin) {
            $details[] = "• أنت Super Admin ولديك صلاحيات كاملة";
            $details[] = "• يجب أن تتمكن من تعديل جميع الرحلات";
            $details[] = "• إذا استمر هذا الخطأ، يرجى التحقق من إعدادات النظام";
            return "ملاحظة مهمة:\n" . implode("\n", $details);
        }
        
        if (str_contains($message, 'Cause: User is not associated with any company')) {
            $details[] = "• المستخدم غير مرتبط بأي شركة نقل";
            $details[] = "• يرجى التأكد من أن المستخدم لديه شركة مرتبطة به";
        }
        elseif (str_contains($message, 'Cause: Company ID mismatch')) {
            $details[] = "• الشركة المرتبطة بالمستخدم مختلفة عن شركة الرحلة";
            $details[] = "• يمكنك تعديل الرحلات التابعة لشركتك فقط";
            $details[] = "• تحقق من أنك مسجل دخول بحساب الشركة الصحيحة";
        }
        else {
            $details[] = "• ليس لديك صلاحية لتعديل هذه الرحلة";
            $details[] = "• تأكد من أن الرحلة تنتمي لشركتك";
        }

        return "السبب:\n" . implode("\n", $details);
    }

    /**
     * Parse company ownership messages
     */
    private function parseCompanyOwnershipMessage(string $message): string
    {
        $details = [];
        
        if (str_contains($message, 'bus must belong to your company')) {
            $details[] = "• الباص المختار لا ينتمي لشركتك";
            $details[] = "• يجب اختيار باص من الباصات التابعة لشركتك";
            $details[] = "• تحقق من قائمة الباصات المتاحة لشركتك";
        }
        elseif (str_contains($message, 'driver must belong to your company')) {
            $details[] = "• السائق المختار لا ينتمي لشركتك";
            $details[] = "• يجب اختيار سائق من السائقين التابعين لشركتك";
            $details[] = "• تحقق من قائمة السائقين المتاحين لشركتك";
        }

        return "السبب:\n" . implode("\n", $details);
    }

    /**
     * Parse capacity messages
     */
    private function parseCapacityMessage(string $message): string
    {
        // Extract numbers from message like "The number of seats (50) cannot exceed the bus capacity (40)"
        preg_match('/seats \((\d+)\) cannot exceed.*capacity \((\d+)\)/', $message, $matches);
        
        if (count($matches) >= 3) {
            $requestedSeats = $matches[1];
            $busCapacity = $matches[2];
            
            return "السبب:\n" .
                   "• عدد المقاعد المطلوب: {$requestedSeats} مقعد\n" .
                   "• سعة الباص المتاحة: {$busCapacity} مقعد\n" .
                   "• لا يمكن أن يتجاوز عدد المقاعد سعة الباص\n" .
                   "• يرجى اختيار عدد مقاعد أقل أو باص بسعة أكبر";
        }

        return "السبب:\n• عدد المقاعد المطلوب يتجاوز سعة الباص المتاحة";
    }

    /**
     * Parse date/time messages
     */
    private function parseDateTimeMessage(string $message): string
    {
        $details = [];
        
        if (str_contains($message, 'after_or_equal:departure_datetime')) {
            $details[] = "• تاريخ العودة يجب أن يكون بعد أو يساوي تاريخ المغادرة";
            $details[] = "• لا يمكن أن يكون تاريخ العودة قبل تاريخ المغادرة";
        }
        elseif (str_contains($message, 'after_or_equal:today')) {
            $details[] = "• تاريخ المغادرة يجب أن يكون اليوم أو تاريخ مستقبلي";
            $details[] = "• لا يمكن إنشاء رحلة بتاريخ ماضي";
        }
        elseif (str_contains($message, 'must be after')) {
            $details[] = "• التاريخ المدخل غير صحيح";
            $details[] = "• يرجى التحقق من التواريخ المدخلة";
        }

        return "السبب:\n" . implode("\n", $details);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
