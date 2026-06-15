<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(NotificationService::class);
        $users = User::where('is_active', true)->limit(5)->get();

        $samples = [
            ['type' => 'transaction_pending', 'title' => 'Transaction Awaiting Approval', 'message' => 'A new expense transaction requires your review.'],
            ['type' => 'production_pending', 'title' => 'Production Batch Submitted', 'message' => 'Batch BATCH-20251023 requires manager approval.'],
            ['type' => 'low_stock', 'title' => 'Low Stock Alert', 'message' => 'Palm oil inventory has fallen below reorder level.'],
            ['type' => 'escalation', 'title' => 'Item Escalated', 'message' => 'A pending approval has been escalated to your attention.'],
            ['type' => 'report_ready', 'title' => 'Monthly Report Ready', 'message' => 'The financial report for last month is available for download.'],
        ];

        foreach ($users as $i => $user) {
            foreach (array_slice($samples, 0, 3) as $j => $sample) {
                $service->create(
                    $user,
                    $sample['type'],
                    $sample['title'],
                    $sample['message'],
                    ['demo' => true, 'index' => $i * 3 + $j]
                );
            }
        }
    }
}
