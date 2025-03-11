<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskResetConfig;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all valid reset_frequency IDs from the database
        $validResetFrequencies = TaskResetConfig::pluck("id")->toArray();

        // If empty, stop execution and show error
        if (empty($validResetFrequencies)) {
            $this->command->error("No TaskResetConfig records found, Run seeder first.");

            return;
        }

        Task::factory(50)->create([
            // Pick a random valid reset_frequency from the existing ones
            "reset_frequency" => fn() => $validResetFrequencies[array_rand($validResetFrequencies)],
        ]);
    }
}
