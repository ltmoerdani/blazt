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
        Schema::create('daily_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_delivered')->default(0);
            $table->integer('messages_failed')->default(0);
            $table->integer('campaigns_created')->default(0);
            $table->integer('campaigns_completed')->default(0);
            $table->integer('ai_requests_used')->default(0);
            $table->integer('unique_contacts_messaged')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date'], 'unique_user_date');
            $table->index('date');
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_analytics');
    }
};
