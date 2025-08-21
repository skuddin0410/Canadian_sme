<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display the Role & Permission Matrix.
     */
    public function matrix()
    {
        $roles = Role::whereIn('name', [
            'Event Admin',
            'Admin',
            'Support Staff Or Helpdesk',
            'Registration Desk'
        ])->with('permissions')->get();

        // Get all permissions (or filter if needed)
        $permissions = Permission::all();

        return view('roles.matrix', compact('roles', 'permissions'));
    }

    public function assignPermission(Request $request)
    {
        $role = Role::findById($request->role_id);
        $permission = Permission::findById($request->permission_id);

        if ($request->checked == 'true') {
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        return response()->json(['success' => true]);
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role with selected permissions.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Create role
        $role = Role::create(['name' => $request->name]);

        // Assign permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.matrix')
                         ->with('success', 'Role created successfully.');
    }
}
