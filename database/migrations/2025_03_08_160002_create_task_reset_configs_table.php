<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("task_reset_configs", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
            $table->string("frequency_type");
            $table->integer("period")->default(1);
            $table->string("period_unit")->default('day');
            $table->json("days_of_week")->nullable();
            $table->integer("day_of_month")->nullable();
            $table->integer("month")->nullable();
            $table->time("reset_time")->default('00:00:00');
            $table->timestamps();
        });


        DB::table('task_reset_configs')->insert([
            [
                'name' => 'Daily',
                'frequency_type' => 'daily',
                'period' => 1,
                'period_unit' => 'day',
                'days_of_week' => null,
                'day_of_month' => null,
                'month' => null,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Weekly',
                'frequency_type' => 'weekly',
                'period' => 1,
                'period_unit' => 'week',
                'days_of_week' => json_encode([1]),
                'day_of_month' => null,
                'month' => null,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Monthly',
                'frequency_type' => 'monthly',
                'period' => 1,
                'period_unit' => 'month',
                'days_of_week' => null,
                'day_of_month' => 1,
                'month' => null,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Yearly',
                'frequency_type' => 'yearly',
                'period' => 1,
                'period_unit' => 'year',
                'days_of_week' => null,
                'day_of_month' => 1,
                'month' => 1,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Every 2 days',
                'frequency_type' => 'custom',
                'period' => 2,
                'period_unit' => 'day',
                'days_of_week' => null,
                'day_of_month' => null,
                'month' => null,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Every 2 weeks',
                'frequency_type' => 'custom',
                'period' => 2,
                'period_unit' => 'week',
                'days_of_week' => json_encode([1]),
                'day_of_month' => null,
                'month' => null,
                'reset_time' => '00:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists("task_reset_configs");
    }
};
