<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $requester = User::whereHas('role', fn ($q) => $q->where('name', 'procurement_officer'))->first()
            ?? User::first();

        $orders = [
            [
                'vendor_name' => 'Agro Supplies Ltd',
                'description' => 'Raw palm fruit supply for Q1 production',
                'line_items' => [
                    ['name' => 'Palm Fruit (tonnes)', 'quantity' => 50, 'unit_price' => 85000],
                    ['name' => 'Transport', 'quantity' => 1, 'unit_price' => 120000],
                ],
                'total_amount' => 4370000,
                'status' => 'pending',
                'category' => 'raw_materials',
                'expected_delivery_date' => now()->addDays(14)->toDateString(),
            ],
            [
                'vendor_name' => 'PackRight Nigeria',
                'description' => 'PET bottles and labelling for cooking oil line',
                'line_items' => [
                    ['name' => '1L PET Bottles', 'quantity' => 10000, 'unit_price' => 45],
                    ['name' => 'Labels (rolls)', 'quantity' => 20, 'unit_price' => 15000],
                ],
                'total_amount' => 750000,
                'status' => 'approved',
                'category' => 'packaging',
                'expected_delivery_date' => now()->addDays(7)->toDateString(),
            ],
            [
                'vendor_name' => 'Industrial Parts Co.',
                'description' => 'Spare parts for expeller press maintenance',
                'line_items' => [
                    ['name' => 'Bearing Set', 'quantity' => 4, 'unit_price' => 25000],
                    ['name' => 'Filter Cartridges', 'quantity' => 12, 'unit_price' => 8000],
                ],
                'total_amount' => 196000,
                'status' => 'fulfilled',
                'category' => 'maintenance',
                'expected_delivery_date' => now()->subDays(5)->toDateString(),
            ],
        ];

        foreach ($orders as $data) {
            PurchaseOrder::create([
                ...$data,
                'requested_by' => $requester->id,
                'approved_by' => in_array($data['status'], ['approved', 'fulfilled']) ? User::whereHas('role', fn ($q) => $q->where('name', 'md'))->first()?->id : null,
                'approved_at' => in_array($data['status'], ['approved', 'fulfilled']) ? now()->subDays(3) : null,
            ]);
        }
    }
}
