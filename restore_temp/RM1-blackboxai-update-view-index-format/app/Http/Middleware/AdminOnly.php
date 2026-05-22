<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

// Check if user has Admin role (null-safe)
        if ($user->role?->name !== 'Admin') {
            abort(403);
        }

        return $next($request);
    }
}
