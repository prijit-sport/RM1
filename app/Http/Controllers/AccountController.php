<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function editProfile()
    {
        $user = Auth::user();

        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', __('ui.account.profile_updated'));
    }

    public function editSettings(Request $request)
    {
        $settings = [
            'locale' => $request->session()->get('settings.locale', 'th'),
            'items_per_page' => (int) $request->session()->get('settings.items_per_page', 10),
            'compact_mode' => (bool) $request->session()->get('settings.compact_mode', false),
        ];

        return view('account.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:th,en',
            'items_per_page' => 'required|integer|in:10,20,50,100',
            'compact_mode' => 'nullable|boolean',
        ]);

        $request->session()->put('settings.locale', $validated['locale']);
        $request->session()->put('settings.items_per_page', (int) $validated['items_per_page']);
        $request->session()->put('settings.compact_mode', $request->boolean('compact_mode'));

        return redirect()->route('settings.edit')->with('success', __('ui.account.settings_updated'));
    }
}
