<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("task_difficulties", function (Blueprint $table): void {
            $table->integer("difficulty_level")->primary()->unique();
            $table->string("difficulty_name");
            $table->float("energy_cost");
            $table->float("health_penalty");
            $table->float("base_exp_multiplier");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("task_difficulties");
    }
};
