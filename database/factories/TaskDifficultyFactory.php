<?php

namespace Database\Factories;

use App\Models\TaskDifficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskDifficulty>
 */
class TaskDifficultyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           "difficulty_level" => [
               1,2,3,4
           ][ $this->faker->numberBetween(0, 3) ],
            "difficulty_name" => [
                "Easy",
                "Medium",
                "Hard",
                "Very Hard",
            ][$this->faker->numberBetween(0, 3)],
            "energy_cost" => $this->faker->numberBetween(1, 20),
            "health_penalty" => $this->faker->numberBetween(1, 20),
            "base_exp_multiplier" => $this->faker->randomFloat(2, 1, 2),
        ];
    }
}
