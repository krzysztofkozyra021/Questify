<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Get user's tasks with appropriate relations
        $tasks = $user->tasks()
            ->with(['tags', 'difficulty', 'resetConfig'])
            ->orderBy('created_at', 'desc')
            ->get();

        $userStatistics = $user->userStatistics;

        // Pass data to the view
        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'userStatistics' => $userStatistics,
            'user' => $user,
        ]);
    }

    public function create()
    {
        // Get available difficulty levels and reset configurations for the form
        $difficulties = TaskDifficulty::orderBy('difficulty_level')->get();
        $resetConfigs = TaskResetConfig::all();

        return Inertia::render('Tasks/Create', [
            'difficulties' => $difficulties,
            'resetConfigs' => $resetConfigs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'required|integer|exists:difficulties,id',
            'reset_frequency' => 'required|integer|exists:reset_configs,id',
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

        // Assign task to the logged-in user
        $task = auth()->user()->tasks()->create($validated);

        if (!empty($validated['tags'])) {
            $task->tags()->createMany(
                collect($validated['tags'])->map(fn($tag) => ['name' => $tag])->toArray()
            );
        }

        return redirect()->route('dashboard');
    }
}
