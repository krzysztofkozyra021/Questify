<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureClassIsSelected;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ShareInertiaData;
use App\Http\Middleware\SetLocale;
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
        $middleware->web(SetLocale::class);
        $middleware->web(ShareInertiaData::class);
        $middleware->web(HandleInertiaRequests::class);
        $middleware->web(EnsureClassIsSelected::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->create();
