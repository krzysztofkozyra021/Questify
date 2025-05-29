<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, cookie, or default to 'en'
        $locale = session('locale') ?? $request->cookie('locale') ?? 'en';

        // Set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}
