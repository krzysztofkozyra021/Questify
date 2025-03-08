<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatistics extends Model
{
    protected $fillable = [
        'user_id',
        'class',
        'health',
        'energy',
        'experience',
        'level',
        'energy_regen_rate',
        'last_login',
        'last_reset',
    ];

    protected $casts = [
        'health' => 'float',
        'energy' => 'float',
        'experience' => 'float',
        'energy_regen_rate' => 'float',
        'last_login' => 'datetime',
        'last_reset' => 'datetime',
    ];

    /**
     * Get the user that owns the statistics.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class attributes for the user.
     */
    public function classAttributes() : BelongsTo
    {
        return $this->belongsTo(ClassAttribute::class, 'class', 'id');
    }
}
