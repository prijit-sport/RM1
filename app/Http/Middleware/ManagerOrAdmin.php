<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ManagerOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication first
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();

        // If user has no role, deny access
        $role = $user?->role;
        if (! $role) {
            Log::warning('Authorization denied', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => 'none',
                'reason' => 'missing_role',
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // Check if user has Admin or Manager role
        if (!in_array($role->name, ['Admin', 'Manager'], true)) {

            Log::warning('Authorization denied', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'user_id' => $user->id,
                'role' => $role->name,
                'reason' => 'not_manager_or_admin',
            ]);

            abort(403);
        }

        return $next($request);
    }
}
