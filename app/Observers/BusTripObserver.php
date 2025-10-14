<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BusTrip;
use App\Enum\UserTypeEnum;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Observer لعمليات (إنشاء/تحديث) رحلات الباصات
 */
final class BusTripObserver
{
    /**
     * عند إنشاء سجل جديد
     */
    public function creating(BusTrip $busTrip): void
    {
        $this->validateSeatCapacity($busTrip); // تحقق من سعة الباص
        $busTrip->remaining_seats = $busTrip->number_of_seats; // عيّن المقاعد المتبقية = كل المقاعد
    }

    /**
     * عند الحفظ (سواء إنشاء أو تحديث)
     */
    public function saving(BusTrip $busTrip): void
    {
        // تحقق فقط إذا تغير الباص وتم اختياره
        if ($busTrip->isDirty('bus_id') && $busTrip->bus) {
            $this->validateBusBelongsToCompany($busTrip);
        }

        // تحقق فقط إذا تغير السائق وتم اختياره
        if ($busTrip->isDirty('bus_driver_id') && $busTrip->busDriver) {
            $this->validateDriverBelongsToCompany($busTrip);
        }
    }

    /**
     * عند تحديث السجل
     */
    public function updating(BusTrip $busTrip): void
    {
        $this->validateSeatCapacity($busTrip);
    }

    /**
     * التأكد أن عدد المقاعد لا يتجاوز سعة الباص
     */
    private function validateSeatCapacity(BusTrip $busTrip): void
    {
        if ($busTrip->bus && $busTrip->number_of_seats > $busTrip->bus->capacity) {
            // رسالة خطأ مخصصة (يمكن تعديلها في ملف اللغة)
            $msg = __('messages.errors.bus_trip.insufficient_seats');

            Log::error('فشل تحقق السعة', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'bus_id' => $busTrip->bus_id,
                'requested_seats' => $busTrip->number_of_seats,
                'bus_capacity' => $busTrip->bus->capacity,
                'error' => $msg
            ]);
            throw new Exception($msg);
        }
    }

    /**
     * التأكد أن الباص يتبع نفس الشركة (إلا إذا كان مستخدم Super Admin)
     */
    private function validateBusBelongsToCompany(BusTrip $busTrip): void
    {
        if ($this->isSuperAdmin()) return;

        if ($busTrip->bus && $busTrip->bus->travel_company_id !== $busTrip->travel_company_id) {
            $msg = __('messages.errors.bus_trip.access_denied');
            Log::error('الباص لا يتبع نفس الشركة', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'bus_id' => $busTrip->bus_id,
                'bus_company_id' => $busTrip->bus->travel_company_id,
                'trip_company_id' => $busTrip->travel_company_id,
                'error' => $msg
            ]);
            throw new Exception($msg);
        }
    }

    /**
     * التأكد أن السائق يتبع نفس الشركة (إلا إذا كان مستخدم Super Admin)
     */
    private function validateDriverBelongsToCompany(BusTrip $busTrip): void
    {
        if ($this->isSuperAdmin()) return;

        if ($busTrip->busDriver && $busTrip->busDriver->travel_company_id !== $busTrip->travel_company_id) {
            $msg = __('messages.errors.bus_trip.access_denied');
            Log::error('السائق لا يتبع نفس الشركة', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'driver_id' => $busTrip->bus_driver_id,
                'driver_name' => $busTrip->busDriver->user->name ?? 'Unknown',
                'driver_company_id' => $busTrip->busDriver->travel_company_id,
                'trip_company_id' => $busTrip->travel_company_id,
                'error' => $msg
            ]);
            throw new Exception($msg);
        }
    }

    /**
     * تحقق إذا كان المستخدم الحالي Super Admin
     */
    private function isSuperAdmin(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // تحقق إذا كان لدى المستخدم دور Super Admin
        // إذا كان هناك حقل مباشرة في users (مثلاً user_type) استخدمه بدل العلاقات
        if (property_exists($user, 'user_type') && $user->user_type === UserTypeEnum::SUPER_ADMIN->value) {
            return true;
        }

        // إذا كانت للأدوار علاقة
        if (method_exists($user, 'roles')) {
            return $user->roles()->where('role_name', UserTypeEnum::SUPER_ADMIN->value)->exists();
        }

        return false;
    }
}
