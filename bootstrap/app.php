<?php

use App\Http\Middleware\LoggerMiddleware;
use App\Http\Middleware\TokenExtraction;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    /**
     * Define the routing configuration for the application.
     */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    /**
     * Configure the global and aliased middleware for the application.
     */
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware appended to every request
        $middleware->append(LoggerMiddleware::class);
        $middleware->append(TokenExtraction::class);

        // Middleware aliases for role-based access control (RBAC)
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })

    /**
     * Configure exception handling.
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
