<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Console\Commands\ResetDailies;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailiesResetTest extends TestCase
{
    use RefreshDatabase;

    public function testThatDailyTaskIsResetCorrectly(): void
    {
        $task = Task::create([
            "title" => "Test Daily Task",
            "type" => "daily",
            "reset_frequency" => 1,
            "is_completed" => true,
            "next_reset_at" => Carbon::now()->subDay(),
        ]);

        // Verify initial state
        $this->assertTrue($task->is_completed);
        $this->assertTrue($task->next_reset_at->isPast());

        $resetDailies = new ResetDailies();
        $resetDailies->handle(); // Run the reset dailies command

        // Refresh the task from database
        $task->refresh();

        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->next_reset_at->isFuture());
        $this->assertEquals(
            Carbon::now()->addDay()->startOfDay()->timestamp,
            $task->next_reset_at->startOfDay()->timestamp,
        );
    }

    public function testThatDailyTaskIsResetCorrectlyOnEveryTwoDays(): void
    {
        $task = Task::Create([
            "title" => "Test Daily Task",
            "type" => "daily",
            "reset_frequency" => 5, // every 2 days
            "is_completed" => true,
            "next_reset_at" => Carbon::now()->subMinutes(1),
        ]);

        $resetDailies = new ResetDailies();
        $resetDailies->handle();

        $task->refresh();

        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->next_reset_at->isFuture());
        $this->assertEquals(
            Carbon::now()->addDays(2)->startOfDay()->timestamp,
            $task->next_reset_at->startOfDay()->timestamp,
        );
    }

    public function testThatDailyTaskIsResetCorrectlyOnEveryTwoWeeks(): void
    {
        $task = Task::Create([
            "title" => "Test Daily Task",
            "type" => "daily",
            "reset_frequency" => 6, // every 2 weeks
            "is_completed" => true,
            "next_reset_at" => Carbon::now()->subMinutes(1),
        ]);

        $resetDailies = new ResetDailies();
        $resetDailies->handle();

        $task->refresh();

        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->next_reset_at->isFuture());
        $this->assertEquals(
            Carbon::now()->addWeeks(2)->startOfDay()->timestamp,
            $task->next_reset_at->startOfDay()->timestamp,
        );
    }

    public function testThatWeeklyTaskIsResetCorrectly(): void
    {
        $task = Task::create([
            "title" => "Test Weekly Task",
            "type" => "daily",
            "reset_frequency" => 2,
            "is_completed" => true,
            "next_reset_at" => Carbon::now()->subWeek(),
        ]);

        $resetDailies = new ResetDailies();
        $resetDailies->handle();

        $task->refresh();

        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->next_reset_at->isFuture());
    }

    public function testThatDailyTaskIsResetCorrectlyOnWeeklySchedule(): void
    {
        $testDate = Carbon::parse("2025-06-02 00:30:00"); // Monday
        Carbon::setTestNow($testDate);

        $task = Task::create([
            "title" => "Test Weekly Task",
            "type" => "daily",
            "reset_frequency" => 2,
            "is_completed" => true,
            "next_reset_at" => $testDate->copy()->subMinute(), // 1 minute ago
            "weekly_schedule" => ["monday", "wednesday", "friday"],
        ]);

        $resetDailies = new ResetDailies();
        $resetDailies->handle();

        $task->refresh();

        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->next_reset_at->isFuture());

        // Since today (Monday) is in the schedule, next reset should be Wednesday
        $expectedNext = $testDate->copy()->next("wednesday")->startOfDay();
        $this->assertEquals(
            $expectedNext->toDateTimeString(),
            $task->next_reset_at->toDateTimeString(),
        );

        $testDate = Carbon::parse("2025-06-04 00:30:00"); // Wednesday
        Carbon::setTestNow($testDate);
        $resetDailies = new ResetDailies();
        $resetDailies->handle();

        $task->refresh();

        $expectedLastScheduleDay = $testDate->copy()->next("friday")->startOfDay();
        $this->assertEquals(
            $expectedLastScheduleDay->toDateTimeString(),
            $task->next_reset_at->toDateTimeString(),
        );

        Carbon::setTestNow();
    }

    public function testThatDailyTaskResetsCorrectlyAcrossWeeklySchedule(): void
    {
        $schedule = ["monday", "wednesday", "friday"];

        $currentDate = Carbon::parse("2025-06-02 00:30:00"); // Monday 00:30

        $task = Task::create([
            "title" => "Weekly Reset Task",
            "type" => "daily",
            "reset_frequency" => 2, // weekly
            "is_completed" => true,
            "next_reset_at" => $currentDate->copy()->subMinute(), // already due
            "weekly_schedule" => $schedule,
        ]);

        $expectedDates = [
            "wednesday" => $currentDate->copy()->next("wednesday")->startOfDay(),
            "friday" => $currentDate->copy()->next("friday")->startOfDay(),
            "monday" => $currentDate->copy()->next("monday")->startOfDay()->addWeek(), // loop back to Monday (next week)
        ];

        foreach ($expectedDates as $day => $expectedNextDate) {
            Carbon::setTestNow($currentDate);

            $resetDailies = new ResetDailies();
            $resetDailies->handle();

            $task->refresh();

            $this->assertFalse($task->is_completed);
            $this->assertEquals(
                $expectedNextDate->toDateTimeString(),
                $task->next_reset_at->toDateTimeString(),
                "Failed asserting next reset is on {$day}",
            );

            // Mark it completed again for the next test cycle
            $task->is_completed = true;
            $task->next_reset_at = $expectedNextDate->copy()->subMinute(); // 1 min before again
            $task->save();

            // Move the current test time forward to the next expected day at 00:30
            $currentDate = $expectedNextDate->copy()->setTime(0, 30, 0);
        }

        Carbon::setTestNow(); // Clear mocked time
    }
}
