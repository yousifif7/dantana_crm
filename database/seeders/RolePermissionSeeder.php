<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [
            // Chairman - View and Approve access to all modules
            'chairman' => [
                ['permission' => 'transactions.view', 'level' => 'view'],
                ['permission' => 'transactions.approve', 'level' => 'approve'],
                ['permission' => 'hr.view', 'level' => 'view'],
                ['permission' => 'procurement.view', 'level' => 'view'],
                ['permission' => 'procurement.approve', 'level' => 'approve'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'production.view', 'level' => 'view'],
                ['permission' => 'production.approve', 'level' => 'approve'],
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'reports.view', 'level' => 'view'],
                ['permission' => 'reports.export', 'level' => 'view'],
            ],

            // MD - Full access to everything
            'md' => [
                ['permission' => 'transactions.view', 'level' => 'full'],
                ['permission' => 'transactions.create', 'level' => 'full'],
                ['permission' => 'transactions.edit', 'level' => 'full'],
                ['permission' => 'transactions.delete', 'level' => 'full'],
                ['permission' => 'transactions.approve', 'level' => 'full'],
                ['permission' => 'hr.view', 'level' => 'full'],
                ['permission' => 'hr.create', 'level' => 'full'],
                ['permission' => 'hr.edit', 'level' => 'full'],
                ['permission' => 'hr.delete', 'level' => 'full'],
                ['permission' => 'procurement.view', 'level' => 'full'],
                ['permission' => 'procurement.create', 'level' => 'full'],
                ['permission' => 'procurement.edit', 'level' => 'full'],
                ['permission' => 'procurement.approve', 'level' => 'full'],
                ['permission' => 'inventory.view', 'level' => 'full'],
                ['permission' => 'inventory.create', 'level' => 'full'],
                ['permission' => 'inventory.edit', 'level' => 'full'],
                ['permission' => 'inventory.adjust', 'level' => 'full'],
                ['permission' => 'inventory.delete', 'level' => 'full'],
                ['permission' => 'production.view', 'level' => 'full'],
                ['permission' => 'production.create', 'level' => 'full'],
                ['permission' => 'production.edit', 'level' => 'full'],
                ['permission' => 'production.approve', 'level' => 'full'],
                ['permission' => 'process.view', 'level' => 'full'],
                ['permission' => 'process.create', 'level' => 'full'],
                ['permission' => 'process.edit', 'level' => 'full'],
                ['permission' => 'process.delete', 'level' => 'full'],
                ['permission' => 'reports.view', 'level' => 'full'],
                ['permission' => 'reports.export', 'level' => 'full'],
                ['permission' => 'attendance.view', 'level' => 'full'],
                ['permission' => 'attendance.manage', 'level' => 'full'],
            ],

            // CFO - Finance only
            'cfo' => [
                ['permission' => 'transactions.view', 'level' => 'full'],
                ['permission' => 'transactions.create', 'level' => 'edit'],
                ['permission' => 'transactions.edit', 'level' => 'edit'],
                ['permission' => 'transactions.approve', 'level' => 'approve'],
                ['permission' => 'reports.view', 'level' => 'view'],
                ['permission' => 'reports.export', 'level' => 'view'],
            ],

            // General Manager - Department access
            'general_manager' => [
                ['permission' => 'transactions.view', 'level' => 'view'],
                ['permission' => 'transactions.approve', 'level' => 'approve'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'inventory.edit', 'level' => 'edit'],
                ['permission' => 'production.view', 'level' => 'view'],
                ['permission' => 'production.approve', 'level' => 'approve'],
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'process.create', 'level' => 'edit'],
                ['permission' => 'process.edit', 'level' => 'edit'],
                ['permission' => 'reports.view', 'level' => 'view'],
                ['permission' => 'attendance.view', 'level' => 'view'],
            ],

            // Department Head
            'department_head' => [
                ['permission' => 'transactions.view', 'level' => 'view'],
                ['permission' => 'transactions.create', 'level' => 'edit'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'inventory.edit', 'level' => 'edit'],
                ['permission' => 'production.view', 'level' => 'view'],
                ['permission' => 'production.create', 'level' => 'edit'],
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'process.create', 'level' => 'edit'],
                ['permission' => 'process.edit', 'level' => 'edit'],
                ['permission' => 'reports.view', 'level' => 'view'],
                ['permission' => 'attendance.view', 'level' => 'view'],
                ['permission' => 'attendance.manage', 'level' => 'edit'],
            ],

            // HR Officer
            'hr_officer' => [
                ['permission' => 'hr.view', 'level' => 'full'],
                ['permission' => 'hr.create', 'level' => 'full'],
                ['permission' => 'hr.edit', 'level' => 'full'],
                ['permission' => 'hr.delete', 'level' => 'full'],
                ['permission' => 'attendance.view', 'level' => 'full'],
                ['permission' => 'attendance.manage', 'level' => 'full'],
                ['permission' => 'reports.view', 'level' => 'view'],
            ],

            // Procurement Officer
            'procurement_officer' => [
                ['permission' => 'procurement.view', 'level' => 'full'],
                ['permission' => 'procurement.create', 'level' => 'edit'],
                ['permission' => 'procurement.edit', 'level' => 'edit'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'inventory.edit', 'level' => 'edit'],
            ],

            // Operations Officer
            'operations_officer' => [
                ['permission' => 'production.view', 'level' => 'full'],
                ['permission' => 'production.create', 'level' => 'edit'],
                ['permission' => 'production.edit', 'level' => 'edit'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'process.view', 'level' => 'view'],
            ],

            // Executive
            'executive' => [
                ['permission' => 'transactions.view', 'level' => 'view'],
                ['permission' => 'transactions.create', 'level' => 'edit'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'production.view', 'level' => 'view'],
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'process.create', 'level' => 'edit'],
                ['permission' => 'attendance.view', 'level' => 'view'],
            ],

            // Officer
            'officer' => [
                ['permission' => 'transactions.view', 'level' => 'view'],
                ['permission' => 'inventory.view', 'level' => 'view'],
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'attendance.view', 'level' => 'view'],
            ],

            // Support Staff
            'support_staff' => [
                ['permission' => 'process.view', 'level' => 'view'],
                ['permission' => 'attendance.view', 'level' => 'view'],
            ],

            // ICT
            'ict' => [
                ['permission' => 'hr.view', 'level' => 'view'],
                ['permission' => 'reports.view', 'level' => 'full'],
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            
            foreach ($permissions as $perm) {
                $permission = DB::table('permissions')->where('name', $perm['permission'])->first();
                
                if ($role && $permission) {
                    DB::table('role_permission')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'access_level' => $perm['level'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}