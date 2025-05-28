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
        $habits = $tasks['habits'];
        $dailyTasks = $tasks['dailies'];
        $todoTasks = $tasks['todos'];
        
        // Get tags using TagService
        $tags = $this->tagService->getUserTags($user);

        return Inertia::render("Dashboard", [
            "habits" => $habits,
            "dailyTasks" => $dailyTasks,
            "todoTasks" => $todoTasks,
            "tags" => $tags,
            "userStatistics" => $userStatistics,
            "user" => $user,
            "userClassName" => $userClassName,
            "userClassExpMultiplier" => $userClassExpMultiplier,
            "difficulties" => $difficulties,
            "resetConfigs" => $resetConfigs,
        ]);
    }


    public function getMotivationalQuote($locale)
    {
        try {
            $quote = $this->quoteService->getQuote($locale);
            
            if (empty($quote)) {
                \Log::error('Quote service returned empty response');
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
                \Log::error('Translation service failed', ['quote' => $quote, 'locale' => $locale]);
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
        } catch (\Exception $e) {
            \Log::error('Error in getMotivationalQuote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
