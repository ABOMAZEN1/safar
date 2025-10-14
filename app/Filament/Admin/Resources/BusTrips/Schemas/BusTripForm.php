<?php

namespace App\Filament\Admin\Resources\BusTrips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\City;
use App\Models\Bus;
use App\Models\BusDriver;
use App\Models\TravelCompany;
use App\Models\User;

class BusTripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 🏙️ المدينة المنطلقة
                Select::make('from_city_id')
                    ->label('من المدينة')
                    ->options(function () {
                        return City::orderBy('name_en')->pluck('name_en', 'id');
                    })
                    ->searchable()
                    ->required(),

                // 🏙️ المدينة الوجهة
                Select::make('to_city_id')
                    ->label('إلى المدينة')
                    ->options(function () {
                        return City::orderBy('name_en')->pluck('name_en', 'id');
                    })
                    ->searchable()
                    ->required(),

                // 🚌 الباص
                Select::make('bus_id')
                    ->label('الباص')
                    ->options(function () {
                        return Bus::orderBy('details')->pluck('details', 'id');
                    })
                    ->searchable()
                    ->required(),

                // 👨‍✈️ السائق
                // لا يوجد حقل name مباشرة في bus_drivers، بل يجب جلب أسماء السائقين من جدول users (عبر user_id)
                Select::make('bus_driver_id')
                    ->label('السائق')
                    ->options(function () {
                        return BusDriver::with('user')->get()->pluck('user.name', 'id');
                    })
                    ->searchable()
                    ->required(),

                // 🏢 الشركة
                Select::make('travel_company_id')
                    ->label('شركة النقل')
                    ->options(function () {
                        return TravelCompany::orderBy('company_name')->pluck('company_name', 'id');
                    })
                    ->searchable()
                    ->required(),

                DateTimePicker::make('departure_datetime')
                    ->label('تاريخ المغادرة')
                    ->required(),

                DateTimePicker::make('return_datetime')
                    ->label('تاريخ العودة'),

                TextInput::make('duration_of_departure_trip')
                    ->label('مدة الذهاب (ساعات)')
                    ->required()
                    ->numeric(),

                TextInput::make('duration_of_return_trip')
                    ->label('مدة العودة (ساعات)')
                    ->numeric()
                    ->default(null),

                // نوع الرحلة: اختيار ثابت
                Select::make('trip_type')
                    ->label('نوع الرحلة')
                    ->options([
                        'one_way' => 'ذهاب فقط',
                        'round_trip' => 'ذهاب وعودة',
                    ])
                    ->required(),

                TextInput::make('number_of_seats')
                    ->label('عدد المقاعد')
                    ->required()
                    ->numeric(),

                TextInput::make('remaining_seats')
                    ->label('المقاعد المتبقية')
                    ->required()
                    ->numeric(),

                TextInput::make('ticket_price')
                    ->label('سعر التذكرة')
                    ->required()
                    ->numeric(),

                FileUpload::make('image')
                    ->label('صورة الرحلة')
                    ->image(),
            ]);
    }
}
