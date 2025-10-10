<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class CustomersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')->label('اسم المستخدم'),
                TextInput::make('user.phone_number')->label('رقم الهاتف'),
                DatePicker::make('birth_date')->label('تاريخ الميلاد'),
                TextInput::make('national_id')->label('الرقم الوطني'),
                TextInput::make('gender')->label('الجنس'),
                TextInput::make('address')->label('العنوان'),
                TextInput::make('mother_name')->label('اسم الأم'),
            ]);
    }
}
