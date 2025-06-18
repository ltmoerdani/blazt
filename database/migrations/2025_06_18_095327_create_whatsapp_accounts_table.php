<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone_number', 20)->unique();
            $table->string('display_name')->nullable();
            $table->enum('status', ['disconnected', 'connecting', 'connected', 'banned'])->default('disconnected');
            $table->json('session_data')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('health_check_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['phone_number', 'status']);
            $table->index('health_check_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_accounts');
    }
};
