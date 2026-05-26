<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Support\DirectLog;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();

        // Ensure logs are flushed/visible during tests

        if (app()->environment('testing')) {
            @ini_set('log_errors', '1');
            @ini_set('error_log', storage_path('logs/laravel.log'));
        }


        // If user has no role, deny access
        if (!$user->role) {
            DirectLog::write(json_encode([
                'msg' => 'Authorization denied',
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => 'User',
                'reason' => 'missing_role',
            ]));

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // Check if user has Admin role (null-safe)
        if ($user->role?->name !== 'Admin') {
            DirectLog::write(json_encode([
                'msg' => 'Authorization denied',
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => $user->role?->name ?? 'unknown',
                'reason' => 'not_admin',
            ]));

            abort(403);
        }


        return $next($request);
    }
}
