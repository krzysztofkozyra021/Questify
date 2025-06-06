<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureClassIsSelected;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ShareInertiaData;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
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
        $middleware->api(prepend: EnsureFrontendRequestsAreStateful::class);

        $middleware->validateCsrfTokens(
            except: [
                "api/*",
            ],
        );

        $middleware->trustHosts(at: ["localhost", "127.0.0.1", "0.0.0.0"]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->create();
