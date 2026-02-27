<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:50',
            'description' => 'nullable|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('roles.index')->with('success', __('ui.role.created'));
    }

    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:50',
            'description' => 'nullable|max:255',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('roles.show', $role)->with('success', __('ui.role.updated'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', __('ui.role.deleted'));
    }

    public function export(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('id', 'desc')->get();
        $filename = 'roles_export_' . date('Y-m-d') . '.xlsx';

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


