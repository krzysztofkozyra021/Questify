<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskDifficulty>
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
            "difficulty" => [
                "Easy",
                "Medium",
                "Hard",
                "Very Hard",
            ][$this->faker->numberBetween(0, 3)],
            "energy_cost" => [
                5,
                10,
                15,
                20,
            ][$this->faker->numberBetween(0, 3)],
            "health_penalty" => [
                0,
                5,
                10,
                15,
            ][$this->faker->numberBetween(0, 3)],
            "base_exp_multiplier" => [
                1,
                1.5,
                2,
                2.5,
            ][$this->faker->numberBetween(0, 3)],
        ];
    }
}
