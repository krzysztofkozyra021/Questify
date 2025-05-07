<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private TaskDifficulty $difficulty;
    private TaskResetConfig $resetConfig;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->difficulty = TaskDifficulty::create([
            'difficulty_level' => 1,
            'difficulty_name' => 'Easy',
            'energy_cost' => 10.0,
            'health_penalty' => 5.0,
            'base_exp_multiplier' => 1.0
        ]);

        $this->resetConfig = TaskResetConfig::create([
            'reset_frequency' => 1,
            'reset_time' => now()->addDay()
        ]);
    }

    public function test_user_can_be_created_with_valid_attributes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_can_have_tasks()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'difficulty_level' => $this->difficulty->difficulty_level,
            'reset_frequency' => $this->resetConfig->id,
            'due_date' => now()->addDays(7),
            'is_completed' => false,
            'is_deadline_task' => true,
            'experience_reward' => 100.0,
        ]);

        $user->tasks()->attach($task, [
            'is_completed' => false,
            'progress' => 0
        ]);

        $this->assertCount(1, $user->tasks);
        $this->assertInstanceOf(Task::class, $user->tasks->first());
        $this->assertEquals($task->id, $user->tasks->first()->id);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_can_have_multiple_tasks()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $task1 = Task::create([
            'title' => 'Task 1',
            'description' => 'Description 1',
            'difficulty_level' => $this->difficulty->difficulty_level,
            'reset_frequency' => $this->resetConfig->id,
            'due_date' => now()->addDays(7),
            'is_completed' => false,
            'is_deadline_task' => true,
            'experience_reward' => 100.0,
        ]);

        $task2 = Task::create([
            'title' => 'Task 2',
            'description' => 'Description 2',
            'difficulty_level' => $this->difficulty->difficulty_level,
            'reset_frequency' => $this->resetConfig->id,
            'due_date' => now()->addDays(14),
            'is_completed' => false,
            'is_deadline_task' => true,
            'experience_reward' => 200.0,
        ]);

        $user->tasks()->attach($task1, ['is_completed' => false, 'progress' => 0]);
        $user->tasks()->attach($task2, ['is_completed' => false, 'progress' => 0]);

        $this->assertCount(2, $user->tasks);
        $this->assertEquals('Task 1', $user->tasks[0]->title);
        $this->assertEquals('Task 2', $user->tasks[1]->title);
    }
} 