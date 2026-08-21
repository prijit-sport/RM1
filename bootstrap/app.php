<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        // Defensive HTTP response headers (CSP, X-Frame-Options, HSTS, ...)
        // applied to both the Blade app and the JSON API.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'admin_only' => \App\Http\Middleware\AdminOnly::class,
            'manager_or_admin' => \App\Http\Middleware\ManagerOrAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isApi = fn ($request) => $request->expectsJson()
            || str_starts_with($request->path(), 'api/');

        // ✅ ValidationException — ให้ Laravel จัดการเองอัตโนมัติ (redirect + flash errors)
        // ต้องอยู่ก่อน General Throwable เพื่อกัน Throwable handler มา catch ก่อน
        $exceptions->dontReport(\Illuminate\Validation\ValidationException::class);
        $exceptions->dontFlash(\Illuminate\Validation\ValidationException::class);

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        });

        // Let Laravel handle HttpResponseException automatically
        $exceptions->render(function (\Illuminate\Http\Exception\HttpResponseException $e, $request) {
            return null;
        });

        // ✅ AuthenticationException (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        });

        // ✅ HttpException — จาก abort(403), abort(404) ใน middleware
        $exceptions->render(function (HttpException $e, $request) use ($isApi) {
            $status = $e->getStatusCode();
            if ($isApi($request)) {
                return response()->json(['message' => $e->getMessage() ?: 'Error'], $status);
            }
            $view = "errors.{$status}";
            if (view()->exists($view)) {
                return response()->view($view, ['message' => $e->getMessage()], $status);
            }

            return response($e->getMessage() ?: 'Error', $status);
        });

        // 1) ModelNotFoundException
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => 'ไม่พบข้อมูลที่ต้องการ'], 404);
            }

            return redirect()->back()->with('error', 'ไม่พบข้อมูลที่ต้องการ');
        });

        // 2) AuthorizationException
        $exceptions->render(function (\Illuminate\Contracts\Auth\Access\AuthorizationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        // 3) ThrottleRequestsException (rate limit)
        $exceptions->render(function (\Illuminate\Contracts\Routing\ThrottleRequestsException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => 'ลองใหม่อีกครั้งในอีกสักครู่'], 429);
            }

            return redirect()->back()->with('error', 'ลองใหม่อีกครั้งในอีกสักครู่');
        });

        // 4) General Throwable (500) — ต้องอยู่ท้ายสุดเสมอ
        $exceptions->render(function (\Throwable $e, $request) use ($isApi) {
            report($e);
            if ($isApi($request)) {
                return response()->json(['message' => 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้ง'], 500);
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();