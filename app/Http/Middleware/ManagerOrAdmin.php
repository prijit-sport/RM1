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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication first
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();
        


        
        // If user has no role, deny access
        if (!$user->role) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }
        
        // Check if user has Manager or Admin role
        $userRole = $user->role->name;
        if (!in_array($userRole, ['Admin', 'Manager'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }
        
        return $next($request);
    }
}
