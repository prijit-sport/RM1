<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Support\CacheKeys;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        $permissions = Cache::remember(CacheKeys::allPermissions(), now()->addHours(6), fn () => Permission::orderBy('name')->get());

        return view('roles.create', compact('permissions'));

    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => 'required|unique:roles|max:50',
            'description' => 'nullable|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        Cache::forget(CacheKeys::allPermissions());

        return redirect()->route('roles.index')->with('success', __('ui.role.created'));

    }

    public function show(Role $role): View
    {
        $this->authorize('view', $role);

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $permissions = Cache::remember(CacheKeys::allPermissions(), now()->addHours(6), fn () => Permission::orderBy('name')->get());

        return view('roles.edit', compact('role', 'permissions'));

    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $validated = $request->validate([

            'name' => 'required|unique:roles,name,'.$role->id.'|max:50',
            'description' => 'nullable|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        Cache::forget(CacheKeys::allPermissions());

        return redirect()->route('roles.show', $role)->with('success', __('ui.role.updated'));

    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        // ✅ FIX: ปิดการลบ role ทั้งหมดถาวร — เคยมีเหตุการณ์ที่ลบ role "Admin"
        // ออกไปทั้งตาราง (ผ่านหน้า role show ที่ตอนนั้นยังไม่มีการป้องกัน) ทำให้
        // ทุกคนในระบบเสียสิทธิ์ทันที เนื่องจากระบบนี้ใช้แค่ 2 role คงที่
        // (Admin, Staff) ไม่มีความจำเป็นต้องลบ role ได้เลย จึงปิดไว้ที่นี่เป็น
        // ชั้นป้องกันสุดท้าย (นอกเหนือจากที่เอาปุ่มลบออกจากหน้าเว็บไปแล้ว)
        return redirect()->route('roles.index')
            ->with('error', 'ไม่สามารถลบบทบาทได้ — ระบบนี้ปิดการลบบทบาทไว้ถาวรเพื่อป้องกันการสูญเสียสิทธิ์การใช้งานโดยไม่ตั้งใจ');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $roles = Role::with('permissions')->orderBy('id', 'desc')->get();
        $filename = 'roles_export_'.date('Y-m-d').'.xlsx';

        $rows = [];
        $rows[] = ['Name', 'Description', 'Permissions'];

        foreach ($roles as $role) {
            $rows[] = [
                $role->name,
                $role->description ?? '-',
                $role->permissions->pluck('name')->implode(', '),
            ];
        }

        return xlsx_download($filename, $rows);
    }
}
