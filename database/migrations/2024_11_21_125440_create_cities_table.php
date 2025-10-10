<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->unsignedInteger('population')->default(0);
            $table->timestamps();

            $table->index('name_en');
            $table->index('name_ar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
