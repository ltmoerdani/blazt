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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone_number', 20);
            $table->string('name')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'phone_number'], 'unique_user_contact');
            $table->index(['user_id', 'is_active']);
            $table->index(['group_id', 'is_active']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
