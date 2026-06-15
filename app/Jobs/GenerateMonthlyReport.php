<?php

namespace App\Jobs;

use App\Services\ReportService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $startDate,
        public string $endDate,
        public ?int $userId = null
    ) {
    }

    public function handle(ReportService $reportService): void
    {
        $financial = $reportService->financialReport($this->startDate, $this->endDate);
        $production = $reportService->productionReport($this->startDate, $this->endDate);
        $inventory = $reportService->inventoryReport();
        
        $report = [
            'period' => ['start' => $this->startDate, 'end' => $this->endDate],
            'financial' => $financial,
            'production' => $production,
            'inventory' => $inventory,
            'generated_at' => now()->toDateTimeString(),
        ];
        
        // Save report to storage
        $filename = "reports/monthly_" . date('Y_m', strtotime($this->startDate)) . ".json";
        Storage::put($filename, json_encode($report, JSON_PRETTY_PRINT));
        
        // If userId provided, notify user that report is ready
        if ($this->userId) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                app(NotificationService::class)->create(
                    $user,
                    'report_ready',
                    'Monthly Report Generated',
                    "Your monthly report for {$this->startDate} to {$this->endDate} is ready.",
                    ['filename' => $filename]
                );
            }
        }
    }
}