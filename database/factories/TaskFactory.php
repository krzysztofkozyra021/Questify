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
        $repeatUnits = ["day", "week", "month", "year"];

        return [
            "title" => $this->faker->word(),
            "description" => $this->faker->sentence(10),
            "difficulty_level" => $this->faker->numberBetween(1, 4),
            // Don't set reset_frequency here, will be overridden by seeder
            "reset_frequency" => null,
            "due_date" => $this->faker->dateTimeBetween("now", "+1 year"),
            "start_date" => $this->faker->dateTimeBetween("-1 month", "now"),
            "repeat_every" => $this->faker->numberBetween(1, 30),
            "repeat_unit" => $this->faker->randomElement($repeatUnits),
            "is_completed" => $this->faker->boolean,
            "is_deadline_task" => $this->faker->boolean,
            "experience_reward" => $this->faker->randomFloat(2, 1, 100),
            "checklist_items" => $this->faker->boolean(70) ? json_encode([
                ["text" => $this->faker->sentence(3), "completed" => $this->faker->boolean],
                ["text" => $this->faker->sentence(3), "completed" => $this->faker->boolean],
            ]) : null,
        ];
    }
}
