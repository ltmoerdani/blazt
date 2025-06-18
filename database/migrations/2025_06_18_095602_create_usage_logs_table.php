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
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('usage_type', ['message', 'ai_request', 'api_call']);
            $table->integer('quantity')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedInteger('created_year_month')->comment('YYYYMM format for partitioning');

            $table->primary(['id', 'created_year_month']);
            $table->index(['user_id', 'usage_type', 'created_at']);
            $table->index('created_at');
            $table->index('created_year_month');
        });

        DB::statement('
            ALTER TABLE usage_logs PARTITION BY RANGE (created_year_month) (
                PARTITION p202401 VALUES LESS THAN (202402),
                PARTITION p202402 VALUES LESS THAN (202403),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            );
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};
