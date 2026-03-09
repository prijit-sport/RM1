<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        Log::info('[ManagerOrAdmin] Checking - URI: ' . $request->uri() . ', Method: ' . $request->method());
        
        if (!auth()->check()) {
            Log::warning('[ManagerOrAdmin] User not authenticated - Redirecting to login');
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();
        Log::info('[ManagerOrAdmin] User: ' . $user->name . ', Role: ' . ($user->role ? $user->role->name : 'null'));
        
        // Check if user has role relationship loaded
        if (!$user->role) {
            Log::error('[ManagerOrAdmin] User role not found - User ID: ' . $user->id);
            return redirect()->route('dashboard')->with('error', 'ไม่พบข้อมูลบทบาทของผู้ใช้ กรุณาติดต่อผู้ดูแลระบบ');
        }
        
        if (!$user->hasRole('Admin') && !$user->hasRole('Manager')) {
            Log::warning('[ManagerOrAdmin] User does not have required role - User: ' . $user->name);
            abort(403, 'Unauthorized - Manager or Admin access required');
        }

        Log::info('[ManagerOrAdmin] Access granted - User: ' . $user->name);
        return $next($request);
    }
}
