<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassAttribute extends Model
{
    protected $fillable = [
        "class_name",
        "energy_multiplier",
        "health_multiplier",
        "exp_multiplier",
        "special_ability",
    ];
    protected $casts = [
        "energy_multiplier" => "float",
        "health_multiplier" => "float",
        "exp_multiplier" => "float",
    ];

    /**
     * Get the user statistics that belong to this class.
     */
    public function userStatistics(): HasMany
    {
        return $this->hasMany(UserStatistics::class, "class", "id");
    }
}
