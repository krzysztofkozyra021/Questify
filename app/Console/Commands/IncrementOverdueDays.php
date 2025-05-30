<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IncrementOverdueDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:increment-overdue-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment overdue days for tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueTasks = Task::where("is_completed", false)
            ->where("due_date", "<", Carbon::now())
            ->get();

        foreach ($overdueTasks as $task) {
            $task->increment("overdue_days");
            $task->save();
        }

        Cache::put("last_overdue_days_sync", now()->timestamp, now()->addDays(7));
        Cache::put('overdue_sync_details', [
            'timestamp' => now()->timestamp,
            'date' => now()->toDateTimeString(),
            'affected_tasks' => $overdueTasks->count(),
            'completed_at' => now()->toISOString(),
        ], now()->addDays(7));

        Log::info("Incremented overdue days for {$overdueTasks->count()} tasks");
    }
}
