<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\{GenerateMonthlyReport, CleanupExpiredTokens, SendBulkNotifications, SyncInventoryLevels, ProcessEscalation};
use App\Services\EscalationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CleanupExpiredTokens)->daily();
Schedule::job(new SyncInventoryLevels)->hourly();
Schedule::call(function () {
    $start = now()->subMonth()->startOfMonth()->toDateString();
    $end = now()->subMonth()->endOfMonth()->toDateString();
    GenerateMonthlyReport::dispatch($start, $end);
})->monthlyOn(1, '06:00');
Schedule::call(function (EscalationService $escalationService) {
    $escalationService->checkForOverdueApprovals();
})->hourly();
