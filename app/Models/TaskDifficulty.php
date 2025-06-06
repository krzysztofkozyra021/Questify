<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskDifficulty extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = "difficulty_level";
    protected $fillable = [
        "difficulty_level",
        "name",
        "color",
        "icon",
        "energy_cost",
        "health_penalty",
        "exp_multiplier",
    ];

    /**
     * Get the tasks for this difficulty level.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "difficulty_level", "difficulty_level");
    }
}
