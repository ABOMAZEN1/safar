<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_city_id')->constrained('cities')->cascadeOnDelete();
            $table->foreignId('to_city_id')->constrained('cities')->cascadeOnDelete();

            $table->foreignId('bus_id')
                ->constrained('buses')
                ->onDelete('RESTRICT');

            $table->foreignId('bus_driver_id')
                ->constrained('bus_drivers');

            $table->foreignId('travel_company_id')
                ->constrained('travel_companies')
                ->onDelete('RESTRICT');


            $table->dateTime('departure_datetime');
            $table->dateTime('return_datetime')->nullable();

            $table->decimal('duration_of_departure_trip', 5, 2);
            $table->decimal('duration_of_return_trip', 5, 2)->nullable();

            $table->string('trip_type');
            $table->integer('number_of_seats');
            $table->integer('remaining_seats');
            $table->decimal('ticket_price', 10, 2);

            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_trips');
    }
};
