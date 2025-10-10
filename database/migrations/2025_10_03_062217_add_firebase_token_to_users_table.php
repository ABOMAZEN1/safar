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
        Schema::table('users', function (Blueprint $table) {
            $table->string('firebase_token')->nullable()->after('phone_number');
            $table->timestamp('firebase_token_updated_at')->nullable()->after('firebase_token');
            
            $table->index('firebase_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['firebase_token']);
            $table->dropColumn(['firebase_token', 'firebase_token_updated_at']);
        });
    }
};
