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
        /// الفائدة عرض الاعمدة ضمن صفحة عرض كل شركات النقل 
        return $table
            ->columns([
            
                TextColumn::make('company_name')
                    ->label('الشركة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_number')
                    ->label('رقم الاتصال')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),
               
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
