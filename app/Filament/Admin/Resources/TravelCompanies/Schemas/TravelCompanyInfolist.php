<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Image as ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text as TextEntry;
use Filament\Schemas\Schema;

class TravelCompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الشركة')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make(fn($record) => 'اسم الشركة: ' . ($record->company_name ?? '')),
                                TextEntry::make(fn($record) => 'رقم الاتصال: ' . ($record->contact_number ?? '')),
                                TextEntry::make(fn($record) => 'العنوان: ' . ($record->address ?? '')),
                            ]),
                    ]),

                Section::make('شعار الشركة')
                    ->schema([
                        ImageEntry::make(fn($record) => $record->image_path, 'الشعار')
                            ->imageHeight(120),
                    ]),
            ]);
    }
}
