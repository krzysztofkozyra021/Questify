<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get completed tasks count by type
        $completedTasksByType = Task::whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('is_completed', true)
        ->selectRaw('type, count(*) as count')
        ->groupBy('type')
        ->get()
        ->pluck('count', 'type')
        ->toArray();
            
        // Get highest streak for habits
        $highestStreakHabit = Task::whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('type', 'habit')
        ->orderBy('completed_count', 'desc')
        ->first();

        $mostMissedHabit = Task::whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('type', 'habit')
        ->orderBy('not_completed_count', 'desc')
        ->first();

        // Get all habits with their completion counts
        $habits = Task::whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('type', 'habit')
        ->select('id', 'title', 'completed_count')
        ->get();
            
        return Inertia::render('Statistics/Statistics', [
            'completedTasksByType' => $completedTasksByType,
            'highestStreakHabit' => $highestStreakHabit,
            'mostMissedHabit' => $mostMissedHabit,
            'habits' => $habits,
        ]);
    }
} 