<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_company_commissions', function (Blueprint $table) {
            $table->id();
            $table->decimal('commission_amount', 8, 2);
            $table->foreignId('travel_company_id')->constrained('travel_companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_company_commissions');
    }
};
