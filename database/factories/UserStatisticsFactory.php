<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserStatistics>
 */
class UserStatisticsFactory extends Factory
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
            "level" => $this->faker->numberBetween(1, 10),
            "class_id" => $this->faker->numberBetween(1, 4),
        ];
    }
}
