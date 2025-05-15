<?php

namespace Database\Factories;

use App\Models\TaskDifficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDifficultyFactory extends Factory
{
    protected $model = TaskDifficulty::class;

    private static $difficultyLevels = [1, 2, 3, 4];
    private static $currentIndex = 0;

    public function definition(): array
    {
        if (self::$currentIndex >= count(self::$difficultyLevels)) {
            self::$currentIndex = 0;
        }

        $level = self::$difficultyLevels[self::$currentIndex++];
        $names = [
            1 => 'Very Easy',
            2 => 'Easy',
            3 => 'Medium',
            4 => 'Hard'
        ];

        $colors = [
            1 => '#44CF6C',
            2 => '#3A9BDC',
            3 => '#E6BC2F',
            4 => '#E03F3F'
        ];

        $icons = [
            1 => 'star',
            2 => 'fire',
            3 => 'shield',
            4 => 'sword'
        ];

        $energyCosts = [
            1 => 0.5,
            2 => 0.8,
            3 => 1.2,
            4 => 1.5
        ];

        $healthPenalties = [
            1 => 0.1,
            2 => 0.3,
            3 => 0.5,
            4 => 0.7
        ];

        $expMultipliers = [
            1 => 1.0,
            2 => 1.5,
            3 => 2.0,
            4 => 2.5
        ];

        return [
            'difficulty_level' => $level,
            'name' => $names[$level],
            'color' => $colors[$level],
            'icon' => $icons[$level],
            'energy_cost' => $energyCosts[$level],
            'health_penalty' => $healthPenalties[$level],
            'exp_multiplier' => $expMultipliers[$level],
        ];
    }
} 