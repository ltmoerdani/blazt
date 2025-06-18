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
        Schema::create('ai_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('provider', ['openai', 'deepseek', 'claude']);
            $table->string('api_key')->nullable(); // Encrypted
            $table->string('model');
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->integer('max_tokens')->default(150);
            $table->boolean('active')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'active']);
            $table->unique(['user_id', 'provider']); // One config per provider per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_configurations');
    }
};
