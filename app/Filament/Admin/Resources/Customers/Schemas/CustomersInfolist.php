<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\DateEntry;
use Filament\Schemas\Schema;

class CustomersInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('اسم المستخدم'),

                TextEntry::make('user.phone_number')
                    ->label('رقم الهاتف'),

                TextEntry::make('birth_date')
                    ->label('تاريخ الميلاد'),

                TextEntry::make('national_id')
                    ->label('الرقم الوطني'),

                TextEntry::make('gender')
                    ->label('الجنس'),

                TextEntry::make('address')
                    ->label('العنوان'),

                TextEntry::make('mother_name')
                    ->label('اسم الأم'),
            ]);
    }
}