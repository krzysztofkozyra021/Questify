<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaskDifficulty;
use Illuminate\Database\Seeder;

class TaskDifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $task_difficulties = [
            [
                "difficulty_level" => 1,
                "difficulty_name" => "Easy",
                "energy_cost" => 10,
                "health_penalty" => 0,
                "base_exp_multiplier" => 1.0,
            ],
            [
                "difficulty_level" => 2,
                "difficulty_name" => "Medium",
                "energy_cost" => 15,
                "health_penalty" => 5,
                "base_exp_multiplier" => 1.2,
            ],
            [
                "difficulty_level" => 3,
                "difficulty_name" => "Hard",
                "energy_cost" => 20,
                "health_penalty" => 10,
                "base_exp_multiplier" => 1.5,
            ],
            [
                "difficulty_level" => 4,
                "difficulty_name" => "Very Hard",
                "energy_cost" => 25,
                "health_penalty" => 15,
                "base_exp_multiplier" => 2.0,
            ],
        ];

        foreach ($task_difficulties as $task_difficulty) {
            TaskDifficulty::create($task_difficulty);
        }
    }
}
