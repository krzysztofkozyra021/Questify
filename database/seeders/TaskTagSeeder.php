<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TaskTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $tags = Tag::all();

        // For each task, assign tags
        foreach ($tasks as $task) {
            $randomTagCount = rand(1, min(3, $tags->count()));
            $randomTags = $tags->random($randomTagCount);
            $task->tags()->attach($randomTags->pluck('id')->toArray());
        }
    }
}
