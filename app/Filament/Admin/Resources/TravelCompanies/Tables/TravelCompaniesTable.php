<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TravelCompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('المالك')->searchable(),
                TextColumn::make('company_name')->label('اسم الشركة')->searchable(),
                TextColumn::make('contact_number')->label('رقم التواصل')->searchable(),
                TextColumn::make('address')->label('العنوان')->searchable(),
                ImageColumn::make('image_path')->label('الصورة'),
                TextColumn::make('status')->label('الحالة')->badge(),
                TextColumn::make('commission_amount')->label('العمولة')->numeric()->sortable(),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable()->toggleable(),
                TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
