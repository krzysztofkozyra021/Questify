<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Collection;

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
        // Create the task
        $task = Task::create($data);

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

    public function completeTask(Task $task): void
    {
        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $user = $task->users()->first();
        $userStats = $user->userStatistics;
        $expGain = $task->experience_reward * $task->difficulty->exp_multiplier;
        
        $userStats->current_experience += $expGain;
        $userStats->save();
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
        return $tasks->filter(fn($task) => $task->resetConfig->frequency_type === 'daily');
    }

    private function filterTodos(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->is_deadline_task);
    }

    private function filterHabits(Collection $tasks): Collection
    {
        return $tasks->filter(fn($task) => $task->resetConfig->frequency_type === 'custom');
    }
} 