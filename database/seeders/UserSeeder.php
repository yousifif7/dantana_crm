<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'employee_id' => 'EMP00001',
                'first_name' => 'Aliko',
                'last_name' => 'Dangote',
                'email' => 'chairman@dantatafoods.com',
                'password' => Hash::make('password'),
                'role_id' => DB::table('roles')->where('name', 'chairman')->value('id'),
                'department_id' => null,
                'hire_date' => '2020-01-01',
                'age' => 65,
                'is_active' => true,
            ],
            [
                'employee_id' => 'EMP00002',
                'first_name' => 'Muhammad',
                'last_name' => 'Ibrahim',
                'email' => 'md@dantatafoods.com',
                'password' => Hash::make('password'),
                'role_id' => DB::table('roles')->where('name', 'md')->value('id'),
                'department_id' => null,
                'hire_date' => '2020-02-01',
                'age' => 52,
                'is_active' => true,
            ],
            [
                'employee_id' => 'EMP00003',
                'first_name' => 'Fatima',
                'last_name' => 'Ahmed',
                'email' => 'cfo@dantatafoods.com',
                'password' => Hash::make('password'),
                'role_id' => DB::table('roles')->where('name', 'cfo')->value('id'),
                'department_id' => DB::table('departments')->where('code', 'FIN')->value('id'),
                'hire_date' => '2020-03-01',
                'age' => 45,
                'is_active' => true,
            ],
            [
                'employee_id' => 'EMP00004',
                'first_name' => 'Aisha',
                'last_name' => 'Usman',
                'email' => 'gm.production@dantatafoods.com',
                'password' => Hash::make('password'),
                'role_id' => DB::table('roles')->where('name', 'general_manager')->value('id'),
                'department_id' => DB::table('departments')->where('code', 'PROD')->value('id'),
                'hire_date' => '2020-04-01',
                'age' => 42,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                ...$user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
