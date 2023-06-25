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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->integer('user_id');
            $table->text('photo_url')->nullable();
            $table->char('points')->default('0');
            $table->longText('bio')->nullable();
            $table->char('school')->nullable();
            $table->char('student_number')->nullable();
            $table->char('year_of_enrollment')->nullable();
            $table->char('type');
            $table->text('enrolled_courses_uuid')->nullable();
            $table->text('enrolled_classes_uuid')->nullable();
            $table->boolean('push_notification_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
