<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function getUserTasks(User $user): array
    {
        $tasks = $user->tasks()
            ->with(["tags", "difficulty", "resetConfig"])
            ->orderBy("created_at", "desc")
            ->get();

        return [
            "dailies" => $this->filterDailies($tasks),
            "todos" => $this->filterTodos($tasks),
            "habits" => $this->filterHabits($tasks),
        ];
    }

    public function calculateExperienceReward(Task $task): int
    {
        // Base experience reward is difficulty level * 10
        return round($task->difficulty->difficulty_level * 10);
    }

    public function createTask(User $user, array $data): Task
    {
        // Ensure type is set
        if (!isset($data["type"])) {
            $data["type"] = "todo"; // Default to todo if not specified
        }

        \Log::info("Creating task with data:", [
            "user_id" => $user->id,
            "data" => $data,
        ]);

        try {
            return DB::transaction(function () use ($user, $data) {
                // Create the task
                $task = Task::create($data);
                $task->experience_reward = $this->calculateExperienceReward($task);
                $task->save();

                \Log::info("Task created:", [
                    "task_id" => $task->id,
                    "type" => $task->type,
                ]);

                // Set next_reset_at if there's a reset configuration
                if (!empty($data["reset_frequency"])) {
                    $task->next_reset_at = null;
                    $task->save();
                }

                // Attach the task to the user
                $user->tasks()->attach($task->id, [
                    "is_completed" => false,
                    "progress" => 0,
                ]);

                \Log::info("Task attached to user:", [
                    "task_id" => $task->id,
                    "user_id" => $user->id,
                ]);

                // Create and attach tags if provided
                if (!empty($data["tags"])) {
                    foreach ($data["tags"] as $tagName) {
                        $tag = Tag::firstOrCreate(["name" => $tagName]);
                        $task->tags()->attach($tag->id);
                    }
                }

                // Reload the task with its relationships
                $task->load(["users", "difficulty", "tags"]);

                return $task;
            });
        } catch (\Exception $e) {
            \Log::error("Failed to create task:", [
                "user_id" => $user->id,
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function updateHabit(Task $habit, array $data, array $tags = []): void
    {
        try {
            $habitId = $habit->id;

            if (!$habitId) {
                throw new \Exception("Habit ID is missing");
            }

            \Log::info("UpdateTask called with:", [
                "habit_id" => $habitId,
                "data" => $data,
                "tags" => $tags,
            ]);

            // Update task
            $habit->update($data);
            $habit->experience_reward = $this->calculateExperienceReward($habit);
            $habit->save();
            // Re-fetch the task to ensure we have the latest instance
            $habit = Task::find($habitId);

            if (!$habit) {
                throw new \Exception("Habit not found after update");
            }

            // Process tags
            $tagIds = [];

            foreach ($tags as $tagName) {
                $tagName = trim($tagName);

                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ["name" => $tagName],
                        ["user_id" => $habit->user_id ?? auth()->id()],
                    );
                    $tagIds[] = $tag->id;
                }
            }

            // Sync tags using the fresh task instance
            $habit->tags()->sync($tagIds);

            // Load relationships
            $habit->load("tags", "difficulty");
        } catch (\Exception $e) {
            \Log::error("Error in updateHabit:", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function completeHabit(Task $habit): void
    {
        // Load the difficulty relationship if not already loaded
        if (!$habit->relationLoaded("difficulty")) {
            $habit->load("difficulty");
        }

        $habit->update([
            "is_completed" => true,
            "completed_at" => now(),
            "completed_count" => $habit->completed_count + 1,
        ]);

        // Calculate next reset date based on completion time
        if ($habit->reset_frequency) {
            $habit->next_reset_at = $this->calculateNextResetDate($habit);
            $habit->save();
        }

        $user = $habit->users()->first();
        $userStats = $user->userStatistics;
        $userClassExpMultiplier = $userStats->classAttributes->exp_multiplier;
        $expGain = round($habit->experience_reward * $habit->difficulty->exp_multiplier * $userClassExpMultiplier);
        $userStats->current_experience += $expGain;
        $userStats->current_energy = max(0, $userStats->current_energy - $this->getEnergyPenalty($habit));
        $userStats->save();
    }

    // Calculate energy penalty based on player 10% of max energy and task difficulty multiplier
    public function getEnergyPenalty(Task $habit): int
    {
        $userStats = $habit->users()->first()->userStatistics;
        $playerMaxEnergy = $userStats->max_energy;

        return round(($playerMaxEnergy * 0.1) * $habit->difficulty->energy_cost);
    }

    // Calculate health penalty based on player 10% of max health and task difficulty multiplier
    public function getHealthPenalty(Task $habit): int
    {
        $userStats = $habit->users()->first()->userStatistics;
        $playerMaxHealth = $userStats->max_health;

        return round(($playerMaxHealth * 0.1) * $habit->difficulty->health_penalty);
    }


    public function completeTodo(Task $todo): void
    {
        // Load the difficulty relationship if not already loaded
        if (!$todo->relationLoaded("difficulty")) {
            $todo->load("difficulty");
        }

        $todo->update([
            "is_completed" => true,
            "completed_at" => now(),
        ]);

        $user = $todo->users()->first();

        if (!$user) {
            \Log::error("No user found for task", ["task_id" => $todo->id]);

            throw new \Exception("No user found for this task");
        }

        $userStats = $user->userStatistics;

        if (!$userStats) {
            \Log::error("No user statistics found for user", ["user_id" => $user->id]);

            throw new \Exception("No user statistics found for this user");
        }

        $userClassExpMultiplier = $userStats->classAttributes->exp_multiplier;
        $expGain = round($todo->experience_reward * $todo->difficulty->exp_multiplier * $userClassExpMultiplier);

        $userStats->current_experience += $expGain;
        $userStats->save();
    }

    public function updateTodo(Task $todo, array $data, array $tags = []): void
    {
        try {
            $todoId = $todo->id;

            if (!$todoId) {
                throw new \Exception("Todo ID is missing");
            }

            \Log::info("UpdateTodo called with:", [
                "todo_id" => $todoId,
                "data" => $data,
                "tags" => $tags,
            ]);

            // Update task
            $todo->update($data);
            $todo->experience_reward = $this->calculateExperienceReward($todo);
            $todo->save();
            // Re-fetch the task to ensure we have the latest instance
            $todo = Task::find($todoId);

            if (!$todo) {
                throw new \Exception("Todo not found after update");
            }

            // Process tags
            $tagIds = [];

            foreach ($tags as $tagName) {
                $tagName = trim($tagName);

                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ["name" => $tagName],
                        ["user_id" => $todo->user_id ?? auth()->id()],
                    );
                    $tagIds[] = $tag->id;
                }
            }

            // Sync tags using the fresh task instance
            $todo->tags()->sync($tagIds);

            // Load relationships
            $todo->load("tags", "difficulty");
        } catch (\Exception $e) {
            \Log::error("Error in updateTodo:", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function habitNotCompleted(Task $habit): void
    {
        $habit->update([
            "is_completed" => false,
            "completed_at" => null,
            "not_completed_count" => $habit->not_completed_count + 1,
        ]);

        $user = $habit->users()->first();
        $userStats = $user->userStatistics;
        $userStats->current_health = max(0, $userStats->current_health - $this->getHealthPenalty($habit));
        $userStats->save();
    }

    public function resetTask(Task $task): void
    {
        $task->update([
            "is_completed" => false,
            "completed_at" => null,
            "progress" => 0,
        ]);
    }

    public function getTaskFormData(): array
    {
        return [
            "difficulties" => TaskDifficulty::orderBy("difficulty_level")->get(),
            "resetConfigs" => TaskResetConfig::all(),
        ];
    }

    /**
     * Fix missing next_reset_at values for all tasks
     */
    public function fixMissingResetDates(): int
    {
        $fixedCount = 0;

        $tasks = Task::whereNotNull("reset_frequency")
            ->whereNull("next_reset_at")
            ->get();

        foreach ($tasks as $task) {
            $task->next_reset_at = $this->calculateNextResetDate($task);
            $task->save();
            $fixedCount++;
        }

        return $fixedCount;
    }

    /**
     * Reset all tasks that have reached their reset time
     */
    public function resetDueTasks(): int
    {
        $resetCount = 0;

        // Find all completed tasks that have passed their reset time
        $tasksToReset = Task::where("is_completed", true)
            ->whereNotNull("next_reset_at")
            ->where("next_reset_at", "<=", now())
            ->get();

        foreach ($tasksToReset as $task) {
            // Reset the task
            $task->update([
                "is_completed" => false,
                "completed_at" => null,
                "progress" => 0,
            ]);

            // Reset the user task pivot table
            $task->users()->updateExistingPivot($task->users->pluck("id")->toArray(), [
                "is_completed" => false,
                "progress" => 0,
                "completed_at" => null,
            ]);

            // Recalculate next reset time
            $task->next_reset_at = $this->calculateNextResetDate($task);
            $task->save();

            $resetCount++;
        }

        return $resetCount;
    }

    /**
     * Calculate the next reset date for a task based on its reset config
     * Simplified version using period_to_days
     */
    public function calculateNextResetDate(Task $task): ?Carbon
    {
        $config = $task->resetConfig;
        $baseDate = $task->updated_at;
        $resetDate = null;

        switch ($config->frequency_type) {
            case "daily":
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);

                break;
            case "weekly":
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);

                break;
            case "monthly":
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);

                break;
            case "yearly":
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);

                break;
            case "custom":
                break;
        }

        return $resetDate;
    }

    /**
     * Get a human-readable description of when a task will reset
     */
    public function getResetDescription(Task $task): string
    {
        if (!$task->resetConfig) {
            return "No reset scheduled";
        }

        $config = $task->resetConfig;

        if ($config->period_to_days === 1) {
            return "Resets daily";
        } else if ($config->period_to_days === 7) {
            $daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            $dayOfWeek = json_decode($config->days_of_week, true)[0] ?? 1;

            return "Resets weekly on " . $daysOfWeek[$dayOfWeek % 7];
        } else if ($config->period_to_days >= 28 && $config->period_to_days <= 31) {
            return "Resets monthly on day " . ($config->day_of_month ?? 1);
        } else if ($config->period_to_days >= 365) {
            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            return "Resets yearly on " . ($config->day_of_month ?? 1) . " " . $months[($config->month ?? 1) - 1];
        } else {
            return "Resets every " . $config->period_to_days . " days";
        }
    }

    private function filterDailies(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === "daily");
    }

    private function filterTodos(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === "todo");
    }

    private function filterHabits(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === "habit");
    }
}
