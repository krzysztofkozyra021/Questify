<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTaskCollection extends Model
{

    use HasFactory;

    protected $fillable = [
        "user_id",
        "task_id",
        "is_completed",
        "completed_at",
        "progress",
    ];
    protected $casts = [
        "is_completed" => "boolean",
        "completed_at" => "datetime",
        "progress" => "float",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tasks')
            ->withPivot('is_completed', 'completed_at', 'progress')
            ->withTimestamps();
    }
}
