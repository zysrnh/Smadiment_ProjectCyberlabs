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
        Schema::create('project_api_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('media', 30)->default('all')->index();
            $table->string('endpoint_key', 60)->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->longText('payload');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Composite unique index for fast lookups and atomic upserts
            $table->unique(
                ['project_id', 'media', 'endpoint_key', 'start_date', 'end_date'],
                'uq_proj_media_endpoint_dates'
            );

            // Additional composite index for filtering by project, media, and date range
            $table->index(
                ['project_id', 'media', 'start_date', 'end_date'],
                'idx_proj_media_dates'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_api_snapshots');
    }
};
