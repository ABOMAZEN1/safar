<?php

namespace App\Filament\Admin\Resources\BusTrips\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BusTripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('fromCity.name')->label('مدينة الانطلاق'),
                TextEntry::make('toCity.name')->label('مدينة الوصول'),
                TextEntry::make('bus.busType.name')->label('الباص'),
                TextEntry::make('busDriver.user.name')->label('السائق'),
                TextEntry::make('travelCompany.company_name')->label('شركة النقل'),
                TextEntry::make('departure_datetime')->label('تاريخ المغادرة')->dateTime(),
                TextEntry::make('return_datetime')->label('تاريخ العودة')->dateTime()->placeholder('-'),
                TextEntry::make('duration_of_departure_trip')->label('مدة الذهاب (ساعات)')->numeric(),
                TextEntry::make('duration_of_return_trip')->label('مدة العودة (ساعات)')->numeric()->placeholder('-'),
                TextEntry::make('trip_type')->label('نوع الرحلة'),
                TextEntry::make('number_of_seats')->label('عدد المقاعد')->numeric(),
                TextEntry::make('remaining_seats')->label('المقاعد المتبقية')->numeric(),
                TextEntry::make('ticket_price')->label('سعر التذكرة')->numeric(),
                ImageEntry::make('image')->label('صورة الرحلة')->placeholder('-'),
                TextEntry::make('created_at')->label('تاريخ الإنشاء')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->label('تاريخ التعديل')->dateTime()->placeholder('-'),
            ]);
    }
}
