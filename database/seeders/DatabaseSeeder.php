<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TagSeeder::class,
            TaskSeeder::class,
            TaskTagSeeder::class,
            UserSeeder::class,
            UserTasksSeeder::class,
            ClassAttributesSeeder::class,
        ]);
    }
}
