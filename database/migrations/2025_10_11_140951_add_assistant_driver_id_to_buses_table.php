<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            if (!Schema::hasColumn('buses', 'assistant_driver_id')) {
                $table->unsignedBigInteger('assistant_driver_id')->nullable()->after('travel_company_id');
                $table->foreign('assistant_driver_id')
                      ->references('id')
                      ->on('assistant_drivers')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            if (Schema::hasColumn('buses', 'assistant_driver_id')) {
                $table->dropForeign(['assistant_driver_id']);
                $table->dropColumn('assistant_driver_id');
            }
        });
    }
};
