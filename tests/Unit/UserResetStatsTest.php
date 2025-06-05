<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserStatistics;
use App\Console\Commands\ResetEnergyAndHealth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserResetStatsTest extends TestCase
{
    use RefreshDatabase;

    public function testThatUserStatsAreResetCorrectly(): void
    {
        $user = User::factory()->create();
        $userStatistics = UserStatistics::factory()->create([
            'user_id' => $user->id,
        ]);


        $this->assertEquals($userStatistics->max_energy, $userStatistics->current_energy);
        $this->assertEquals($userStatistics->max_health, $userStatistics->current_health);

        $user->userStatistics->current_energy = 0;
        $user->userStatistics->current_health = 0;

        $user->userStatistics->save();

        $this->assertEquals(0, $user->userStatistics->current_energy);
        $this->assertEquals(0, $user->userStatistics->current_health);

        $resetEnergyAndHealth = new ResetEnergyAndHealth();
        $resetEnergyAndHealth->handle();

        $user->refresh();

        $this->assertEquals($user->userStatistics->max_energy, $user->userStatistics->current_energy);
        $this->assertEquals($user->userStatistics->max_health, $user->userStatistics->current_health);
    }
}
