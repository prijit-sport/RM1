<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        Log::info('[Auth] showLogin - Session ID: ' . $request->session()->getId());
        Log::info('[Auth] showLogin - User authenticated: ' . (Auth::check() ? 'Yes' : 'No'));
        
        $request->session()->regenerateToken();

        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('[Auth] login attempt - Email: ' . $request->email);
        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            Log::warning('[Auth] login failed - User not found: ' . $request->email);
            return back()->withErrors(['email' => __('ui.auth.login_failed')])->onlyInput('email');
        }
        
        // Check if user is active (default to true if column doesn't exist)
        $isActive = $user->is_active ?? true;
        if ($user && Hash::check($credentials['password'], $user->password) && !$isActive) {
            Log::warning('[Auth] login failed - User inactive: ' . $request->email);
            return back()->withErrors(['email' => __('ui.auth.inactive')])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ])) {
            $request->session()->regenerate();
            AuditLogger::log('auth.login', Auth::user());
            
            Log::info('[Auth] login success - User: ' . Auth::user()->name . ', Session ID: ' . $request->session()->getId());

            return redirect('/dashboard')->with('success', __('ui.auth.login_success'));
        }

        Log::warning('[Auth] login failed - Invalid credentials: ' . $request->email);
        return back()
            ->withErrors(['email' => __('ui.auth.login_failed')])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Unknown';
        Log::info('[Auth] logout - User: ' . $userName);
        
        AuditLogger::log('auth.logout', Auth::user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('[Auth] logout complete - Session invalidated');

        return redirect('/')->with('success', __('ui.auth.logout_success'));
    }
}
