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
