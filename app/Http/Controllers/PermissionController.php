<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        
        // Group by module for better organization
        $grouped = $permissions->groupBy('module');
        
        return response()->json([
            'permissions' => PermissionResource::collection($permissions),
            'grouped' => $grouped,
            'modules' => $grouped->keys(),
        ]);
    }
}
