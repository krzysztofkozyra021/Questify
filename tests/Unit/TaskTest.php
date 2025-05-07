<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use App\Models\User;
use App\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
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

    public function test_task_can_be_created_with_valid_attributes()
    {
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

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Description', $task->description);
        $this->assertEquals($this->difficulty->difficulty_level, $task->difficulty_level);
        $this->assertEquals($this->resetConfig->id, $task->reset_frequency);
        $this->assertFalse($task->is_completed);
        $this->assertTrue($task->is_deadline_task);
        $this->assertEquals(100.0, $task->experience_reward);
    }

    public function test_task_belongs_to_difficulty()
    {
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

        $this->assertInstanceOf(TaskDifficulty::class, $task->difficulty);
        $this->assertEquals($this->difficulty->difficulty_level, $task->difficulty->difficulty_level);
    }

    public function test_task_belongs_to_reset_config()
    {
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

        $this->assertInstanceOf(TaskResetConfig::class, $task->resetConfig);
        $this->assertEquals($this->resetConfig->id, $task->resetConfig->id);
    }

    public function test_task_can_have_tags()
    {
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

        $tag = Tag::create([
            'name' => 'Test Tag',
            'description' => 'Test Tag Description'
        ]);

        $task->tags()->attach($tag);

        $this->assertCount(1, $task->tags);
        $this->assertInstanceOf(Tag::class, $task->tags->first());
        $this->assertEquals($tag->id, $task->tags->first()->id);
    }

    public function test_task_can_be_assigned_to_users()
    {
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

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $task->users()->attach($user, [
            'is_completed' => false,
            'progress' => 0
        ]);

        $this->assertCount(1, $task->users);
        $this->assertInstanceOf(User::class, $task->users->first());
        $this->assertEquals($user->id, $task->users->first()->id);
    }
} 