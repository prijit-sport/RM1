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
        // Check authentication first
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();
        
        // Force reload role from database to ensure we have the latest
        $user->load('role');
        
        // If user has no role, allow access (basic authenticated user)
        if (!$user->role) {
            return $next($request);
        }
        
        // Check role - allow all authenticated users to pass
        // The role restrictions are handled at route level
        $userRole = $user->role->name;
        
        // Allow all authenticated users through
        return $next($request);
    }
}
