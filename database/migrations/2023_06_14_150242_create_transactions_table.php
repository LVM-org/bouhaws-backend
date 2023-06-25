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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->text('description');
            $table->char('gateway');
            $table->integer('user_id');
            $table->integer('wallet_id');
            $table->char('dr_or_cr');
            $table->char('chargeable_id');
            $table->char('chargeable_type');
            $table->char('wallet_balance');
            $table->char('amount');
            $table->char('charges');
            $table->char('currency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
