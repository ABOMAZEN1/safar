<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_trip_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->foreignId('bus_trip_id')
                ->constrained('bus_trips')
                ->cascadeOnDelete();
            $table->integer('reserved_seat_count');
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_departure_confirmed')->default(false);
            $table->boolean('is_return_confirmed')->default(false);
            $table->string('booking_status');
            $table->decimal('total_price', 10, 2);
            $table->string('reserved_seat_numbers');
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_trip_bookings');
    }
};
