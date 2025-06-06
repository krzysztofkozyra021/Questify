<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("class_attributes", function (Blueprint $table): void {
            $table->id();
            $table->string("class_name");
            $table->float("energy_multiplier");
            $table->float("health_multiplier");
            $table->float("exp_multiplier");
            $table->string("special_ability")->nullable();
            $table->timestamps();
        });

        // Insert the four basic character classes
        DB::table("class_attributes")->insert([
            [
                "class_name" => "Warrior",
                "energy_multiplier" => 1.6,
                "health_multiplier" => 1.8,
                "exp_multiplier" => 0.5,
                "special_ability" => "Damage resistance: Reduces damage taken from tasks with high difficulty.",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "class_name" => "Mage",
                "energy_multiplier" => 0.4,
                "health_multiplier" => 0.5,
                "exp_multiplier" => 2.0,
                "special_ability" => "Wisdom: Gain bonus experience for completing knowledge-based tasks.",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "class_name" => "Rogue",
                "energy_multiplier" => 2.0,
                "health_multiplier" => 0.5,
                "exp_multiplier" => 0.9,
                "special_ability" => "Efficiency: Can complete certain tasks with reduced energy cost.",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "class_name" => "Paladin",
                "energy_multiplier" => 1.1,
                "health_multiplier" => 1.1,
                "exp_multiplier" => 1.1,
                "special_ability" => "Inspiration: Has a chance to motivate you, restoring energy after completing tasks.",
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists("class_attributes");
    }
};
