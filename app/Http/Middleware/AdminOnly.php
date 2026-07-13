<?php

namespace App\Http\Middleware;

use App\Support\DirectLog;
use App\Models\Role;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();

        if (app()->environment('testing')) {
            @ini_set('log_errors', '1');
            @ini_set('error_log', storage_path('logs/laravel.log'));
        }

        if (! $user->role) {
            DirectLog::write(json_encode([
                'msg'     => 'Authorization denied',
                'route'   => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role'    => 'User',
                'reason'  => 'missing_role',
            ]));

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        if ($user->role?->name !== 'Admin') {
            DirectLog::write(json_encode([
                'msg'     => 'Authorization denied',
                'route'   => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role'    => $user->role?->name ?? 'unknown',
                'reason'  => 'not_admin',
            ]));

            abort(403);
        }

        return $next($request);
    }
}
