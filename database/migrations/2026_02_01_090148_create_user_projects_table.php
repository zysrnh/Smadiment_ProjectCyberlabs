<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('project_id'); // ID dari Drone Emprit API
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['user_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_projects');
    }
};