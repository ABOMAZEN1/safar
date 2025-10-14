<?php

namespace App\Filament\Admin\Resources\Buses;

use App\Filament\Admin\Resources\Buses\Schemas\BusesInfolist;
use App\Filament\Admin\Resources\Buses\Schemas\BusForm;
use App\Filament\Admin\Resources\Buses\Tables\BusesTable;
use App\Models\Bus;
use App\Models\User;
use App\Models\AssistantDriver;
use App\Enum\UserTypeEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\Support\Facades\Log;

class BusResource extends Resource
{
    protected static ?string $model = Bus::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'الباصات';
    protected static ?string $recordTitleAttribute = 'details';

    public static function form(Schema $schema): Schema
    {
        return BusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BusesInfolist::configure($schema);
    }

    public static function getPluralLabel(): string
    {
        return 'الباصات';
    }

    public static function getSingularLabel(): string
    {
        return 'باص';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuses::route('/'),
            'create' => Pages\CreateBus::route('/create'),
            'edit' => Pages\EditBus::route('/{record}/edit'),
            'view' => Pages\ViewBus::route('/{record}'),
        ];
    }

    /**
     * إنشاء الباص وربط المعاون إذا وجد أو إنشاء معاون جديد
     */
    public static function handleCreate(array $data): Bus
    {
        $hasAssistantColumn = DBSchema::hasColumn('buses', 'assistant_driver_id');
        $addNewAssistant = boolval($data['add_new_assistant'] ?? false);

        return DB::transaction(function () use ($data, $hasAssistantColumn, $addNewAssistant) {
            $assistantDriverId = null;

            // إذا تم تفعيل إضافة معاون جديد
            if ($addNewAssistant) {
                if (empty($data['assistant_phone'])) {
                    throw ValidationException::withMessages([
                        'assistant_phone' => ['رقم الهاتف مطلوب عند إضافة معاون جديد.'],
                    ]);
                }

                $assistantDriverId = static::createOrGetAssistantDriver($data);

                if (!$assistantDriverId) {
                    throw ValidationException::withMessages([
                        'assistant_phone' => ['فشل في إنشاء معاون السائق الجديد.'],
                    ]);
                }
            } else {
                // إذا تم اختيار معاون موجود مسبقًا
                $assistantDriverId = $data['assistant_driver_id'] ?? null;
            }

            // تجهيز بيانات الباص
            $busData = [
                'bus_type_id' => $data['bus_type_id'],
                'travel_company_id' => $data['travel_company_id'],
                'capacity' => $data['capacity'],
                'details' => $data['details'],
            ];

            // ربط المعاون إذا العمود موجود
            if ($hasAssistantColumn && $assistantDriverId) {
                $busData['assistant_driver_id'] = $assistantDriverId;
            }

            return Bus::create($busData);
        });
    }

    /**
     * إنشاء أو استرجاع معاون سائق
     */
    private static function createOrGetAssistantDriver(array $data): ?int
    {
        $phone = $data['assistant_phone'] ?? null;
        $companyId = $data['travel_company_id'] ?? null;

        if (empty($phone) || empty($companyId)) {
            return null;
        }

        // تحقق من وجود مستخدم بنفس الرقم
        $user = User::firstOrCreate(
            ['phone_number' => $phone],
            [
                'name' => $data['assistant_name'] ?? 'معاون جديد',
                'type' => UserTypeEnum::ASSISTANT_DRIVER->value,
                'password' => Hash::make($data['assistant_password'] ?? 'password123'),
                'verified_at' => now(),
            ]
        );

        // تعديل نوع المستخدم إذا لم يكن معاون
        if ($user->type !== UserTypeEnum::ASSISTANT_DRIVER->value) {
            $user->update(['type' => UserTypeEnum::ASSISTANT_DRIVER->value]);
        }

        // إنشاء أو استرجاع سجل المعاون
        try {
            $assistantDriver = AssistantDriver::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'travel_company_id' => $companyId,
                ],
                [
                    'license_number' => $data['assistant_license_number'] ?? null,
                    'license_expiry_date' => $data['assistant_license_expiry'] ?? null,
                    'status' => 'active',
                    'notes' => $data['assistant_notes'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to create assistant: '.$e->getMessage());
            return null;
        }

        return $assistantDriver->id;
    }
}
