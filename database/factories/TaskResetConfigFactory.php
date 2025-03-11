<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskResetConfig>
 */
class TaskResetConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "reset_frequency" => $this->faker->numberBetween(0, 6),
            "reset_time" => $this->faker->dateTimeBetween("now", "+1 year"),
        ];
    }
}
