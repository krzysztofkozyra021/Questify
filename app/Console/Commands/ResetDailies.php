<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetDailies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "tasks:reset-dailies";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Reset dailies";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dailies = Task::where("type", "daily")->get();

        foreach ($dailies as $daily) {
            if ($daily->next_reset_at === null) {
                continue;
            }
            $nextResetAt = $daily->next_reset_at->setTimezone("UTC");
            $now = Carbon::now("UTC");

            if ($nextResetAt->isPast()) {
                $daily->is_completed = false;
                $daily->next_reset_at = $this->calculateNextResetDate($daily);
                $daily->save();
            }
        }
    }

    private function calculateNextResetDate(Task $task)
    {
        $now = Carbon::now("UTC");

        // Use reset_frequency to determine the frequency type
        // Assuming: 1 = daily, 2 = weekly, etc.
        switch ($task->reset_frequency) {
            case 1: // daily
                return $now->copy()->addDay();
            case 2: // weekly
                if ($task->weekly_schedule && is_array($task->weekly_schedule)) {
                    return $this->calculateNextWeeklyReset($task->weekly_schedule);
                }

                return $now->copy()->addWeek();
            case 3: // monthly
                return $now->copy()->addMonth();
            case 4: // yearly
                return $now->copy()->addYear();
            case 5: // every 2 days
                return $task->next_reset_at->copy()->addDays(2);
            case 6: // every 2 weeks
                return $task->next_reset_at->copy()->addWeeks(2);
            default:
                return $now->copy()->addDay();
        }
    }

    private function calculateNextWeeklyReset(array $weeklySchedule)
    {
        $now = Carbon::now("UTC");
        $currentDay = strtolower($now->format("l"));

        // Sort the schedule days
        $daysOfWeek = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
        usort($weeklySchedule, fn($a, $b) => array_search($a, $daysOfWeek, true) - array_search($b, $daysOfWeek, true));

        // Find the next scheduled day after today
        $currentDayIndex = array_search($currentDay, $daysOfWeek, true);

        foreach ($weeklySchedule as $scheduledDay) {
            $scheduledDayIndex = array_search($scheduledDay, $daysOfWeek, true);

            if ($scheduledDayIndex > $currentDayIndex) {
                return $now->copy()->next($scheduledDay)->startOfDay();
            }
        }

        // If no next day found this week, get the first day of next week
        return $now->copy()->next($weeklySchedule[0])->addWeek()->startOfDay();
    }
}
