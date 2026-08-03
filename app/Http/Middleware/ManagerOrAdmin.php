<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ManagerOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();
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

        if (! in_array($role->name, [Role::ADMIN, Role::STAFF], true)) {
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
