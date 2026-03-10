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
        Log::info('[Auth] showLogin - User authenticated: ' . (Auth::check() ? 'Yes - ' . Auth::user()->name : 'No'));
        Log::info('[Auth] showLogin - Session has user_id: ' . ($request->session()->has('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d') ? 'Yes' : 'No'));
        
        $request->session()->regenerateToken();

        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('[Auth] login attempt - Email: ' . $request->email);
        Log::info('[Auth] login - Session ID before: ' . $request->session()->getId());
        
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
            // Force session to persist before redirect
            $request->session()->put('auth_user_id', Auth::user()->id);
            $request->session()->save();
            
            $request->session()->regenerate();
            AuditLogger::log('auth.login', Auth::user());
            
            Log::info('[Auth] login success - User: ' . Auth::user()->name . ', Session ID: ' . $request->session()->getId());
            Log::info('[Auth] Session login_web key: ' . ($request->session()->has('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d') ? 'Yes' : 'No'));

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
