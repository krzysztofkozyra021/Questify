<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Check for tasks that need to be reset every 10 minutes

Schedule::command("tasks:increment-overdue-days")
    ->dailyAt("00:30")
    ->withoutOverlapping();

Schedule::command("tasks:reset-dailies")
    ->dailyAt("00:30")
    ->withoutOverlapping();

Schedule::command("users:reset-energy-and-health")
    ->dailyAt("00:30")
    ->withoutOverlapping();
