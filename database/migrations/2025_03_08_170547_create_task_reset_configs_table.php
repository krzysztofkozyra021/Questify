<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("task_reset_configs", function (Blueprint $table): void {
            $table->id();
            $table->integer("reset_frequency");
            $table->timestamp("reset_time");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("task_reset_configs");
    }
};
