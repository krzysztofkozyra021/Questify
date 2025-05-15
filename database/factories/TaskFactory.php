<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get existing difficulty levels and reset configs
        $difficultyLevel = TaskDifficulty::inRandomOrder()->first()->difficulty_level;
        $resetConfig = TaskResetConfig::inRandomOrder()->first()->id;

        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'difficulty_level' => $difficultyLevel,
            'reset_frequency' => $resetConfig,
            'start_date' => now(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'repeat_every' => $this->faker->numberBetween(1, 7),
            'repeat_unit' => $this->faker->randomElement(['day', 'week', 'month']),
            'is_completed' => false,
            'is_deadline_task' => $this->faker->boolean,
            'experience_reward' => $this->faker->numberBetween(5, 50),
        ];
    }
}
