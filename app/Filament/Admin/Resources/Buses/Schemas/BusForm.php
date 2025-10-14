<?php

namespace App\Filament\Admin\Resources\Buses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\BusType;
use App\Models\TravelCompany;
use App\Models\AssistantDriver;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;

class BusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ComponentsSection::make('معلومات الباص')
                ->schema([
                    ComponentsGrid::make(2)->schema([
                        Select::make('bus_type_id')
                            ->label('نوع الباص')
                            ->options(fn () => BusType::orderBy('name')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Select::make('travel_company_id')
                            ->label('شركة النقل')
                            ->options(fn () => TravelCompany::orderBy('company_name')->pluck('company_name', 'id'))
                            ->required()
                            ->searchable(),
                    ]),

                    ComponentsGrid::make(2)->schema([
                        TextInput::make('capacity')
                            ->label('السعة (عدد المقاعد)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100),

                        Select::make('assistant_driver_id')
                            ->label('معاون السائق')
                            ->options(fn () => AssistantDriver::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->placeholder('اختياري - يمكن إضافة معاون جديد لاحقاً'),
                    ]),

                    Textarea::make('details')
                        ->label('تفاصيل الباص')
                        ->rows(3)
                        ->required()
                        ->placeholder('مثال: باص حديث، مكيف، واي فاي، مقاعد مريحة...'),
                ]),

            ComponentsSection::make('إضافة معاون سائق جديد')
                ->schema([
                    Toggle::make('add_new_assistant')
                        ->label('إضافة معاون سائق جديد')
                        ->default(false)
                        ->live(),

                    ComponentsGrid::make(2)->schema([
                        TextInput::make('assistant_name')
                            ->label('اسم المعاون')
                            ->required(fn ($get) => $get('add_new_assistant'))
                            ->visible(fn ($get) => $get('add_new_assistant')),

                        TextInput::make('assistant_phone')
                            ->label('رقم الهاتف')
                            ->required(fn ($get) => $get('add_new_assistant'))
                            ->visible(fn ($get) => $get('add_new_assistant')),
                    ]),

                    TextInput::make('assistant_password')
                        ->label('كلمة مرور المعاون')
                        ->password()
                        ->minLength(6)
                        ->maxLength(32)
                        ->placeholder('كلمة مرور افتراضية')
                        ->required(fn ($get) => $get('add_new_assistant'))
                        ->visible(fn ($get) => $get('add_new_assistant')),

                    ComponentsGrid::make(2)->schema([
                        TextInput::make('assistant_license_number')
                            ->label('رقم رخصة القيادة')
                            ->visible(fn ($get) => $get('add_new_assistant')),

                        DatePicker::make('assistant_license_expiry')
                            ->label('تاريخ انتهاء الرخصة')
                            ->visible(fn ($get) => $get('add_new_assistant')),
                    ]),

                    Textarea::make('assistant_notes')
                        ->label('ملاحظات إضافية')
                        ->rows(2)
                        ->visible(fn ($get) => $get('add_new_assistant')),
                ])->collapsible(),
        ]);
    }
}
