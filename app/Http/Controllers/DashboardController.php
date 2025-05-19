<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\QuoteService;
use App\Services\TagService;
use App\Services\TaskService;
use App\Services\TranslationService;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private readonly QuoteService $quoteService,
        private readonly TranslationService $translationService,
        private readonly TaskService $taskService,
        private readonly TagService $tagService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $userStatistics = $user->userStatistics;
        $userClassName = $userStatistics->classAttributes->class_name;
        $userClassExpMultiplier = $userStatistics->classAttributes->exp_multiplier;
        $difficulties = TaskDifficulty::all();
        $resetConfigs = TaskResetConfig::all();
        
        // Get tasks using TaskService
        $tasks = $this->taskService->getUserTasks($user);
        
        // Get tags using TagService
        $tags = $this->tagService->getUserTags($user);

        return Inertia::render("Dashboard", [
            "tasks" => $tasks,
            "tags" => $tags,
            "userStatistics" => $userStatistics,
            "user" => $user,
            "userClassName" => $userClassName,
            "userClassExpMultiplier" => $userClassExpMultiplier,
            "difficulties" => $difficulties,
            "resetConfigs" => $resetConfigs,
        ]);
    }

    public function create()
    {
        $formData = $this->taskService->getTaskFormData();
        
        return Inertia::render("Tasks/Create", $formData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "difficulty_level" => "required|integer|exists:task_difficulties,difficulty_level",
            "reset_frequency" => "required|integer|exists:task_reset_configs,id",
            "start_date" => "required|date",
            "due_date" => "nullable|date|after:start_date",
            "repeat_every" => "required|integer|min:1",
            "repeat_unit" => "required|in:day,week,month",
            "is_completed" => "boolean",
            "is_deadline_task" => "boolean",
            "experience_reward" => "required|integer|min:1",
            "tags" => "array",
            "tags.*" => "string|max:50",
        ]);

        $this->taskService->createTask(auth()->user(), $validated);

        return redirect()->route("dashboard");
    }

    public function completeTask(Task $task)
    {
        $this->taskService->completeTask($task);
        return back();
    }

    public function resetTask(Task $task)
    {
        $this->taskService->resetTask($task);
        return back();
    }

    public function getMotivationalQuote($locale)
    {
        $quote = $this->quoteService->getQuote($locale);
        
        if (empty($quote)) {
            return response()->json([
                'error' => 'Forismatic: Failed to fetch quote'
            ], 502);
        }

        $locale = strtoupper($locale);
        
        // Check if quote is already in the requested language
        $requestedLang = strtolower(substr($locale, 0, 2));
        if ($requestedLang === $quote['lang']) {
            return response()->json([
                [
                    'q' => $quote['text'],
                    'a' => $quote['author']
                ]
            ]);
        }

        $translatedText = $this->translationService->translate($quote['text'], $locale);
        
        if (!$translatedText) {
            return response()->json([
                'error' => 'DeepL: Failed to translate quote'
            ], 502);
        }

        return response()->json([
            [
                'q' => $translatedText,
                'a' => $quote['author']
            ]
        ]);
    }
}
