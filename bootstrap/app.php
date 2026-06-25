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
            'admin_only'       => \App\Http\Middleware\AdminOnly::class,
            'manager_or_admin' => \App\Http\Middleware\ManagerOrAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isApi = fn($request) => $request->expectsJson()
            || str_starts_with($request->path(), 'api/');
 
        // Let Laravel handle ValidationException automatically
        $exceptions->render(function (\Illuminate\Http\Exception\HttpResponseException $e, $request) {
            return null;
        });
 
        // ✅ เพิ่มใหม่: AuthenticationException (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
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
 
        // 4) ThrottleRequestsException (rate limit)
        $exceptions->render(function (\Illuminate\Contracts\Routing\ThrottleRequestsException $e, $request) use ($isApi) {
            if ($isApi($request)) {
                return response()->json(['message' => 'ลองใหม่อีกครั้งในอีกสักครู่'], 429);
            }
            return redirect()->back()->with('error', 'ลองใหม่อีกครั้งในอีกสักครู่');
        });
 
        // 5) General Throwable (500) — ต้องอยู่ท้ายสุดเสมอ
        $exceptions->render(function (\Throwable $e, $request) use ($isApi) {
            report($e);
            if ($isApi($request)) {
                return response()->json(['message' => 'เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้ง'], 500);
            }
            return response()->view('errors.500', [], 500);
        });
    })->create();
 