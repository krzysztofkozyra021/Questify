<?php

declare(strict_types=1);

namespace Database\Seeders;

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
        User::Factory(30)->create();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        $admin->userStatistics()->create([
            'user_id' => $admin->id,
            'class' => 1,
            'current_health' => 100,
            'current_energy' => 100,
            'max_health' => 100,
            'max_energy' => 100,
            'current_experience' => 0,
            'next_level_experience' => 100,
            'level' => 1,
            'energy_regen_rate' => 1,
        ]);

    }
}
