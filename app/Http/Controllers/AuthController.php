<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password) && ! $user->is_active) {
            return back()->withErrors(['email' => __('ui.auth.inactive')])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ])) {
            $request->session()->regenerate();
            AuditLogger::log('auth.login', Auth::user());

            return redirect('/dashboard')->with('success', __('ui.auth.login_success'));
        }

        return back()
            ->withErrors(['email' => __('ui.auth.login_failed')])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        AuditLogger::log('auth.logout', Auth::user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('ui.auth.logout_success'));
    }
}
