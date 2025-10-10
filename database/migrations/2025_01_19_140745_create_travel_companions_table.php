<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_trip_booking_id')->constrained('bus_trip_bookings', 'id')->cascadeOnDelete();
            $table->string('companion_name');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('travel_companions');
    }
};
