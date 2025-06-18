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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name');
            $table->enum('subscription_plan', ['trial', 'starter', 'pro', 'enterprise'])->default('trial');
            $table->enum('subscription_status', ['active', 'expired', 'suspended'])->default('active');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('subscription_status');
            $table->index('subscription_plan');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
