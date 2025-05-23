<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TaskService
{
    public function getUserTasks(User $user): array
    {
        $tasks = $user->tasks()
            ->with(['tags', 'difficulty', 'resetConfig'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'dailies' => $this->filterDailies($tasks),
            'todos' => $this->filterTodos($tasks),
            'habits' => $this->filterHabits($tasks),
        ];
    }

    public function createTask(User $user, array $data): Task
    {
        // Ensure type is set
        if (!isset($data['type'])) {
            $data['type'] = 'todo'; // Default to todo if not specified
        }
        
        // Create the task
        $task = Task::create($data);
        $task->experience_reward = $data['difficulty_level'] * 10;
        $task->save();
        
        // Set next_reset_at if there's a reset configuration
        if (!empty($data['reset_frequency'])) {
            // For new tasks, we don't want to set a reset date until they're completed
            $task->next_reset_at = null;
            $task->save();
        }
        
        // Attach the task to the user
        $user->tasks()->attach($task->id, [
            'is_completed' => false,
            'progress' => 0
        ]);
        
        // Create and attach tags if provided
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $task->tags()->attach($tag->id);
            }
        }
        
        return $task;
    }

    public function updateTask(Task $task, array $data, array $tags = []): void
{
    try {
        $taskId = $task->id;
        
        if (!$taskId) {
            throw new \Exception('Task ID is missing');
        }
        
        \Log::info('UpdateTask called with:', [
            'task_id' => $taskId,
            'data' => $data,
            'tags' => $tags
        ]);
        
        // Update task
        $task->update($data);
        
        // Re-fetch the task to ensure we have the latest instance
        $task = Task::find($taskId);
        
        if (!$task) {
            throw new \Exception('Task not found after update');
        }
        
        // Process tags
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (!empty($tagName)) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['user_id' => $task->user_id ?? auth()->id()]
                );
                $tagIds[] = $tag->id;
            }
        }
        
        // Sync tags using the fresh task instance
        $task->tags()->sync($tagIds);
        
        // Load relationships
        $task->load('tags', 'difficulty');
        
    } catch (\Exception $e) {
        \Log::error('Error in updateTask:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function completeTask(Task $task): void
    {
        // Load the difficulty relationship if not already loaded
        if (!$task->relationLoaded('difficulty')) {
            $task->load('difficulty');
        }

        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_count' => $task->completed_count + 1,
        ]);
        
        // Calculate next reset date based on completion time
        if ($task->reset_frequency) {
            $task->next_reset_at = $this->calculateNextResetDate($task);
            $task->save();
        }
        
        $user = $task->users()->first();
        $userStats = $user->userStatistics;
        $userClassExpMultiplier = $userStats->classAttributes->exp_multiplier;
        $expGain = round($task->experience_reward * $task->difficulty->exp_multiplier * $userClassExpMultiplier);

        // Calculate energy penalty based on player 10% of max energy and task difficulty multiplier
        $playerMaxEnergy = $userStats->max_energy;
        $energyPenalty = round(($playerMaxEnergy * 0.1) * $task->difficulty->energy_cost);
        
        // Calculate health penalty based on player 10% of max health and task difficulty multiplier
        $playerMaxHealth = $userStats->max_health;
        $healthPenalty = round(($playerMaxHealth * 0.1) * $task->difficulty->health_penalty);


        $userStats->current_experience += $expGain;
        $userStats->current_health = max(0, $userStats->current_health - $healthPenalty);
        $userStats->current_energy = max(0, $userStats->current_energy - $energyPenalty);
        $userStats->save();
    }

    public function taskNotCompleted(Task $task): void
    {
        $task->update([
            'is_completed' => false,
            'completed_at' => null,
            'not_completed_count' => $task->not_completed_count + 1,
        ]);

        $user = $task->users()->first();
        $userStats = $user->userStatistics;
    }

    public function resetTask(Task $task): void
    {
        $task->update([
            'is_completed' => false,
            'completed_at' => null,
            'progress' => 0,
        ]);
    }

    public function getTaskFormData(): array
    {
        return [
            'difficulties' => TaskDifficulty::orderBy('difficulty_level')->get(),
            'resetConfigs' => TaskResetConfig::all(),
        ];
    }

    private function filterDailies(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === 'daily');
    }

    private function filterTodos(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === 'todo');
    }

    private function filterHabits(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->type === 'habit');
    }

    /**
     * Fix missing next_reset_at values for all tasks
     */
    public function fixMissingResetDates(): int
    {
        $fixedCount = 0;
        
        $tasks = Task::whereNotNull('reset_frequency')
            ->whereNull('next_reset_at')
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
        $tasksToReset = Task::where('is_completed', true)
            ->whereNotNull('next_reset_at')
            ->where('next_reset_at', '<=', now())
            ->get();
        
        foreach ($tasksToReset as $task) {
            // Reset the task
            $task->update([
                'is_completed' => false,
                'completed_at' => null,
                'progress' => 0,
            ]);
            
            // Reset the user task pivot table
            $task->users()->updateExistingPivot($task->users->pluck('id')->toArray(), [
                'is_completed' => false,
                'progress' => 0,
                'completed_at' => null
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
    public function calculateNextResetDate(Task $task): ?\Carbon\Carbon
    {
        $config = $task->resetConfig;
        $baseDate = $task->updated_at;
        $resetDate = null;
        
        switch ($config->frequency_type) {
            case 'daily':
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);
                break;
            case 'weekly':
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);
                break;
            case 'monthly':
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);
                break;
            case 'yearly':
                $resetDate = $baseDate->copy()->addDays($config->period_to_days);
                break;
            case 'custom':
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
            return 'No reset scheduled';
        }
        
        $config = $task->resetConfig;
        
        if ($config->period_to_days == 1) {
            return 'Resets daily';
        } else if ($config->period_to_days == 7) {
            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $dayOfWeek = json_decode($config->days_of_week, true)[0] ?? 1;
            return 'Resets weekly on ' . $daysOfWeek[$dayOfWeek % 7];
        } else if ($config->period_to_days >= 28 && $config->period_to_days <= 31) {
            return 'Resets monthly on day ' . ($config->day_of_month ?? 1);
        } else if ($config->period_to_days >= 365) {
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return 'Resets yearly on ' . ($config->day_of_month ?? 1) . ' ' . $months[($config->month ?? 1) - 1];
        } else {
            return 'Resets every ' . $config->period_to_days . ' days';
        }
    }
}