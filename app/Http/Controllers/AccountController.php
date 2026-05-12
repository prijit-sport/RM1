<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // เพิ่มตัวนี้

class AccountController extends Controller
{
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user(); // ใช้ Auth Facade แทน helper

        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'โปรไฟล์ถูกอัปเดตเรียบร้อยแล้ว');
    }

    public function updateSettings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->back()->with('success', 'การตั้งค่าถูกบันทึกแล้ว');
    }
}