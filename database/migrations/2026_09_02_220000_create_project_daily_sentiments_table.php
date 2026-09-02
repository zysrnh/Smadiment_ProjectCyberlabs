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
        Schema::create('project_daily_sentiments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->date('date')->index();
            $table->integer('positive')->default(0);
            $table->integer('neutral')->default(0);
            $table->integer('negative')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_daily_sentiments');
    }
};
