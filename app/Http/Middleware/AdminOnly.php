<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // If user has no role, deny access
        if (!$user->role) {
            Log::warning('Authorization denied', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => 'none',
                'reason' => 'missing_role',
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // Check if user has Admin role (null-safe)
        if ($user->role?->name !== 'Admin') {
            Log::warning('Authorization denied', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => $user->role?->name ?? 'unknown',
                'reason' => 'not_admin',
            ]);

            abort(403);
        }

        return $next($request);
    }
}
