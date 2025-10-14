<?php

namespace App\Filament\Admin\Resources\Buses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BusesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('رقم الباص'),

                TextEntry::make('busType.name')
                    ->label('نوع الباص'),

                TextEntry::make('travelCompany.company_name')
                    ->label('شركة النقل'),
                    TextEntry::make('assistant_status')
                    ->label('حالة المعاون')
                    ->getStateUsing(function ($record) {
                        return $record->assistant_driver_id ? 'مرتبط بمعاون' : 'غير مرتبط بمعاون';
                    })
                    ->color(function ($record) {
                        return $record->assistant_driver_id ? 'success' : 'danger';
                    }),
                
                TextEntry::make('capacity')
                    ->label('السعة')
                    ->suffix(' مقعد'),

                TextEntry::make('details')
                    ->label('التفاصيل')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i'),

                TextEntry::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}