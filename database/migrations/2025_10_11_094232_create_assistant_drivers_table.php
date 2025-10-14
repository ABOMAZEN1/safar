<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assistant_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('travel_company_id')->constrained('travel_companies', 'id')->cascadeOnDelete();
            
            // حقول إضافية اختيارية
            $table->string('license_number')->nullable()->comment('رقم رخصة القيادة');
            $table->date('license_expiry_date')->nullable()->comment('تاريخ انتهاء الرخصة');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->comment('حالة المعاون');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            
            $table->timestamps();
            
            // فهارس
            $table->unique('user_id');
            $table->index(['travel_company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_drivers');
    }
};
