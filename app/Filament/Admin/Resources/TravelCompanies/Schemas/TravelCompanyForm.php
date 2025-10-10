<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class TravelCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCreatePage = str_contains(request()->route()?->getName() ?? '', '.create');

        return $schema->components([
            // 👤 بيانات المالك
            ComponentsSection::make('بيانات مالك الشركة')
                ->description('معلومات المستخدم المالك للشركة')
                ->columns(1)
                ->schema([
                    TextInput::make('user.name')
                        ->label('اسم المالك')
                        ->required()
                        ->formatStateUsing(fn ($state, $record) => old('user.name', $state ?? ($record->user->name ?? ''))),

                    TextInput::make('user.phone_number')
                        ->label('رقم جوال المالك')
                        ->tel()
                        ->required()
                        ->formatStateUsing(fn ($state, $record) => old('user.phone_number', $state ?? ($record->user->phone_number ?? ''))),

                    TextInput::make('user.password')
                        ->label('كلمة المرور')
                        ->password()
                        ->placeholder('اتركها فارغة إذا لا تريد تغييرها')
                        ->dehydrated(fn ($state) => !empty($state)),
                ]),

            // 🏢 بيانات الشركة
            ComponentsSection::make('بيانات الشركة')
                ->description('معلومات الشركة')
                ->columns(1)
                ->schema([
                    TextInput::make('company_name')
                        ->label('اسم الشركة')
                        ->required(),

                    TextInput::make('contact_number')
                        ->label('رقم الشركة')
                        ->tel()
                        ->required(),

                    TextInput::make('address')
                        ->label('عنوان الشركة')
                        ->required(),

                    FileUpload::make('image_path')
                        ->label('شعار الشركة')
                        ->image()
                        ->directory('travel_companies')
                        ->required(),


                    Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'active' => 'فعّالة',
                            'inactive' => 'غير فعّالة',
                        ])
                        ->required(),

                    TextInput::make('commission_amount')
                        ->label('نسبة الربح (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.1)
                        ->required(),
                ]),
        ]);
    }
}
