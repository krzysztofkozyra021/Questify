<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Cache;
use App\Services\TaskService;
use App\Services\TagService;
use App\Models\Task;
use App\Models\Tag;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;


Route::middleware(["auth:sanctum"])->group(function (): void {
      Route::get('/overdue-days-sync', function () {
        $syncDetails = Cache::get('overdue_sync_details');
        
        if (!$syncDetails) {
            return response()->json([
                'last_sync_timestamp' => 0,
                'last_sync_date' => null,
                'has_synced' => false,
                'affected_tasks' => 0,
            ]);
        }
    });
});

// Public API endpoints
Route::prefix('v1')->group(function () {
    // Task difficulties
    Route::get('/difficulties', function () {
        return response()->json([
            'success' => true,
            'data' => Cache::remember('task_difficulties', 3600, function () {
                return TaskDifficulty::select('difficulty_level', 'name', 'color', 'icon', 'energy_cost', 'health_penalty', 'exp_multiplier')->get();
            })
        ]);
    });

    // Task reset configurations
    Route::get('/reset-configs', function () {
        return response()->json([
            'success' => true,
            'data' => Cache::remember('task_reset_configs', 3600, function () {
                return TaskResetConfig::select('id', 'name', 'frequency_type', 'period', 'period_unit')->get();
            })
        ]);
    });

    // Get task statistics
    Route::get('/stats', function () {
        return response()->json([
            'success' => true,
            'data' => Cache::remember('task_statistics', 1800, function () {
                return [
                    'total_tasks' => Task::count(),
                    'tasks_by_type' => [
                        'habits' => Task::where('type', 'habit')->count(),
                        'dailies' => Task::where('type', 'daily')->count(),
                        'todos' => Task::where('type', 'todo')->count(),
                    ],
                    'difficulty_distribution' => Task::select('difficulty_level')
                        ->selectRaw('count(*) as count')
                        ->groupBy('difficulty_level')
                        ->get(),
                ];
            })
        ]);
    });

    // Get popular tags
    Route::get('/popular-tags', function () {
        return response()->json([
            'success' => true,
            'data' => Cache::remember('popular_tags', 1800, function () {
                return Tag::withCount('tasks')
                    ->orderBy('tasks_count', 'desc')
                    ->limit(10)
                    ->get(['id', 'name', 'tasks_count']);
            })
        ]);
    });

    // Get task templates
    Route::get('/task-templates', function () {
        return response()->json([
            'success' => true,
            'data' => Cache::remember('task_templates', 86400, function () {
                return [
                    'habits' => [
                        [
                            'title' => 'Daily Exercise',
                            'description' => 'Complete 30 minutes of exercise',
                            'difficulty_level' => 2,
                            'type' => 'habit',
                            'experience_reward' => 50,
                        ],
                        [
                            'title' => 'Read Books',
                            'description' => 'Read for 20 minutes',
                            'difficulty_level' => 1,
                            'type' => 'habit',
                            'experience_reward' => 30,
                        ],
                    ],
                    'dailies' => [
                        [
                            'title' => 'Morning Routine',
                            'description' => 'Complete your morning routine',
                            'difficulty_level' => 1,
                            'type' => 'daily',
                            'experience_reward' => 40,
                        ],
                        [
                            'title' => 'Evening Reflection',
                            'description' => 'Reflect on your day',
                            'difficulty_level' => 2,
                            'type' => 'daily',
                            'experience_reward' => 45,
                        ],
                    ],
                    'todos' => [
                        [
                            'title' => 'Project Planning',
                            'description' => 'Plan your next project',
                            'difficulty_level' => 3,
                            'type' => 'todo',
                            'experience_reward' => 100,
                        ],
                        [
                            'title' => 'Learning Goals',
                            'description' => 'Set learning goals for the week',
                            'difficulty_level' => 2,
                            'type' => 'todo',
                            'experience_reward' => 60,
                        ],
                    ],
                ];
            })
        ]);
    });

    // API documentation
    Route::get('/docs', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'version' => '1.0.0',
                'endpoints' => [
                    [
                        'path' => '/api/v1/difficulties',
                        'method' => 'GET',
                        'description' => 'Get all task difficulty levels and their attributes',
                    ],
                    [
                        'path' => '/api/v1/reset-configs',
                        'method' => 'GET',
                        'description' => 'Get all task reset configurations',
                    ],
                    [
                        'path' => '/api/v1/stats',
                        'method' => 'GET',
                        'description' => 'Get general statistics about tasks',
                    ],
                    [
                        'path' => '/api/v1/popular-tags',
                        'method' => 'GET',
                        'description' => 'Get the most used tags',
                    ],
                    [
                        'path' => '/api/v1/task-templates',
                        'method' => 'GET',
                        'description' => 'Get example task templates for different types',
                    ],
                ],
                'rate_limits' => [
                    'requests_per_minute' => 60,
                ],
            ]
        ]);
    });
});


