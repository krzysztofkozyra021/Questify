<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("task_tag", function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger("task_id");
            $table->unsignedBigInteger("tag_id");
            $table->timestamps();
        });

        Schema::table("task_tag", function (Blueprint $table): void {
            $table->foreign("task_id")->references("id")->on("tasks")->onDelete("cascade");
            $table->foreign("tag_id")->references("id")->on("tags")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("task_tag");
    }
};
