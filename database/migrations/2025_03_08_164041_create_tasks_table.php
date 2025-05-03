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
            $table->text("description")->nullable();
            $table->integer("difficulty_level")->default(2);
            $table->unsignedBigInteger("reset_frequency")->nullable(); //
            $table->timestamp("due_date")->nullable();
            $table->timestamp("start_date")->nullable();
            $table->integer("repeat_every")->default(1);
            $table->string("repeat_unit")->default('day');
            $table->boolean("is_completed")->default(false);
            $table->boolean("is_deadline_task")->default(false);
            $table->float("experience_reward")->default(10.0);
            $table->json("checklist_items")->nullable();
            $table->timestamps();
        });

        Schema::table("tasks", function (Blueprint $table): void {
            $table->foreign("difficulty_level")->references("difficulty_level")->on("task_difficulties");
            $table->foreign("reset_frequency")->references("id")->on("task_reset_configs");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("tasks");
    }
};
