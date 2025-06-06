<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "class",
        "current_health",
        "current_energy",
        "max_health",
        "max_energy",
        "current_experience",
        "next_level_experience",
        "level",
        "energy_regen_rate",
        "last_login",
        "last_reset",
    ];
    protected $casts = [
        "current_health" => "integer",
        "current_energy" => "integer",
        "max_health" => "integer",
        "max_energy" => "integer",
        "current_experience" => "integer",
        "next_level_experience" => "integer",
        "energy_regen_rate" => "integer",
        "last_login" => "datetime",
        "last_reset" => "datetime",
    ];

    /**
     * Check if experience exceeds the threshold and update level if needed
     */
    public function checkAndUpdateLevel(): void
    {
        // Keep leveling up as long as experience exceeds the threshold
        while ($this->current_experience >= $this->next_level_experience) {
            // Increase level
            ++$this->level;

            // Calculate new experience threshold for next level
            $this->next_level_experience = $this->calculateNextLevelExperience($this->level);

            // Calculate health and energy increases
            $healthIncrease = $this->calculateHealthIncrease($this->level);
            $energyIncrease = $this->calculateEnergyIncrease($this->level);

            $this->max_health += $healthIncrease;
            $this->max_energy += $energyIncrease;

            // Optionally restore health/energy on level up
            $this->current_health = $this->max_health;
            $this->current_energy = $this->max_energy;

            $this->current_experience = 0;
        }
    }

    /**
     * Get the user that owns the statistics.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class attributes for the user.
     */
    public function classAttributes(): BelongsTo
    {
        return $this->belongsTo(ClassAttribute::class, "class", "id");
    }

    /**
     * Calculate health increase based on current level and tier
     */
    protected function calculateHealthIncrease(int $level)
    {
        if ($level <= 10) {
            // Tier 1: Modest health increases
            return 8 + (1 * $level);
        } elseif ($level <= 25) {
            // Tier 2: Better health increases
            return 10 + (2 * $level);
        }

        // Tier 3: Significant health increases
        return 15 + (3 * $level);
    }

    /**
     * Calculate energy increase based on current level and tier
     */
    protected function calculateEnergyIncrease(int $level)
    {
        if ($level <= 10) {
            // Tier 1: Modest energy increases
            return 5 + (1 * $level);
        } elseif ($level <= 25) {
            // Tier 2: Better energy increases
            return 8 + (1.5 * $level);
        }

        // Tier 3: Significant energy increases
        return 10 + (2 * $level);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(function ($userStats): void {
            // Check if experience has been updated
            if ($userStats->isDirty("current_experience")) {
                $userStats->checkAndUpdateLevel();
            }
        });
    }

    /**
     * Calculate experience required for the next level
     * This is a simple example - customize with your game's progression formula
     */
    protected function calculateNextLevelExperience($level)
    {
        $baseXP = 100;

        if ($level <= 10) {
            // Easy early progression - Formula: 100 * level + 25 * level
            return $baseXP * $level + $baseXP * 0.25 * $level;
        } elseif ($level <= 25) {
            // Medium difficulty mid-game - Formula: 100 + 150 * (level - 10)
            return $baseXP * 10 + $baseXP * 1.5 * ($level - 10);
        }

        // More challenging end-game - Formula: 100 + 150 * 15 + 200 * (level - 25)
        return $baseXP * 10 + $baseXP * 1.5 * 15 + $baseXP * 2 * ($level - 25);
    }
}
