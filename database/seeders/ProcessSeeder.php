<?php

namespace Database\Seeders;

use App\Models\Process;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['department_head', 'general_manager']);
        })->first() ?? User::first();

        $assignees = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['executive', 'officer', 'operations_officer']);
        })->get();

        if ($assignees->isEmpty()) {
            $assignees = User::limit(4)->get();
        }

        $processes = [
            [
                'name' => 'Procure Raw Materials - Palm Kernels',
                'description' => 'Purchase 5000kg of palm kernels from local suppliers',
                'status' => 'completed',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->subDays(7),
                'completed_at' => now()->subDays(8),
                'priority' => 2,
            ],
            [
                'name' => 'Quality Inspection - Batch #2024-001',
                'description' => 'Conduct quality control tests on recent production batch',
                'status' => 'completed',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->subDays(5),
                'completed_at' => now()->subDays(6),
                'priority' => 1,
            ],
            [
                'name' => 'Equipment Maintenance - Production Line A',
                'description' => 'Scheduled maintenance for oil extraction machinery',
                'status' => 'in_progress',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(2),
                'completed_at' => null,
                'priority' => 2,
            ],
            [
                'name' => 'Packaging Material Restock',
                'description' => 'Order 5000 units of 5L plastic containers',
                'status' => 'in_progress',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(5),
                'completed_at' => null,
                'priority' => 3,
            ],
            [
                'name' => 'Client Order Fulfillment - SuperMart',
                'description' => 'Prepare and ship 200 units of vegetable oil to SuperMart',
                'status' => 'in_progress',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(3),
                'completed_at' => null,
                'priority' => 1,
            ],
            [
                'name' => 'Monthly Inventory Audit',
                'description' => 'Conduct physical count and reconcile inventory records',
                'status' => 'pending',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(10),
                'completed_at' => null,
                'priority' => 3,
            ],
            [
                'name' => 'Prepare Q4 Sales Report',
                'description' => 'Compile sales data and prepare quarterly report',
                'status' => 'pending',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(15),
                'completed_at' => null,
                'priority' => 4,
            ],
            [
                'name' => 'Staff Training - Safety Procedures',
                'description' => 'Conduct mandatory safety training for production staff',
                'status' => 'pending',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(20),
                'completed_at' => null,
                'priority' => 2,
            ],
            [
                'name' => 'Update Product Labels',
                'description' => 'Redesign and print new product labels with updated information',
                'status' => 'pending',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(12),
                'completed_at' => null,
                'priority' => 3,
            ],
            [
                'name' => 'Negotiate Supplier Contract',
                'description' => 'Review and negotiate terms with main palm kernel supplier',
                'status' => 'in_progress',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->addDays(7),
                'completed_at' => null,
                'priority' => 2,
            ],
            [
                'name' => 'OVERDUE: Customer Complaint Resolution',
                'description' => 'Address and resolve quality complaint from Lagos Retail Store',
                'status' => 'in_progress',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->subDays(3),
                'completed_at' => null,
                'priority' => 1,
            ],
            [
                'name' => 'OVERDUE: Warehouse Organization',
                'description' => 'Reorganize warehouse storage for better efficiency',
                'status' => 'pending',
                'assigned_to' => $assignees->random()->id,
                'due_date' => now()->subDays(5),
                'completed_at' => null,
                'priority' => 3,
            ],
        ];

        foreach ($processes as $processData) {
            Process::create([
                'name' => $processData['name'],
                'description' => $processData['description'],
                'status' => $processData['status'],
                'assigned_to' => $processData['assigned_to'],
                'created_by' => $creator->id,
                'due_date' => $processData['due_date'],
                'completed_at' => $processData['completed_at'],
                'priority' => $processData['priority'],
            ]);
        }

        $this->command->info('Created ' . count($processes) . ' processes');
    }
}