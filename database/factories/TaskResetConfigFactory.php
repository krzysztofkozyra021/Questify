<?php

namespace Database\Factories;

use App\Models\TaskResetConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskResetConfigFactory extends Factory
{
    protected $model = TaskResetConfig::class;

    public function definition(): array
    {
        $frequencyTypes = ['daily', 'weekly', 'monthly', 'yearly', 'custom'];
        $frequencyType = $this->faker->randomElement($frequencyTypes);

        $data = [
            'name' => ucfirst($frequencyType),
            'frequency_type' => $frequencyType,
            'period' => 1,
            'period_unit' => 'day',
            'reset_time' => '00:00:00',
        ];

        switch ($frequencyType) {
            case 'weekly':
                $data['days_of_week'] = json_encode([1]); // Monday
                break;
            case 'monthly':
                $data['day_of_month'] = 1;
                break;
            case 'yearly':
                $data['day_of_month'] = 1;
                $data['month'] = 1;
                break;
            case 'custom':
                $data['period'] = $this->faker->numberBetween(2, 7);
                $data['period_unit'] = $this->faker->randomElement(['day', 'week', 'month']);
                break;
        }

        return $data;
    }
} 