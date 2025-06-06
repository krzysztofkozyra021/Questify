<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ClassAttribute;
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
        $class = ClassAttribute::inRandomOrder()->first();

        return [
            "user_id" => $this->faker->numberBetween(1, 10),
            "class" => $class->id,
            "current_health" => 100,
            "current_energy" => 100,
            "max_health" => 100,
            "max_energy" => 100,
            "current_experience" => 0,
            "next_level_experience" => 100,
            "level" => 1,
            "energy_regen_rate" => 1,
            "last_login" => now(),
            "last_reset" => now(),
        ];
    }
}
