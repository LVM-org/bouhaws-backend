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
        Schema::create('project_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->integer('user_id');
            $table->integer('project_id');
            $table->char('current_milestone_index');
            $table->char('title');
            $table->text('description')->nullable();
            $table->longText('images')->nullable();
            $table->char('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_entries');
    }
};
