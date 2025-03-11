<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTaskCollection>
 */
class UserTaskCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => $this->faker->numberBetween(1, 10),
            "task_id" => $this->faker->numberBetween(1, 10),
            "completed" => $this->faker->boolean,
            "completed_at" => $this->faker->dateTimeBetween("-1 year", "now"),
        ];
    }
}
