<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("task_difficulties", function (Blueprint $table): void {
            $table->integer("difficulty_level")->primary()->unique();
            $table->string("name");
            $table->string("color")->default("#8a8a8a");
            $table->string("icon")->nullable(); // optional icon
            $table->float("energy_cost")->default(1.0);
            $table->float("health_penalty")->default(0.0);
            $table->float("exp_multiplier")->default(1.0);
            $table->timestamps();
        });

        DB::table("task_difficulties")->insert([
            [
                "difficulty_level" => 1,
                "name" => "Very easy",
                "color" => "#44CF6C", // green
                "exp_multiplier" => 0.75,
                "energy_cost" => 0.5,
                "health_penalty" => 0.0,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "difficulty_level" => 2,
                "name" => "Easy",
                "color" => "#3A9BDC", // blue
                "exp_multiplier" => 1.0,
                "energy_cost" => 1.0,
                "health_penalty" => 0.25,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "difficulty_level" => 3,
                "name" => "Medium",
                "color" => "#E6BC2F", // yellow
                "exp_multiplier" => 1.5,
                "energy_cost" => 1.5,
                "health_penalty" => 0.5,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "difficulty_level" => 4,
                "name" => "Hard",
                "color" => "#E03F3F", // red
                "exp_multiplier" => 2.5,
                "energy_cost" => 2.0,
                "health_penalty" => 1.0,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists("task_difficulties");
    }
};
