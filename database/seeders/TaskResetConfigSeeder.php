<?php

namespace Database\Seeders;

use App\Models\TaskResetConfig;
use Illuminate\Database\Seeder;

class TaskResetConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reset_configs = [
            [
                "reset_frequency" => 0,
                "reset_time" => now()->addDays(0),
            ],
            [
                "reset_frequency" => 1,
                "reset_time" => now()->addDays(1),
            ],
            [
                "reset_frequency" => 2,
                "reset_time" => now()->addDays(7),
            ],
            [
                "reset_frequency" => 3,
                "reset_time" => now()->addDays(30),
            ],
            [
                "reset_frequency" => 4,
                "reset_time" => now()->addDays(60),
            ],
            [
                "reset_frequency" => 5,
                "reset_time" => now()->addDays(180),
            ],
            [
                "reset_frequency" => 6,
                "reset_time" => now()->addDays(365),
            ],
        ];

        foreach ($reset_configs as $reset_config) {
            TaskResetConfig::create($reset_config);
        }
    }
}
