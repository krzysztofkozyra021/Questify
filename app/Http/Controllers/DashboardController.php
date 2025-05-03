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
        // Pobierz zalogowanego użytkownika
        $user = auth()->user();

        // Pobierz zadania użytkownika z odpowiednimi relacjami
        $tasks = $user->tasks()
            ->with(['difficulty', 'resetConfig', 'tags'])
            ->get()
            ->map(function ($task) {
                $task->is_completed = $task->pivot->is_completed;
                $task->completed_at = $task->pivot->completed_at;
                $task->progress = $task->pivot->progress;
                return $task;
            });

        // Przekaż dane do widoku
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks
        ]);
    }

    public function create()
    {
        // Pobierz dostępne poziomy trudności i konfiguracje resetu do formularza
        $difficulties = TaskDifficulty::orderBy('difficulty_level')->get();
        $resetConfigs = TaskResetConfig::all();

        return Inertia::render('Tasks/Create', [
            'difficulties' => $difficulties,
            'resetConfigs' => $resetConfigs
        ]);
    }

    public function store(Request $request)
    {
        // Walidacja danych
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'required|integer|exists:task_difficulties,difficulty_level',
            'reset_frequency' => 'required|integer|exists:task_reset_configs,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'repeat_every' => 'required|integer|min:1',
            'repeat_unit' => 'required|string|in:day,week,month,year',
            'is_completed' => 'boolean',
            'is_deadline_task' => 'boolean',
            'experience_reward' => 'nullable|numeric',
            'checklist_items' => 'nullable|array'
        ]);

        // Przygotuj dane do zapisania
        if (isset($validated['checklist_items']) && is_array($validated['checklist_items'])) {
            $validated['checklist_items'] = json_encode($validated['checklist_items']);
        }

        // Tworzenie nowego zadania
        $task = Task::create($validated);

        // Przypisanie zadania do zalogowanego użytkownika
        auth()->user()->tasks()->attach($task->id, [
            'is_completed' => false,
            'progress' => 0
        ]);

        return redirect()->route('dashboard')->with('success', __('Task created successfully.'));
    }
}
