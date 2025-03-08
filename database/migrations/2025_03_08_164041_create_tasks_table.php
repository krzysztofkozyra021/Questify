<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("tasks", function (Blueprint $table): void {
            $table->id();
            $table->string("title");
            $table->string("description");
            $table->integer("difficulty_level")->references("difficulty_level")->on("task_difficulties");
            $table->string("reset_frequency")->references("id")->on("task_reset_configs");
            $table->dateTime("due_date");
            $table->boolean("is_completed")->default(false);
            $table->boolean("is_deadline_task")->default(false);
            $table->float("experience_reward")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("tasks");
    }
};
