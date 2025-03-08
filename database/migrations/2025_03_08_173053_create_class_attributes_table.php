<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_attributes', function (Blueprint $table) {
            $table->id();
            $table->string("class_name");
            $table->float("energy_multiplier");
            $table->float("health_multiplier");
            $table->float("exp_multiplier");
            $table->string("special_ability")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_attributes');
    }
};
