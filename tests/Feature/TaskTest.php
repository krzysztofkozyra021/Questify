<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserStatistics;
use App\Models\ClassAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a class attribute (use the first seeded one)
        $class = ClassAttribute::factory()->create([
            'class_name' => 'TestClass',
            'energy_multiplier' => 1.0,
            'health_multiplier' => 1.0,
            'exp_multiplier' => 1.0,
        ]);
        
        // Create test user with verified email and known password
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        
        // Create user statistics for the user
        UserStatistics::factory()->create([
            'user_id' => $this->user->id,
            'class' => $class->id,
        ]);
        
        // Get seeded difficulty levels
        $this->difficulties = TaskDifficulty::all();
        
        // Create test reset configs
        $this->resetConfigs = TaskResetConfig::factory()->count(1)->create([
            'frequency_type' => 'daily'
        ]);
    }

    public function test_user_can_create_task()
    {
        $response = $this->actingAs($this->user)->post('/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'difficulty_level' => $this->difficulties->first()->difficulty_level,
            'reset_frequency' => $this->resetConfigs->first()->id,
            'start_date' => now(),
            'due_date' => now()->addDays(7),
            'repeat_every' => 1,
            'repeat_unit' => 'day',
            'is_completed' => false,
            'is_deadline_task' => true,
            'experience_reward' => 10,
            'tags' => ['test', 'important']
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);
    }

    public function test_user_can_view_their_tasks()
    {
        // Create a task with proper relationships
        $task = Task::factory()->create([
            'difficulty_level' => $this->difficulties->first()->difficulty_level,
            'reset_frequency' => $this->resetConfigs->first()->id,
        ]);
        
        // Attach task to user
        $this->user->tasks()->attach($task, [
            'is_completed' => false,
            'progress' => 0
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Dashboard')
            ->has('tasks', fn ($tasks) => 
                $tasks->has('dailies', 1)
                    ->has('todos', 0)
                    ->has('habits', 0)
            )
        );
    }

    public function test_user_can_filter_tasks_by_tag()
    {
        // Create a task with proper relationships
        $task = Task::factory()->create([
            'difficulty_level' => $this->difficulties->first()->difficulty_level,
            'reset_frequency' => $this->resetConfigs->first()->id,
        ]);
        
        // Attach task to user
        $this->user->tasks()->attach($task, [
            'is_completed' => false,
            'progress' => 0
        ]);
        
        // Create and attach tag
        $tag = Tag::factory()->create(['name' => 'test-tag']);
        $task->tags()->attach($tag);

        // Use tag ID for filtering
        $response = $this->actingAs($this->user)->get('/tags/filter/' . $tag->id);

        $response->assertStatus(200);
        $response->assertJson([
            'tasks' => [
                [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                ]
            ]
        ]);
    }
} 