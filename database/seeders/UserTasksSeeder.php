<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error("No User records found. Run user seeder first.");

            return;
        }

        // Create tasks without user_id (since we'll use the pivot table)
        $tasks = Task::factory(5 * $users->count())->create();

        // Associate tasks with users through the pivot table
        foreach ($users as $user) {
            // Get 5 random tasks for each user
            $randomTasks = $tasks->random(min(5, $tasks->count()));

            // Attach tasks to user with pivot data
            foreach ($randomTasks as $task) {
                $user->tasks()->attach($task->id, [
                    "is_completed" => false,
                    "completed_at" => null,
                    "progress" => 0,
                ]);

                // Add tags to the task
                $task->tags()->attach(rand(1, 20));
            }
        }
    }
}
