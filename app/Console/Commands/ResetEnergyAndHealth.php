<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserStatistics;
use Illuminate\Console\Command;

class ResetEnergyAndHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "users:reset-energy-and-health";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Reset energy and health for all users";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $users = User::all();
        $userStatistics = UserStatistics::all();

        foreach ($users as $user) {
            $user->userStatistics->current_energy = $user->userStatistics->max_energy;
            $user->userStatistics->current_health = $user->userStatistics->max_health;
            $user->userStatistics->save();
        }
    }
}
