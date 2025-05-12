<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class UserActivityLogger
{
    public function log(User $user, string $activityType, Request $request, array $additionalData = []): void
    {
        UserActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'additional_data' => $additionalData,
        ]);
    }
} 