<?php

declare(strict_types=1);

use App\Http\Middleware\Localization;
use App\Http\Middleware\ShareInertiaData;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies("*");

        // Add the Localization middleware to the web group
        $middleware->web(Localization::class);
        $middleware->web(ShareInertiaData::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->create();
