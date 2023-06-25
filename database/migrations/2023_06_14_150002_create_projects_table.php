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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->char('title');
            $table->integer('user_id');
            $table->dateTime('end_date');
            $table->char('prize');
            $table->char('currency');
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->text('photo_url')->nullable();
            $table->char('type');
            $table->char('total_points');
            $table->integer('project_category_id');
            $table->char('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
