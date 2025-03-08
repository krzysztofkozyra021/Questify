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
            $table->float("health");
            $table->float("energy");
            $table->float("experience");
            $table->integer("level")->default(1);
            $table->float("energy_regen_rate");
            $table->timestamp("last_login");
            $table->timestamp("last_reset");
            $table->timestamps();
        });

        Schema::table("user_statistics", function (Blueprint $table): void {
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("class")->references("id")->on("class_attributes");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_statistics");
    }
};
