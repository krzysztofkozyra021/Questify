<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskResetConfig extends Model
{

    use HasFactory;

    protected $fillable = [
        "reset_frequency",
        "reset_time",
    ];
    protected $casts = [
        "reset_frequency" => "integer",
        "reset_time" => "datetime",
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "reset_frequency", "id");
    }
}
