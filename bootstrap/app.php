<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'premium' => \App\Http\Middleware\CheckPremiumAccess::class,
        ]);

        // Configure authentication redirects for API
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->expectsJson()) {
                return null; // Return null to prevent redirect and return 401 instead
            }
            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();