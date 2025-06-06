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
use Illuminate\Support\Facades\Log;

class TaskService
{
    public const DIFFICULTY_LEVEL_MULTIPLIER = 7;
    public const PLAYER_MAX_STATS_MULTIPLIER = 0.1;

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

    public function calculateExperienceReward(Task $task, User $user): int
    {
        $baseExp = match($task->type) {
            'todo' => 30,
            'daily' => 35,
            'habit' => 40,
            default => 30,
        };
        
        $expMultiplier = $task->difficulty->exp_multiplier;
        $userClassExpMultiplier = $user->userStatistics->classAttributes->exp_multiplier;

        return intval(round($baseExp * $expMultiplier * $userClassExpMultiplier));
    }

    public function calculateOverdueDays(Task $task): int
    {
        if (!$task->due_date) {
            return 0;
        }

        // Use UTC for all calculations
        $dueDate = Carbon::parse($task->due_date)->setTimezone("UTC")->startOfDay();
        $startDate = Carbon::parse($task->start_date)->setTimezone("UTC")->startOfDay();
        $now = Carbon::now()->setTimezone("UTC")->startOfDay();

        Log::debug("Calculating overdue days:", [
            "task_id" => $task->id,
            "due_date" => $dueDate->toDateTimeString(),
            "start_date" => $startDate->toDateTimeString(),
            "now" => $now->toDateTimeString(),
            "is_future" => $startDate->isFuture(),
            "is_gte" => $now->gte($dueDate),
            "diff_days" => $dueDate->diffInDays($now),
        ]);

        // If the task hasn't started yet, return 0
        if ($startDate->isFuture()) {
            return 0;
        }

        // If current date is greater than or equal to due date, calculate days between due date and now
        if ($now->gte($dueDate)) {
            $days = intval($dueDate->diffInDays($now));
            Log::info("Overdue days calculated:", [
                "task_id" => $task->id,
                "days" => $days,
            ]);

            return $days;
        }

        return 0;
    }

    public function deleteUserTask(Task $task): void
    {
        try {
            $task->delete();
        } catch (\Exception $e) {
            \Log::error("Error deleting task:", [
                "message" => $e->getMessage(),
                "task_id" => $task->id,
            ]);

            throw $e;
        }
    }

    public function createTask(User $user, array $data): Task
    {
        // Ensure type is set - default to todo if not specified
        if (!isset($data["type"])) {
            $data["type"] = "todo";
        }

        \Log::info("Creating task with data:", [
            "user_id" => $user->id,
            "data" => $data,
        ]);

        try {
            return DB::transaction(function () use ($user, $data) {
                $task = Task::create($data);

                $task->experience_reward = $this->calculateExperienceReward($task, $user);
                $task->overdue_days = $this->calculateOverdueDays($task);
                $task->save();

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
            $habit->experience_reward = $this->calculateExperienceReward($habit, $habit->users()->first());
            $habit->save();

            // Re-fetch the task to ensure we have the latest instance
            $habit = Task::find($habitId);

            if (!$habit) {
                throw new \Exception("Habit not found after update");
            }

            $tagIds = $this->processTags($habit, $tags);

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
        if (!$habit->relationLoaded("difficulty")) {
            $habit->load("difficulty");
        }

        $habit->update([
            "is_completed" => true,
            "completed_at" => now(),
            "completed_count" => $habit->completed_count + 1,
        ]);

        if ($habit->reset_frequency) {
            $habit->next_reset_at = $this->calculateNextResetDate($habit);
            $habit->save();
        }

        $user = $habit->users()->first();
        $userStats = $user->userStatistics;
        $expGain = $habit->experience_reward;
        $userStats->current_experience += $expGain;
        
        // Ensure energy values are rounded to integers
        $energyPenalty = round($this->getEnergyPenalty($habit));
        $userStats->current_energy = max(0, round($userStats->current_energy - $energyPenalty));
        $userStats->max_energy = round($userStats->max_energy);
        $userStats->save();
    }

    // Calculate energy penalty based on player percentage of max energy and task difficulty multiplier
    public function getEnergyPenalty(Task $habit): int
    {
        $userStats = $habit->users()->first()->userStatistics;
        $playerMaxEnergy = $userStats->max_energy;

        return intval(round(($playerMaxEnergy * self::PLAYER_MAX_STATS_MULTIPLIER) * $habit->difficulty->energy_cost));
    }

    // Calculate health penalty based on player percentage of max health and task difficulty multiplier
    public function getHealthPenalty(Task $habit): int
    {
        $userStats = $habit->users()->first()->userStatistics;
        $playerMaxHealth = $userStats->max_health;

        return intval(round(($playerMaxHealth * self::PLAYER_MAX_STATS_MULTIPLIER) * $habit->difficulty->health_penalty));
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

        $expGain = $todo->experience_reward;
        $healthPenalty = $todo->overdue_days;

        $userStats->current_experience += $expGain;
        $userStats->current_health = max(0, $userStats->current_health - $healthPenalty);
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
            $todo->experience_reward = $this->calculateExperienceReward($todo, $todo->users()->first());
            $todo->overdue_days = $this->calculateOverdueDays($todo);
            $todo->save();

            \Log::info("Todo updated:", [
                "todo_id" => $todoId,
                "overdue_days" => $todo->overdue_days,
            ]);

            // Re-fetch the task to ensure we have the latest instance
            $todo = Task::find($todoId);

            if (!$todo) {
                throw new \Exception("Todo not found after update");
            }

            $tagIds = $this->processTags($todo, $tags);
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

    public function updateDaily(Task $daily, array $data): void
    {
        $daily->update($data);
        $tagIds = $this->processTags($daily, $data["tags"]);
        $daily->tags()->sync($tagIds);
        $daily->experience_reward = $this->calculateExperienceReward($daily, $daily->users()->first());
        $daily->save();
    }

    public function completeDaily(Task $daily): void
    {
        $daily->update([
            "is_completed" => true,
            "completed_at" => now(),
        ]);

        // Set next_reset_at if there's a reset configuration
        if ($daily->reset_frequency) {
            $daily->next_reset_at = $this->calculateNextResetDate($daily);
            $daily->save();
        } else {
            $daily->next_reset_at = null;
            $daily->save();
        }

        $user = $daily->users()->first();
        $userStats = $user->userStatistics;
        $userStats->current_energy = max(0, $userStats->current_energy - $this->getEnergyPenalty($daily));
        $expGain = $daily->experience_reward;
        $userStats->current_experience += $expGain;
        $userStats->save();
    }

    public function dailyNotCompleted(Task $daily): void
    {
        // Load the difficulty relationship if not already loaded
        if (!$daily->relationLoaded("difficulty")) {
            $daily->load("difficulty");
        }

        $daily->update([
            "is_completed" => false,
            "completed_at" => null,
        ]);

        $daily->next_reset_at = null;
        $daily->is_completed = false;
        $daily->save();

        $user = $daily->users()->first();
        $userStats = $user->userStatistics;
        $expLoss = $daily->experience_reward;

        // Subtract experience and ensure it doesn't go below 0
        $userStats->current_experience = max(0, $userStats->current_experience - $expLoss);

        // Calculate energy to restore (same as the penalty that was taken)
        $energyToRestore = $this->getEnergyPenalty($daily);
        $userStats->current_energy = min($userStats->max_energy, $userStats->current_energy + $energyToRestore);

        $userStats->save();
    }

    public function uncompleteTodo(Task $todo): void
    {
        $todo->update([
            "is_completed" => false,
            "completed_at" => null,
        ]);

        // Load the difficulty relationship if not already loaded
        if (!$todo->relationLoaded("difficulty")) {
            $todo->load("difficulty");
        }

        $todo->update([
            "is_completed" => false,
            "completed_at" => null,
        ]);

        $todo->next_reset_at = null;
        $todo->is_completed = false;
        $todo->save();

        $user = $todo->users()->first();
        $userStats = $user->userStatistics;
        $expLoss = $todo->experience_reward;

        $healthRestore = $todo->overdue_days;
        $userStats->current_health = min($userStats->max_health, $userStats->current_health + $healthRestore);

        // Subtract experience and ensure it doesn't go below 0
        $userStats->current_experience = max(0, $userStats->current_experience - $expLoss);

        $userStats->save();
    }

    public function processTags(Task $task, array $tags): array
    {
        // Process tags
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);

            if (!empty($tagName)) {
                $tag = Tag::firstOrCreate(
                    ["name" => $tagName],
                    ["user_id" => $task->user_id ?? auth()->id()],
                );
                $tagIds[] = $tag->id;
            }
        }

        return $tagIds;
    }

    /**
     * Calculate the next reset date for a task based on its reset config
     * Simplified version using period_to_days
     */
    public function calculateNextResetDate(Task $task): ?Carbon
    {
        $config = $task->resetConfig;
        $baseDate = $task->updated_at->setTimezone("UTC");
        $resetDate = null;

        switch ($config->frequency_type) {
            case "daily":
                $resetDate = $baseDate->copy()->addDay();

                break;
            case "weekly":
                $resetDate = $baseDate->copy()->addWeek();

                break;
            case "monthly":
                $resetDate = $baseDate->copy()->addMonth();

                break;
            case "yearly":
                $resetDate = $baseDate->copy()->addYear();

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
