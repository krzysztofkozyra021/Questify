<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("user_statistics", function (Blueprint $table): void {
            $table->id();
            $table->integer("user_id");
            $table->integer("class");
            $table->integer("current_health")->default(100);
            $table->integer("current_energy")->default(100);
            $table->integer("max_health")->default(100);
            $table->integer("max_energy")->default(100);
            $table->integer("current_experience")->default(0);
            $table->integer("next_level_experience")->default(100);
            $table->integer("level")->default(1);
            $table->integer("energy_regen_rate")->default(1);
            $table->timestamp("last_login")->nullable();
            $table->timestamp("last_reset")->nullable();
            $table->timestamps();
        });

        Schema::table("user_statistics", function (Blueprint $table): void {
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("class")->references("id")->on("class_attributes");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_statistics");
    }
};
