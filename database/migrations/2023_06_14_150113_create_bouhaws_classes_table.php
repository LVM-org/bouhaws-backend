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
        Schema::create('bouhaws_classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->char('title');
            $table->integer('user_id');
            $table->text('projects_id')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouhaws_classes');
    }
};
