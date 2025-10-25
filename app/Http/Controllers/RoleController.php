<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('hierarchy_level')->get();
        return RoleResource::collection($roles);
    }

    public function show(Role $role)
    {
        return new RoleResource($role->load('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
            'hierarchy_level' => 'nullable|integer'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'] ?? $validated['name'],
            'description' => $validated['description'] ?? null,
            'hierarchy_level' => $validated['hierarchy_level'] ?? 0,
        ]);

        return response()->json($role, 201);
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*.permission_id' => 'required|exists:permissions,id',
            'permissions.*.access_level' => 'required|in:view,edit,approve,full',
        ]);

        $syncData = [];
        foreach ($validated['permissions'] as $permission) {
            $syncData[$permission['permission_id']] = [
                'access_level' => $permission['access_level']
            ];
        }

        $role->permissions()->sync($syncData);

        return response()->json([
            'message' => 'Permissions synced successfully',
            'role' => new RoleResource($role->fresh('permissions'))
        ]);
    }
}
