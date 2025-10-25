<?php

namespace App\Listeners;

use App\Events\ProcessOverdue;
use App\Notifications\ProcessOverdueNotification;

class NotifyProcessAssignee
{
    public function handle(ProcessOverdue $event): void
    {
        $process = $event->process->load('assignedUser.supervisor');
        
        // Notify assigned user
        $process->assignedUser->notify(
            new ProcessOverdueNotification($process)
        );
        
        // Also notify their supervisor if they have one
        if ($process->assignedUser->supervisor) {
            $process->assignedUser->supervisor->notify(
                new ProcessOverdueNotification($process)
            );
        }
    }
}