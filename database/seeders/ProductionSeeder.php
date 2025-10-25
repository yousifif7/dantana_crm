<?php

namespace Database\Seeders;

use App\Models\ProductionRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('name', 'operations_officer');
        })->first() ?? User::first();

        $approver = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['general_manager', 'md']);
        })->first();

        $productions = [
            [
                'production_date' => now()->subDays(20),
                'quantity' => 3200.00,
                'efficiency_percentage' => 92,
                'downtime_hours' => 1.5,
                'notes' => 'Normal production day, minor equipment delay',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(19),
                'quantity' => 3400.00,
                'efficiency_percentage' => 94,
                'downtime_hours' => 0.5,
                'notes' => 'Excellent production efficiency',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(18),
                'quantity' => 3000.00,
                'efficiency_percentage' => 88,
                'downtime_hours' => 2.0,
                'notes' => 'Scheduled maintenance performed',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(17),
                'quantity' => 3100.00,
                'efficiency_percentage' => 90,
                'downtime_hours' => 1.0,
                'notes' => 'Standard production cycle',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(16),
                'quantity' => 2900.00,
                'efficiency_percentage' => 87,
                'downtime_hours' => 2.5,
                'notes' => 'Raw material quality issues',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(15),
                'quantity' => 3300.00,
                'efficiency_percentage' => 93,
                'downtime_hours' => 0.75,
                'notes' => 'High quality input materials',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(14),
                'quantity' => 2600.00,
                'efficiency_percentage' => 85,
                'downtime_hours' => 3.0,
                'notes' => 'Power outage caused delays',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(13),
                'quantity' => 3500.00,
                'efficiency_percentage' => 95,
                'downtime_hours' => 0.25,
                'notes' => 'Peak performance day',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(12),
                'quantity' => 3250.00,
                'efficiency_percentage' => 91,
                'downtime_hours' => 1.25,
                'notes' => 'Routine operations',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(11),
                'quantity' => 2800.00,
                'efficiency_percentage' => 86,
                'downtime_hours' => 2.75,
                'notes' => 'Staff training conducted',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(10),
                'quantity' => 3150.00,
                'efficiency_percentage' => 89,
                'downtime_hours' => 1.5,
                'notes' => 'Normal production cycle',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(5),
                'quantity' => 3280.00,
                'efficiency_percentage' => 92,
                'downtime_hours' => 1.0,
                'notes' => 'Good production day',
                'status' => 'approved',
            ],
            [
                'production_date' => now()->subDays(3),
                'quantity' => 3100.00,
                'efficiency_percentage' => 90,
                'downtime_hours' => 1.5,
                'notes' => 'Standard operations',
                'status' => 'pending',
            ],
            [
                'production_date' => now()->subDays(2),
                'quantity' => 2950.00,
                'efficiency_percentage' => 88,
                'downtime_hours' => 2.0,
                'notes' => 'Minor equipment issues resolved',
                'status' => 'pending',
            ],
            [
                'production_date' => now()->subDays(1),
                'quantity' => 3400.00,
                'efficiency_percentage' => 94,
                'downtime_hours' => 0.5,
                'notes' => 'Excellent efficiency achieved',
                'status' => 'pending',
            ],
        ];

        foreach ($productions as $productionData) {
            ProductionRecord::create([
                'production_date' => $productionData['production_date'],
                'quantity' => $productionData['quantity'],
                'efficiency_percentage' => $productionData['efficiency_percentage'],
                'downtime_hours' => $productionData['downtime_hours'],
                'notes' => $productionData['notes'],
                'status' => $productionData['status'],
                'created_by' => $user->id,
                'approved_by' => $productionData['status'] === 'approved' ? $approver?->id : null,
            ]);
        }

        $this->command->info('Created ' . count($productions) . ' production records');
    }
}