<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predefined_messages', function (Blueprint $table) {
            $table->id();

            // عنوان الرسالة (يُعرض في قائمة الاختيار)
            $table->string('title', 200)->nullable();

            // نص الرسالة الكامل (يظهر في bodyField)
            $table->text('body');

            // نوع الرسالة (اختياري: info, warning, promo, system, إلخ)
            $table->string('type', 50)->nullable();

            // المستخدم الذي أنشأ الرسالة (اختياري)
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // حالة التفعيل
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predefined_messages');
    }
};
