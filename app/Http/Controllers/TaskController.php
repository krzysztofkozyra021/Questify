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

    public function storeHabit(Request $request)
    {
        // Create task and attach to user
        $user = auth()->user();
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
            "reset_frequency" => "required|integer|exists:task_reset_configs,id",
            "start_date" => "required|date",
            "is_deadline_task" => "boolean",
            "experience_reward" => "required|integer|min:1",
            "tags" => "array",
            "tags.*" => "string|max:50",
            "type" => "required|string|in:habit,daily,todo",
            "is_completed" => "boolean",
            "is_deadline_task" => "boolean",
            "experience_reward" => "required|integer|min:1",
            "type" => "required|string|in:habit,daily,todo",
        ]);

        $task = $this->taskService->createTask($user, $validated);
        return redirect()->back();
    }

    public function updateHabit(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                "title" => "required|string|max:255",
                "description" => "nullable|string",
                "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
                "reset_frequency" => "required|integer|exists:task_reset_configs,id",
                "tags" => "array",
                "tags.*" => "string|max:50",
            ]);
            
            \Log::info('Validated data:', $validated);
            
            // Extract and process tags
            $tags = $validated['tags'] ?? [];
            unset($validated['tags']);
            
            $this->taskService->updateTask($task, $validated, $tags);
            
            return back()->with('success', 'Habit updated successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating habit:', [
                'message' => $e->getMessage(),
            ]);
            
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function completeTask(Task $task)
    {
        $this->taskService->completeTask($task);
        return back()->with("success", "Task completed successfully");
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

    public function create()
    {
        $formData = $this->taskService->getTaskFormData();
        
        return Inertia::render("Tasks/Create", $formData);
    }


} 