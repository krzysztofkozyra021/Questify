<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskDifficulty extends Model
{
    use HasFactory;

    protected $fillable = [
        "difficulty_level",
        "difficulty_name",
        "energy_cost",
        "health_penalty",
        "base_exp_multiplier",
    ];
    protected $primaryKey = "difficulty_level";

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "difficulty_level", "difficulty_level");
    }
}
