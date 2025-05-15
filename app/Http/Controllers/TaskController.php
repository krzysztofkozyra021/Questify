<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all tasks with their relations
        $tasks = $user->tasks()
            ->with(['tags', 'difficulty', 'resetConfig'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate tasks by type
        $dailies = $tasks->filter(fn($task) => $task->resetConfig->frequency_type === 'daily');
        $todos = $tasks->filter(fn($task) => $task->is_deadline_task);
        $habits = $tasks->filter(fn($task) => $task->resetConfig->frequency_type === 'custom');

        return Inertia::render('Tasks/Index', [
            'dailies' => $dailies,
            'todos' => $todos,
            'habits' => $habits,
            'difficulties' => TaskDifficulty::all(),
            'resetConfigs' => TaskResetConfig::all(),
        ]);
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
            'repeat_every' => 'required|integer|min:1',
            'repeat_unit' => 'required|in:day,week,month',
            'is_completed' => 'boolean',
            'is_deadline_task' => 'boolean',
            'experience_reward' => 'required|integer|min:1',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ]);

        // Create task and attach to user
        $task = auth()->user()->tasks()->create($validated);

        // Create and attach tags if provided
        if (!empty($validated['tags'])) {
            $task->tags()->createMany(
                collect($validated['tags'])->map(fn($tag) => ['name' => $tag])->toArray()
            );
        }

        return redirect()->route('tasks.index');
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

    public function complete(Task $task)
    {
        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Award experience to user
        $user = auth()->user();
        $userStats = $user->userStatistics;
        $expGain = $task->experience_reward * $task->difficulty->exp_multiplier;
        
        $userStats->current_experience += $expGain;
        $userStats->save();

        return back();
    }

    public function reset(Task $task)
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