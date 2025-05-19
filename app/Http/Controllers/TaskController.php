<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\TaskService;
class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'required|integer|exists:task_difficulties,difficulty_level',
            'reset_frequency' => 'required|integer|exists:task_reset_configs,id',
            'start_date' => 'required|date',
            'due_date' => 'nullable|date|after:start_date',
            'is_completed' => 'boolean',
            'is_deadline_task' => 'boolean',
            'experience_reward' => 'required|integer|min:1',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
            'type' => 'required|string|in:habit,daily,todo',
        ]);

        // Create task and attach to user
        $user = auth()->user();
        $task = $this->taskService->createTask($user, $validated);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'sometimes|required|integer|exists:task_difficulties,difficulty_level',
            'reset_frequency' => 'sometimes|required|integer|exists:task_reset_configs,id',
            'due_date' => 'nullable|date',
            'is_completed' => 'boolean',
            'progress' => 'numeric|min:0|max:100',
        ]);

        $task->update($validated);

        return back();
    }

    public function completeTask(Task $task)
    {
        if($this->getUserRemainingHealth() <= 0) {
            return back()->with('error', 'You do not have enough health to complete this task.');
        }
        if($this->getUserRemainingEnergy() <= 0) {
            return back()->with('error', 'You do not have enough energy to complete this task.');
        }
        $this->taskService->completeTask($task);
        return back();
    }
    
    public function taskNotCompleted(Task $task)
    {
        $this->taskService->taskNotCompleted($task);
        return back();
    }

    public function getUserRemainingHealth()
    {
        $user = auth()->user();
        $userStats = $user->userStatistics;
        return $userStats->current_health;
    }

    public function getUserRemainingEnergy()
    {
        $user = auth()->user();
        $userStats = $user->userStatistics;
        return $userStats->current_energy;
    }


    public function resetTask(Task $task)
    {
        $task->update([
            'is_completed' => false,
            'completed_at' => null,
            'progress' => 0,
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
} 