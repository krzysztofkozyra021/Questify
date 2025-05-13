<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("user_activity_logs", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("activity_type"); // e.g., 'login', 'register', 'password_reset'
            $table->string("ip_address")->nullable();
            $table->string("user_agent")->nullable();
            $table->json("additional_data")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_activity_logs");
    }
};
