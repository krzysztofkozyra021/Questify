<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassAttribute>
 */
class ClassAttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "class_name" => [
                "Warrior",
                "Mage",
                "Rogue",
                "Cleric",
            ][$this->faker->numberBetween(0, 3)],

            "energy_multiplier" => $this->faker->randomFloat(2, 0.5, 2),
            "health_multiplier" => $this->faker->randomFloat(2, 0.5, 2),
            "exp_multiplier" => $this->faker->randomFloat(2, 0.5, 2),
            "special_ability" => $this->faker->sentence(7),
        ];
    }
}
