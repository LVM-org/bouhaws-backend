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
        Schema::create('project_entry_comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->integer('user_id');
            $table->integer('project_entry_id');
            $table->longText('content');
            $table->boolean('is_reply')->default(false);
            $table->integer('replied_comment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_entry_comments');
    }
};
