<?php

namespace App\Filament\Admin\Resources\CompanyPayments;

use App\Filament\Admin\Resources\CompanyPayments\Pages\CreateCompanyPayments;
use App\Filament\Admin\Resources\CompanyPayments\Pages\EditCompanyPayments;
use App\Filament\Admin\Resources\CompanyPayments\Pages\ListCompanyPayments;
use App\Filament\Admin\Resources\CompanyPayments\Pages\ViewCompanyPayments;
use App\Models\TravelCompany;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class CompanyPaymentsResource extends Resource
{
    protected static ?string $model = TravelCompany::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;
    protected static ?string $navigationLabel = 'مدفوعات الشركات';
    protected static ?int $navigationSort = 3;

    public static function getPluralLabel(): string
    {
        return 'اجمالي المدفوعات';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['busTrips.bookings']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('company_name')->label('اسم الشركة')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('عدد الحجوزات')
                    ->getStateUsing(fn($record) => $record->bookings_count),
                Tables\Columns\TextColumn::make('gross_revenue')
                    ->label('إجمالي المحصلات')
                    ->getStateUsing(fn($record) => number_format($record->gross_revenue, 2)),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('نسبة العمولة (%)')
                    ->getStateUsing(fn($record) => number_format($record->commission_amount ?? 0, 2)),
                Tables\Columns\TextColumn::make('total_commission')
                    ->label('إجمالي العمولة')
                    ->getStateUsing(fn($record) => number_format($record->total_commission, 2)),
                Tables\Columns\TextColumn::make('company_net')
                    ->label('نصيب الشركة')
                    ->getStateUsing(fn($record) => number_format($record->company_net, 2)),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['from']) && empty($data['to'])) {
                            return $query;
                        }

                        $from = $data['from'] ?? null;
                        $to = $data['to'] ?? null;

                        return $query->whereHas('busTrips.bookings', function ($q) use ($from, $to) {
                            $q->whereNull('canceled_at');
                            if ($from) {
                                $q->whereDate('created_at', '>=', $from);
                            }
                            if ($to) {
                                $q->whereDate('created_at', '<=', $to);
                            }
                        });
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyPayments::route('/'),
        ];
    }
}
