<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('code');
            $table->dateTime('expired_at');
            $table->dateTime('used_at')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();
            $table->index('code');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_verification_codes');
    }
};
