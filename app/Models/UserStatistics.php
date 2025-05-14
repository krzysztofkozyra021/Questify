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
        "current_health" => "float",
        "current_energy" => "float",
        "max_health" => "float",
        "max_energy" => "float",
        "current_experience" => "float",
        "next_level_experience" => "float",
        "energy_regen_rate" => "float",
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

            // You could also increase stats with level
            $this->max_health += 5; // Example: increase max health by 5 each level
            $this->max_energy += 3; // Example: increase max energy by 3 each level

            // Optionally restore health/energy on level up
            $this->current_health = $this->max_health;
            $this->current_energy = $this->max_energy;
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
        // Example: each level requires 30 XP  more than the previous level
        // Level 1: 100 XP
        // Level 2: 130 XP
        // Level 3: 160 XP
        // etc.
        return 100 + (($level - 1) * 30);
    }
    
}
