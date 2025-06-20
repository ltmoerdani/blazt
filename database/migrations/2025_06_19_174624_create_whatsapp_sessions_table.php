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
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->constrained('whatsapp_accounts')->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->enum('status', ['connecting', 'connected', 'disconnected', 'failed'])->default('connecting');
            $table->string('qr_code')->nullable();
            $table->json('session_data')->nullable();
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['whatsapp_account_id', 'status']);
            $table->index(['session_id', 'status']);
            $table->index('last_ping_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
