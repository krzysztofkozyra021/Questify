<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Check for tasks that need to be reset every 10 minutes
        // $schedule->call(function (): void {
        //     $taskService = app(TaskService::class);
        //     $resetCount = $taskService->resetDueTasks();
        //     \Log::info("Reset {$resetCount} tasks");
        // })->everyTenMinutes();

Schedule::command("tasks:increment-overdue-days")
                ->dailyAt("00:30")
                 ->withoutOverlapping();

Schedule::command("tasks:reset-dailies")
                // ->dailyAt("00:30")
                 ->dailyAt("00:30")
                 ->withoutOverlapping();