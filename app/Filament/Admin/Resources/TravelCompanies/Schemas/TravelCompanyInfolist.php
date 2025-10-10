<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TravelCompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('user.name')->label('المالك'),
            TextEntry::make('company_name')->label('اسم الشركة'),
            TextEntry::make('contact_number')->label('رقم الشركة'),
            TextEntry::make('address')->label('العنوان'),
            ImageEntry::make('image_path')->label('شعار الشركة'),
            TextEntry::make('status')->label('الحالة')->badge(),
            TextEntry::make('commission_amount')->label('نسبة الربح (%)')->numeric(),
            TextEntry::make('created_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
