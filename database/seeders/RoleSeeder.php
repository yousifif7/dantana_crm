<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'chairman',
                'display_name' => 'Chairman',
                'description' => 'Strategic oversight, read-only access with approval rights',
                'hierarchy_level' => 1,
            ],
            [
                'name' => 'md',
                'display_name' => 'Managing Director',
                'description' => 'Full access, ultimate override authority',
                'hierarchy_level' => 2,
            ],
            [
                'name' => 'general_manager',
                'display_name' => 'General Manager',
                'description' => 'Approval and monitoring within department',
                'hierarchy_level' => 3,
            ],
            [
                'name' => 'department_head',
                'display_name' => 'Department Head',
                'description' => 'Manage staff, assign tasks, oversee execution',
                'hierarchy_level' => 4,
            ],
            [
                'name' => 'cfo',
                'display_name' => 'Chief Financial Officer',
                'description' => 'Finance module access only',
                'hierarchy_level' => 3,
            ],
            [
                'name' => 'hr_officer',
                'display_name' => 'HR Officer',
                'description' => 'HR module access only',
                'hierarchy_level' => 5,
            ],
            [
                'name' => 'procurement_officer',
                'display_name' => 'Procurement Officer',
                'description' => 'Procurement and limited inventory access',
                'hierarchy_level' => 5,
            ],
            [
                'name' => 'operations_officer',
                'display_name' => 'Operations Officer',
                'description' => 'Production module access only',
                'hierarchy_level' => 5,
            ],
            [
                'name' => 'executive',
                'display_name' => 'Executive',
                'description' => 'Input, reporting, and task handling',
                'hierarchy_level' => 6,
            ],
            [
                'name' => 'officer',
                'display_name' => 'Officer',
                'description' => 'Input-only roles',
                'hierarchy_level' => 6,
            ],
            [
                'name' => 'support_staff',
                'display_name' => 'Support Staff',
                'description' => 'Minimal access - requests, attendance, and task reception only',
                'hierarchy_level' => 7,
            ],
            [
                'name' => 'ict',
                'display_name' => 'ICT Officer',
                'description' => 'System administration, integrations, troubleshooting',
                'hierarchy_level' => 4,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                ...$role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
