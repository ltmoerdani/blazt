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
        Schema::create('campaign_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->integer('total_contacts')->default(0);
            $table->integer('messages_queued')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_delivered')->default(0);
            $table->integer('messages_read')->default(0);
            $table->integer('messages_failed')->default(0);
            $table->decimal('delivery_rate', 5, 2)->default(0);
            $table->decimal('read_rate', 5, 2)->default(0);
            $table->integer('avg_delivery_time')->default(0);
            $table->timestamp('last_updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_analytics');
    }
};
