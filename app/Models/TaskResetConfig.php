<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskResetConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "frequency_type",
        "period",
        "period_to_days",
        "period_unit",
        "days_of_week",
        "day_of_month",
        "month",
        "reset_time",
    ];
    protected $casts = [
        "days_of_week" => "array",
        "reset_time" => "datetime",
    ];
    private mixed $period_unit;
    private mixed $period;

    /**
     * Get the tasks with this reset configuration.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "reset_frequency", "id");
    }

    /**
     * Get human-readable representation of the reset configuration.
     */
    public function getDisplayNameAttribute(): string
    {
        switch ($this->frequency_type) {
            case "daily":
                return "Daily";
            case "weekly":
                return "Weekly";
            case "monthly":
                return "Monthly";
            case "yearly":
                return "Yearly";
            case "custom":
                if ($this->period_unit === "day") {
                    return "Every {$this->period} " . ($this->period === 1 ? "day" : "days");
                }

                if ($this->period_unit === "week") {
                    return "Every {$this->period} " . ($this->period === 1 ? "week" : "weeks");
                }

                if ($this->period_unit === "month") {
                    return "Every {$this->period} " . ($this->period === 1 ? "month" : "months");
                }

                if ($this->period_unit === "year") {
                    return "Every {$this->period} " . ($this->period === 1 ? "year" : "years");
                }

                return $this->name;
            default:
                return $this->name;
        }
    }
}
