<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'difficulty_level',
        'reset_frequency',
        'due_date',
        'is_completed',
        'is_deadline_task',
        'experience_reward',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
        'is_deadline_task' => 'boolean',
        'experience_reward' => 'float',
    ];

    /**
     * Get the task difficulty that this task belongs to.
     */
    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(TaskDifficulty::class, 'difficulty_level', 'difficulty_level');
    }

    /**
     * Get the reset configuration for this task.
     */
    public function resetConfig(): BelongsTo
    {
        return $this->belongsTo(TaskResetConfig::class, 'reset_frequency', 'id');
    }
}
