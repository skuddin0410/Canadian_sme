<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
// use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'jwtauth' => \App\Http\Middleware\JwtAuthenticate::class,
            'webauth' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
            'company.exists' => \App\Http\Middleware\EnsureCompanyExists::class,
            'admin' => \App\Http\Middleware\AdminOnly::class,
        ]);
    })->withMiddleware(function (Middleware $middleware) {
        $middleware
        ->web(append: [
            \App\Http\Middleware\TrackPageViews::class,
        ])
        ->api(append: [
            \App\Http\Middleware\TrackPageViews::class,
        ]);    
    })->withExceptions(function (Exceptions $exceptions) {
        // Customize when to render JSON response
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Render a custom JSON response for AuthenticationException
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ], 401);
        });
    })
    ->create();
