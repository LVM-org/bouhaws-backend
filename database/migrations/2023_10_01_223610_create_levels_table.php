<?php

use App\Models\Level;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    protected $defaultLevels = [
        [
            "title" => "Level 1",
            "label" => "1",
            "min_points" => 0,
        ],
        [
            "title" => "Level 2",
            "label" => "2",
            "min_points" => 1000,
        ],
        [
            "title" => "Level 3",
            "label" => "3",
            "min_points" => 3000,
        ],
        [
            "title" => "Level 4",
            "label" => "4",
            "min_points" => 5000,
        ],
        [
            "title" => "Level 5",
            "label" => "5",
            "min_points" => 8000,
        ],
        [
            "title" => "Level 6",
            "label" => "6",
            "min_points" => 12000,
        ],
        [
            "title" => "Level 7",
            "label" => "7",
            "min_points" => 17000,
        ],
        [
            "title" => "Level 8",
            "label" => "8",
            "min_points" => 22000,
        ],
        [
            "title" => "Level 9",
            "label" => "9",
            "min_points" => 25000,
        ],
        [
            "title" => "Level 10",
            "label" => "10",
            "min_points" => 30000,
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->char('title');
            $table->char('label');
            $table->integer('min_points');
            $table->timestamps();
        });

        // Create default levels
        foreach ($this->defaultLevels as $level) {
            $newLevel = Level::create($level);
            $newLevel->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
