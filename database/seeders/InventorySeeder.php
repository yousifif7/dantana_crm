<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['operations_officer', 'procurement_officer']);
        })->first() ?? User::first();

        $items = [
            [
                'name' => 'Refined Palm Oil',
                'description' => 'Premium quality refined palm oil for cooking',
                'stock_quantity' => 5000,
                'reorder_level' => 1000,
                'maximum_level' => 10000,
                'status' => 'in_stock',
                'unit_of_measure' => 'liters',
                'unit_price' => 850.00,
            ],
            [
                'name' => 'Crude Palm Oil',
                'description' => 'Unrefined palm oil for processing',
                'stock_quantity' => 3000,
                'reorder_level' => 2000,
                'maximum_level' => 8000,
                'status' => 'on_order',
                'unit_of_measure' => 'liters',
                'unit_price' => 650.00,
            ],
            [
                'name' => 'Vegetable Oil - 20L Jerry Can',
                'description' => 'Packaged vegetable oil in 20L containers',
                'stock_quantity' => 2500,
                'reorder_level' => 500,
                'maximum_level' => 5000,
                'status' => 'in_stock',
                'unit_of_measure' => 'units',
                'unit_price' => 12500.00,
            ],
            [
                'name' => 'Groundnut Oil',
                'description' => 'Pure groundnut oil',
                'stock_quantity' => 1500,
                'reorder_level' => 1000,
                'maximum_level' => 4000,
                'status' => 'low_stock',
                'unit_of_measure' => 'liters',
                'unit_price' => 1200.00,
            ],
            [
                'name' => 'Plastic Containers - 1L',
                'description' => 'Empty plastic bottles for packaging',
                'stock_quantity' => 8000,
                'reorder_level' => 2000,
                'maximum_level' => 15000,
                'status' => 'in_stock',
                'unit_of_measure' => 'units',
                'unit_price' => 45.00,
            ],
            [
                'name' => 'Plastic Containers - 5L',
                'description' => 'Empty plastic jerry cans for packaging',
                'stock_quantity' => 3500,
                'reorder_level' => 1000,
                'maximum_level' => 8000,
                'status' => 'in_stock',
                'unit_of_measure' => 'units',
                'unit_price' => 180.00,
            ],
            [
                'name' => 'Labels & Stickers',
                'description' => 'Product labels and branding stickers',
                'stock_quantity' => 12000,
                'reorder_level' => 3000,
                'maximum_level' => 20000,
                'status' => 'in_stock',
                'unit_of_measure' => 'units',
                'unit_price' => 5.00,
            ],
            [
                'name' => 'Carton Boxes - Large',
                'description' => 'Packaging cartons for shipping',
                'stock_quantity' => 800,
                'reorder_level' => 500,
                'maximum_level' => 2000,
                'status' => 'low_stock',
                'unit_of_measure' => 'units',
                'unit_price' => 250.00,
            ],
            [
                'name' => 'Palm Kernel',
                'description' => 'Raw palm kernel for oil extraction',
                'stock_quantity' => 6000,
                'reorder_level' => 2000,
                'maximum_level' => 12000,
                'status' => 'in_stock',
                'unit_of_measure' => 'kg',
                'unit_price' => 150.00,
            ],
            [
                'name' => 'Coconut Oil',
                'description' => 'Virgin coconut oil',
                'stock_quantity' => 450,
                'reorder_level' => 500,
                'maximum_level' => 2000,
                'status' => 'low_stock',
                'unit_of_measure' => 'liters',
                'unit_price' => 2500.00,
            ],
        ];

        foreach ($items as $itemData) {
            InventoryItem::create([
                ...$itemData,
                'created_by' => $user->id,
            ]);
        }

        $this->command->info('Created ' . count($items) . ' inventory items');
    }
}

