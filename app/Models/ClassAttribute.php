<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassAttribute extends Model
{
    protected $fillable = [
        'class_name',
        'energy_multiplier',
        'health_multiplier',
        'exp_multiplier',
        'special_ability',
    ];

    protected $casts = [
        'energy_multiplier' => 'float',
        'health_multiplier' => 'float',
        'exp_multiplier' => 'float',
    ];

}
