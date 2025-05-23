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
            $table->unsignedBigInteger("reset_frequency")->nullable(); 
            $table->timestamp("due_date")->nullable();
            $table->timestamp("start_date")->nullable();
            $table->boolean("is_completed")->default(false);
            $table->boolean("is_deadline_task")->default(false);
            $table->integer("experience_reward")->default(10);
            $table->json("checklist_items")->nullable();
            $table->string("type")->default("habit");
            $table->timestamp("next_reset_at")->nullable();
            $table->integer("completed_count")->default(0);
            $table->integer("not_completed_count")->default(0);
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
