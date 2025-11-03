<?php

// namespace App\Http\;
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... existing code

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... existing middleware
        'premium' => \App\Http\Middleware\CheckPremiumAccess::class,
        'admin' => \App\Http\Middleware\CheckAdminRole::class, // Add this line
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array<int, class-string|string>
     */
    protected $middlewarePriority = [
        // ... existing middleware priority
    ];

}
