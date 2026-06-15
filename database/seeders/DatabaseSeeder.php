<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            TransactionSeeder::class,
            InventorySeeder::class,
            ProductionSeeder::class,
            ProcessSeeder::class,
            PurchaseOrderSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
