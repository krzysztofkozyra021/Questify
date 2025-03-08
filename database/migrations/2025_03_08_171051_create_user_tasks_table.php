<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("user_tasks", function (Blueprint $table): void {
            $table->id();
            $table->integer("user_id");
            $table->integer("task_id");
            $table->timestamp("completed_at")->nullable();
            $table->timestamp("reset_at")->nullable();
            $table->float("progress")->default(0);
            $table->timestamps();
        });

        Schema::table("user_tasks", function (Blueprint $table): void {
            $table->foreign("task_id")->references("id")->on("tasks")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_tasks");
    }
};
