<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->word(),
            "description" => $this->faker->sentence(10),
            "difficulty_level" => $this->faker->numberBetween(1, 4),
            "reset_frequency" => $this->faker->numberBetween(1, 7),
            "due_date" => $this->faker->dateTimeBetween("now", "+1 year"),
            "is_completed" => $this->faker->boolean,
            "is_deadline_task" => $this->faker->boolean,
            "experience_reward" => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
