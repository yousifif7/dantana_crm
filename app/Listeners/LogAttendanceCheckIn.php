<?php

namespace App\Listeners;

use App\Events\AttendanceCheckedIn;
use App\Services\AuditService;

class LogAttendanceCheckIn
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function handle(AttendanceCheckedIn $event): void
    {
        $attendance = $event->attendance;
        
        $this->auditService->log(
            $attendance->user,
            'checked_in',
            'attendance',
            $attendance
        );
    }
}
