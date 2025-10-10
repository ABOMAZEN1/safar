<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class TravelCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        // جعل الكاردات تحت بعض بشكل واضح ومرتب
        return $schema
            ->components([
                ComponentsSection::make('بيانات مالك الشركة')
                    ->description('يرجى إدخال معلومات مالك الشركة بدقة')
                    ->icon('heroicon-o-user')
                    ->columns(1) // عمود واحد فقط، الكارد يأخذ صف كامل
                    ->schema([
                        TextInput::make('owner.name')
                            ->label('اسم المالك')
                            ->placeholder('أدخل اسم المالك')
                            ->required()
                            ->autofocus(),
                        TextInput::make('owner.phone_number')
                            ->label('رقم جوال المالك')
                            ->placeholder('09XXXXXXXX')
                            ->required()
                            ->unique(User::class, 'phone_number')
                            ->tel(),
                        TextInput::make('owner.password')
                            ->label('كلمة المرور')
                            ->placeholder('********')
                            ->required()
                            ->password()
                            ->minLength(6)
                            ->revealable(),
                    ]),

                ComponentsSection::make('بيانات الشركة')
                    ->description('معلومات الشركة الأساسية')
                    ->icon('heroicon-o-building-office')
                    ->columns(1) // عمود واحد فقط، الكارد يأخذ صف كامل
                    ->schema([
                        TextInput::make('company_name')
                            ->label('اسم الشركة')
                            ->placeholder('اسم الشركة')
                            ->required(),
                        TextInput::make('contact_number')
                            ->label('رقم الاتصال')
                            ->placeholder('رقم الهاتف')
                            ->required()
                            ->tel(),
                        TextInput::make('address')
                            ->label('العنوان')
                            ->placeholder('العنوان بالتفصيل')
                            ->required(),
                       
                    ]),
                    ComponentsSection::make(' شعار الشركة')
                    ->description('شعار الشركة')
                    ->icon('heroicon-o-building-office')
                    ->columns([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                        '2xl' => 1,
                    ]) // الكارد ياخد عرض الشاشة كاملة على كل الأحجام
                    ->columnSpan('full') // يجعل الكارد ياخد عرض الشاشة كاملة
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('شعار الشركة')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imagePreviewHeight('120')
                            ->required(),
                    ]),
                
                ]);
    }
}