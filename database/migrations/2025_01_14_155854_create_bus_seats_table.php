<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_trip_id')->constrained('bus_trips')->cascadeOnDelete();
            $table->integer('seat_number');
            $table->boolean('is_reserved')->default(false);
            $table->timestamps();

            // Make seat_number unique per trip
            $table->unique(['bus_trip_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_seats');
    }
};
