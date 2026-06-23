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
        // Ensure session middleware is always included for web routes
        $middleware->web(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);
        
        $middleware->alias([
            'admin_only' => \App\Http\Middleware\AdminOnly::class,
            'manager_or_admin' => \App\Http\Middleware\ManagerOrAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exception\HttpResponseException $e, $request) {
            // Let Laravel handle ValidationException automatically
            return null;
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (\Illuminate\Contracts\Auth\Access\AuthorizationException $e, $request) {
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        $exceptions->render(function (\Throwable $e, $request) {
            // 500 handler (log)
            if (app()->environment('production')) {
                report($e);
            } else {
                report($e);
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();

