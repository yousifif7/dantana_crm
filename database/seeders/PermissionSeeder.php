<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Finance Module
            ['name' => 'transactions.view', 'display_name' => 'View Transactions', 'module' => 'finance'],
            ['name' => 'transactions.create', 'display_name' => 'Create Transactions', 'module' => 'finance'],
            ['name' => 'transactions.edit', 'display_name' => 'Edit Transactions', 'module' => 'finance'],
            ['name' => 'transactions.delete', 'display_name' => 'Delete Transactions', 'module' => 'finance'],
            ['name' => 'transactions.approve', 'display_name' => 'Approve Transactions', 'module' => 'finance'],

            // HR Module
            ['name' => 'hr.view', 'display_name' => 'View HR Data', 'module' => 'hr'],
            ['name' => 'hr.create', 'display_name' => 'Create HR Records', 'module' => 'hr'],
            ['name' => 'hr.edit', 'display_name' => 'Edit HR Records', 'module' => 'hr'],
            ['name' => 'hr.delete', 'display_name' => 'Delete HR Records', 'module' => 'hr'],

            // Procurement Module
            ['name' => 'procurement.view', 'display_name' => 'View Procurement', 'module' => 'procurement'],
            ['name' => 'procurement.create', 'display_name' => 'Create Purchase Orders', 'module' => 'procurement'],
            ['name' => 'procurement.edit', 'display_name' => 'Edit Purchase Orders', 'module' => 'procurement'],
            ['name' => 'procurement.approve', 'display_name' => 'Approve Purchases', 'module' => 'procurement'],

            // Inventory Module
            ['name' => 'inventory.view', 'display_name' => 'View Inventory', 'module' => 'inventory'],
            ['name' => 'inventory.create', 'display_name' => 'Add Inventory Items', 'module' => 'inventory'],
            ['name' => 'inventory.edit', 'display_name' => 'Edit Inventory Items', 'module' => 'inventory'],
            ['name' => 'inventory.adjust', 'display_name' => 'Adjust Stock Levels', 'module' => 'inventory'],
            ['name' => 'inventory.delete', 'display_name' => 'Delete Inventory Items', 'module' => 'inventory'],

            // Production Module
            ['name' => 'production.view', 'display_name' => 'View Production', 'module' => 'production'],
            ['name' => 'production.create', 'display_name' => 'Create Production Records', 'module' => 'production'],
            ['name' => 'production.edit', 'display_name' => 'Edit Production Records', 'module' => 'production'],
            ['name' => 'production.approve', 'display_name' => 'Approve Production', 'module' => 'production'],

            // Process Module
            ['name' => 'process.view', 'display_name' => 'View Processes', 'module' => 'process'],
            ['name' => 'process.create', 'display_name' => 'Create Processes', 'module' => 'process'],
            ['name' => 'process.edit', 'display_name' => 'Edit Processes', 'module' => 'process'],
            ['name' => 'process.delete', 'display_name' => 'Delete Processes', 'module' => 'process'],

            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'module' => 'reports'],

            // Attendance
            ['name' => 'attendance.view', 'display_name' => 'View Attendance', 'module' => 'attendance'],
            ['name' => 'attendance.manage', 'display_name' => 'Manage Attendance', 'module' => 'attendance'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                ...$permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}