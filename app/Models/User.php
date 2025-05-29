<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    protected $fillable = [
        "name",
        "email",
        "password",
        "is_admin",
        "profile_image",
    ];
    protected $hidden = [
        "password",
        "remember_token",
    ];
    protected $casts = [
        "email_verified_at" => "datetime",
        "password" => "hashed",
    ];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, "user_tasks")
            ->withPivot("is_completed", "completed_at", "progress")
            ->withTimestamps();
    }

    public function userStatistics(): HasOne
    {
        return $this->hasOne(UserStatistics::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }
}
