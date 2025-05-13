<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ClassAttribute;
use App\Models\User;
use App\Models\UserStatistics;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all available classes
        $classes = ClassAttribute::all();

        // Create regular users with statistics
        User::factory(30)->create()->each(function ($user) use ($classes) {
            $user->userStatistics()->create([
                'user_id' => $user->id,
                'class' => $classes->random()->id,
                'current_health' => 100,
                'current_energy' => 100,
                'max_health' => 100,
                'max_energy' => 100,
                'current_experience' => 0,
                'next_level_experience' => 100,
                'level' => 1,
                'energy_regen_rate' => 1,
                'last_login' => now(),
                'last_reset' => now(),
            ]);
        });

        // Create admin user with statistics
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        $admin->userStatistics()->create([
            'user_id' => $admin->id,
            'class' => $classes->first()->id,
            'current_health' => 100,
            'current_energy' => 100,
            'max_health' => 100,
            'max_energy' => 100,
            'current_experience' => 0,
            'next_level_experience' => 100,
            'level' => 1,
            'energy_regen_rate' => 1,
            'last_login' => now(),
            'last_reset' => now(),
        ]);
    }
}
