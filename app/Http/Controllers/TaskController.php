<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
    ) {
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

        $this->clearDashboardCache();

        return redirect()->back();
    }

    public function storeTodo(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
            "due_date" => "required|date",
            "start_date" => "required|date",
            "experience_reward" => "required|integer|min:1", 
            "tags" => "array",
            "tags.*" => "string|max:50",
            "type" => "required|string|in:habit,daily,todo",
        ]);

        $task = $this->taskService->createTask($user, $validated);

        $this->clearDashboardCache();

        return redirect()->back();
    }

    public function storeDaily(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
            "reset_frequency" => "required|integer|exists:task_reset_configs,id",
            "start_date" => "required|date",
            "weekly_schedule" => "nullable|array",
            "weekly_schedule.*" => "string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday",
            "tags" => "array",
            "tags.*" => "string|max:50",
            "type" => "required|string|in:habit,daily,todo",
            "is_completed" => "boolean",
            "experience_reward" => "required|integer|min:1",
        ]);

        $task = $this->taskService->createTask($user, $validated);

        $this->clearDashboardCache();

        return redirect()->back();
    }

    public function updateDaily(Request $request, Task $daily)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
            "reset_frequency" => "required|integer|exists:task_reset_configs,id",
            "weekly_schedule" => "nullable|array",
            "weekly_schedule.*" => "string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday",
            "tags" => "array",
            "tags.*" => "string|max:50",
        ]);

        $this->taskService->updateDaily($daily, $validated);

        $this->clearDashboardCache();

        return back()->with("success", "Daily updated successfully");
    }

    public function completeDaily(Task $daily)
    {
        $this->taskService->completeDaily($daily);

        $this->clearDashboardCache();

        return back()->with("success", "Daily completed successfully");
    }

    public function dailyNotCompleted(Task $daily)
    {
        $this->taskService->dailyNotCompleted($daily);

        $this->clearDashboardCache();

        return back()->with("success", "Daily set to not completed");
    }

    public function destroyTask(Task $task)
    {
        $this->taskService->deleteUserTask($task);

        $this->clearDashboardCache();

        return back();
    }

    public function updateHabit(Request $request, Task $habit)
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

            \Log::info("Validated data:", $validated);

            // Extract and process tags
            $tags = $validated["tags"] ?? [];
            unset($validated["tags"]);

            $this->taskService->updateHabit($habit, $validated, $tags);

            $this->clearDashboardCache();

            return back()->with("success", "Habit updated successfully");
        } catch (ValidationException $e) {
            \Log::error("Validation error:", ["errors" => $e->errors()]);

            throw $e;
        } catch (\Exception $e) {
            \Log::error("Error updating habit:", [
                "message" => $e->getMessage(),
            ]);

            return back()->withErrors(["message" => $e->getMessage()]);
        }
    }

    public function updateTodo(Request $request, Task $todo)
    {
        try {
            $validated = $request->validate([
                "title" => "required|string|max:255",
                "description" => "nullable|string",
                "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
                "due_date" => "required|date",
                "experience_reward" => "required|integer|min:1",
                "tags" => "array",
                "tags.*" => "string|max:50",
            ]);

            \Log::info("Validated data:", $validated);

            // Extract and process tags
            $tags = $validated["tags"] ?? [];
            unset($validated["tags"]);

            $this->taskService->updateTodo($todo, $validated, $tags);

            $this->clearDashboardCache();

            return back()->with("success", "Todo updated successfully");
        } catch (ValidationException $e) {
            \Log::error("Validation error:", ["errors" => $e->errors()]);

            throw $e;
        } catch (\Exception $e) {
            \Log::error("Error updating todo:", [
                "message" => $e->getMessage(),
            ]);

            return back()->withErrors(["message" => $e->getMessage()]);
        }
    }

    public function completeHabit(Task $habit)
    {
        $this->taskService->completeHabit($habit);

        $this->clearDashboardCache();

        return back()->with("success", "Habit completed successfully");
    }

    public function completeTodo(Task $todo)
    {
        try {
            $this->taskService->completeTodo($todo);

            $this->clearDashboardCache();

            return back()->with("success", "Todo completed successfully");
        } catch (\Exception $e) {
            \Log::error("Error completing todo:", [
                "message" => $e->getMessage(),
                "todo_id" => $todo->id,
            ]);

            return back()->withErrors(["message" => $e->getMessage()]);
        }
    }

    public function uncompleteTodo(Task $todo)
    {
        try {
            $this->taskService->uncompleteTodo($todo);

            $this->clearDashboardCache();

            return back()->with("success", "Todo uncompleted successfully");
        } catch (\Exception $e) {
            \Log::error("Error uncompleting todo:", [
                "message" => $e->getMessage(),
                "todo_id" => $todo->id,
            ]);

            return back()->withErrors(["message" => $e->getMessage()]);
        }
    }

    public function habitNotCompleted(Task $habit)
    {
        $this->taskService->habitNotCompleted($habit);

        $this->clearDashboardCache();

        return back()->with("success", "Habit set to not completed");
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
            "is_completed" => false,
            "completed_at" => null,
            "progress" => 0,
        ]);

        return back()->with("success", "Task reset successfully");
    }

    public function create()
    {
        $formData = $this->taskService->getTaskFormData();

        return Inertia::render("Tasks/Create", $formData);
    }

    public function clearDashboardCache(): void
    {
        Cache::forget("dashboard_data_" . auth()->user()->id);
    }
}
