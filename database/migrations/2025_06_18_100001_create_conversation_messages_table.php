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
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['contact', 'ai', 'user']);
            $table->text('message_content');
            $table->enum('message_type', ['text', 'image', 'video', 'audio', 'document'])->default('text');
            $table->string('media_path')->nullable();
            $table->timestamp('timestamp');
            $table->json('metadata')->nullable(); // For storing AI provider info, etc.
            $table->timestamps();

            // Indexes
            $table->index(['conversation_id', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
    }
};
