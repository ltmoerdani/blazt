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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_name'); // trial, starter, pro, enterprise
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('paddle_subscription_id')->nullable();
            $table->enum('status', ['active', 'canceled', 'past_due', 'unpaid', 'expired'])->default('active');
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
