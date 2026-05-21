<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * แสดงหน้าแก้ไขโปรไฟล์
     */
    public function editProfile()
    {
        $user = Auth::user();
        // ตรวจสอบว่าไฟล์อยู่ที่ resources/views/profile/edit.blade.php
        return view('profile.edit', compact('user'));
    }

    /**
     * อัปเดตข้อมูลโปรไฟล์และรหัสผ่าน
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // ตรวจสอบความถูกต้องของข้อมูล
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            // กำหนดให้ password เป็น nullable (ไม่ต้องกรอกก็ได้) 
            // แต่ถ้ากรอก ต้องยาวอย่างน้อย 8 ตัว และต้องตรงกับช่อง confirmation
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        // อัปเดตชื่อและอีเมล
        $user->name = $request->name;
        $user->email = $request->email;

        // ตรวจสอบว่ามีการกรอกรหัสผ่านใหม่มาหรือไม่
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'โปรไฟล์ถูกอัปเดตเรียบร้อยแล้ว');
    }


    /**
     * แสดงหน้าตั้งค่าระบบ
     */
    public function editSettings()
    {
        $user = Auth::user();
        
        $settings = [
            'locale' => old('locale', 'th'),
            'items_per_page' => old('items_per_page', 20),
            'compact_mode' => old('compact_mode', false),
        ];

        return view('profile.settings', compact('user', 'settings'));
    }

    /**
     * อัปเดตการตั้งค่าระบบ
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string'],
            'items_per_page' => ['required', 'integer'],
            'compact_mode' => ['nullable'],
        ]);

        // compact_mode from form may come as '1' or true
        $compactMode = filter_var($request->input('compact_mode'), FILTER_VALIDATE_BOOLEAN);

        // persist to session using the keys expected by tests
        session([
            'settings.locale' => $validated['locale'],
            'settings.items_per_page' => $validated['items_per_page'],
            'settings.compact_mode' => $compactMode,
        ]);

        return redirect()->route('settings.edit')->with('success', 'การตั้งค่าระบบถูกบันทึกแล้ว');
    }

}