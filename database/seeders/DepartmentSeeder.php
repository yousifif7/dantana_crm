<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Production', 'code' => 'PROD', 'description' => 'Oil production and manufacturing'],
            ['name' => 'Supply Chain', 'code' => 'SUPPLY', 'description' => 'Procurement and logistics'],
            ['name' => 'Sales', 'code' => 'SALES', 'description' => 'Sales and marketing'],
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Administrative support'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Financial management'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human resource management'],
            ['name' => 'Information Technology', 'code' => 'ICT', 'description' => 'IT systems and support'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert([
                ...$dept,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
