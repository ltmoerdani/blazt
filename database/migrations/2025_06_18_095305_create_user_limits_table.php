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
        Schema::create('user_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('messages_daily_limit')->default(1000);
            $table->integer('messages_monthly_limit')->default(30000);
            $table->integer('ai_requests_daily_limit')->default(100);
            $table->integer('whatsapp_accounts_limit')->default(1);
            $table->integer('contacts_limit')->default(10000);
            $table->integer('campaigns_limit')->default(50);
            $table->timestamps();

            $table->unique('user_id', 'unique_user_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_limits');
    }
};
