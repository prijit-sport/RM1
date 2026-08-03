<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = Cache::remember('permissions.all', now()->addHours(6), fn () => Permission::orderBy('name')->get());

        return view('roles.create', compact('permissions'));

    }

    public function store(Request $request)
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

        Cache::forget('permissions.all');

        return redirect()->route('roles.index')->with('success', __('ui.role.created'));

    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Cache::remember('permissions.all', now()->addHours(6), fn () => Permission::orderBy('name')->get());

        return view('roles.edit', compact('role', 'permissions'));

    }

    public function update(Request $request, Role $role)
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

        Cache::forget('permissions.all');

        return redirect()->route('roles.show', $role)->with('success', __('ui.role.updated'));

    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        Cache::forget('permissions.all');

        return redirect()->route('roles.index')->with('success', __('ui.role.deleted'));

    }

    public function export(Request $request)
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
