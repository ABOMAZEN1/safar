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
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('title_en', 255)->nullable();
            $table->text('body');
            $table->text('body_en')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('target_type', ['all', 'specific', 'segment'])->default('all');
            $table->json('target_ids')->nullable(); // Array of user IDs or segment criteria
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed'])->default('draft');
            $table->string('click_action')->nullable(); // Deep link or action
            $table->json('data')->nullable(); // Custom payload data
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index('target_type');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
