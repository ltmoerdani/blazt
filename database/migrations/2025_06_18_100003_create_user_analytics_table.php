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
        Schema::create('user_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // message_sent, ai_request, login, etc.
            $table->integer('quantity')->default(1);
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamp('happened_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'activity_type', 'happened_at']);
            $table->index(['activity_type', 'happened_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_analytics');
    }
};
