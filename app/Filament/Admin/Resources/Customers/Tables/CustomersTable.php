<?php

namespace App\Filament\Admin\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('اسم المستخدم')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.phone_number')
                    ->label('رقم الهاتف')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('birth_date')->label('تاريخ الميلاد'),
                TextColumn::make('national_id')->label('الرقم الوطني'),
                TextColumn::make('gender')->label('الجنس'),
                TextColumn::make('address')->label('العنوان'),
                TextColumn::make('mother_name')->label('اسم الأم'),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteBulkAction::make(),
            ]);
        
    }
}