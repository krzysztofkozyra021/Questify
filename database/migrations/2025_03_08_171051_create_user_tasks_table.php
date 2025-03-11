<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create("user_tasks", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("task_id")->constrained()->onDelete("cascade");
            $table->boolean("is_completed")->default(false);
            $table->timestamp("completed_at")->nullable();
            $table->float("progress")->default(0);
            $table->timestamps();

            // Add a unique constraint to prevent duplicate user-task assignments
            $table->unique(["user_id", "task_id"]);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists("user_tasks");
    }
};
