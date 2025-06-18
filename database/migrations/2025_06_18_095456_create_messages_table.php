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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('whatsapp_account_id');
            $table->unsignedBigInteger('contact_id');
            $table->string('phone_number', 20);
            $table->text('message_content');
            $table->string('media_path')->nullable();
            $table->enum('status', ['queued', 'sending', 'sent', 'delivered', 'read', 'failed'])->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unsignedInteger('created_year_month')->comment('YYYYMM format for partitioning');

            // For partitioned tables, primary key must include partition key
            $table->primary(['id', 'created_year_month']);
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['campaign_id', 'status']);
            $table->index(['contact_id', 'created_at']);
            $table->index(['phone_number', 'created_at']);
            $table->index('created_year_month');
        });

        // Create partitions for better performance with large datasets
        if (config('database.default') === 'mysql') {
            DB::statement('
                ALTER TABLE messages PARTITION BY RANGE (created_year_month) (
                    PARTITION p202501 VALUES LESS THAN (202502),
                    PARTITION p202502 VALUES LESS THAN (202503),
                    PARTITION p202503 VALUES LESS THAN (202504),
                    PARTITION p202504 VALUES LESS THAN (202505),
                    PARTITION p202505 VALUES LESS THAN (202506),
                    PARTITION p202506 VALUES LESS THAN (202507),
                    PARTITION p202507 VALUES LESS THAN (202508),
                    PARTITION p202508 VALUES LESS THAN (202509),
                    PARTITION p202509 VALUES LESS THAN (202510),
                    PARTITION p202510 VALUES LESS THAN (202511),
                    PARTITION p202511 VALUES LESS THAN (202512),
                    PARTITION p202512 VALUES LESS THAN (202601),
                    PARTITION p_future VALUES LESS THAN MAXVALUE
                );
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
