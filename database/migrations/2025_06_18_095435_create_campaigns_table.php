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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('whatsapp_account_id')->constrained('whatsapp_accounts')->onDelete('cascade');
            $table->string('name');
            $table->text('template_content');
            $table->enum('target_type', ['all', 'group', 'custom'])->default('all');
            $table->foreignId('target_group_id')->nullable()->constrained('contact_groups')->onDelete('set null');
            $table->enum('status', ['draft', 'scheduled', 'running', 'completed', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_contacts')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_delivered')->default(0);
            $table->integer('messages_failed')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('scheduled_at');
            $table->index(['status', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
