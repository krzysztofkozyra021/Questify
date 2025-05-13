<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\UserStatistics;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureClassIsSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Exempt routes that should be accessible without a class selection
            if ($request->routeIs("select-class") ||
                $request->routeIs("select-class.store") ||
                $request->routeIs("logout") ||
                $request->routeIs("profile.destroy")) {
                return $next($request);
            }

            $userStats = UserStatistics::where("user_id", Auth::id())->first();

            if (!$userStats || $userStats->class === null) {
                return redirect()->route("select-class");
            }
        }

        return $next($request);
    }
}
