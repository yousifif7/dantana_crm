<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['cfo', 'executive', 'officer']);
        })->get();

        if ($users->isEmpty()) {
            $users = User::limit(3)->get();
        }

        $transactions = [
            // Revenue Transactions
            [
                'type' => 'revenue',
                'description' => 'Product Sales - Vegetable Oil 20L',
                'amount' => 125000.00,
                'transaction_date' => now()->subDays(2),
                'category' => 'Product Sales',
                'client_name' => 'SuperMart Nigeria Ltd',
                'status' => 'approved',
                'approved_at' => now()->subDays(1),
            ],
            [
                'type' => 'revenue',
                'description' => 'Bulk Order - Cooking Oil 50 Units',
                'amount' => 87500.00,
                'transaction_date' => now()->subDays(5),
                'category' => 'Bulk Sales',
                'client_name' => 'Lagos Retail Store',
                'status' => 'approved',
                'approved_at' => now()->subDays(4),
            ],
            [
                'type' => 'revenue',
                'description' => 'Product Sales - Palm Oil',
                'amount' => 65000.00,
                'transaction_date' => now()->subDays(7),
                'category' => 'Product Sales',
                'client_name' => 'Abuja Distribution Center',
                'status' => 'approved',
                'approved_at' => now()->subDays(6),
            ],
            [
                'type' => 'revenue',
                'description' => 'Export Order - Refined Oil',
                'amount' => 250000.00,
                'transaction_date' => now()->subDays(10),
                'category' => 'Export',
                'client_name' => 'Ghana Trading Company',
                'status' => 'approved',
                'approved_at' => now()->subDays(9),
            ],
            [
                'type' => 'revenue',
                'description' => 'Wholesale - Mixed Products',
                'amount' => 180000.00,
                'transaction_date' => now()->subDays(1),
                'category' => 'Wholesale',
                'client_name' => 'Port Harcourt Wholesalers',
                'status' => 'pending',
                'approved_at' => null,
            ],

            // Expense Transactions
            [
                'type' => 'expense',
                'description' => 'Office Rent - October',
                'amount' => 5000.00,
                'transaction_date' => now()->subDays(15),
                'category' => 'Rent',
                'client_name' => null,
                'status' => 'approved',
                'approved_at' => now()->subDays(14),
            ],
            [
                'type' => 'expense',
                'description' => 'Raw Materials - Palm Kernels',
                'amount' => 45000.00,
                'transaction_date' => now()->subDays(8),
                'category' => 'Raw Materials',
                'client_name' => 'Local Farmers Cooperative',
                'status' => 'approved',
                'approved_at' => now()->subDays(7),
            ],
            [
                'type' => 'expense',
                'description' => 'Marketing Campaign - Q4',
                'amount' => 15000.00,
                'transaction_date' => now()->subDays(12),
                'category' => 'Marketing',
                'client_name' => null,
                'status' => 'approved',
                'approved_at' => now()->subDays(11),
            ],
            [
                'type' => 'expense',
                'description' => 'Utilities - Electricity & Water',
                'amount' => 8500.00,
                'transaction_date' => now()->subDays(6),
                'category' => 'Utilities',
                'client_name' => null,
                'status' => 'approved',
                'approved_at' => now()->subDays(5),
            ],
            [
                'type' => 'expense',
                'description' => 'Equipment Maintenance',
                'amount' => 12000.00,
                'transaction_date' => now()->subDays(4),
                'category' => 'Maintenance',
                'client_name' => 'Tech Services Ltd',
                'status' => 'approved',
                'approved_at' => now()->subDays(3),
            ],
            [
                'type' => 'expense',
                'description' => 'Staff Training Program',
                'amount' => 25000.00,
                'transaction_date' => now()->subDays(3),
                'category' => 'Training',
                'client_name' => null,
                'status' => 'pending',
                'approved_at' => null,
            ],
            [
                'type' => 'expense',
                'description' => 'Transportation & Logistics',
                'amount' => 18000.00,
                'transaction_date' => now()->subDays(2),
                'category' => 'Logistics',
                'client_name' => 'Swift Logistics',
                'status' => 'pending',
                'approved_at' => null,
            ],
        ];

        foreach ($transactions as $index => $transactionData) {
            $user = $users->random();
            
            $transaction = Transaction::create([
                'type' => $transactionData['type'],
                'description' => $transactionData['description'],
                'amount' => $transactionData['amount'],
                'transaction_date' => $transactionData['transaction_date'],
                'category' => $transactionData['category'],
                'client_name' => $transactionData['client_name'],
                'status' => $transactionData['status'],
                'created_by' => $user->id,
                'approved_by' => $transactionData['status'] === 'approved' 
                    ? User::whereHas('role', function ($q) {
                        $q->whereIn('name', ['md', 'cfo']);
                    })->first()?->id 
                    : null,
                'approved_at' => $transactionData['approved_at'],
                'notes' => $transactionData['status'] === 'pending' 
                    ? 'Awaiting approval' 
                    : 'Approved and processed',
            ]);
        }

        $this->command->info('Created ' . count($transactions) . ' transactions');
    }
}