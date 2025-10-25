<?php

namespace App\Listeners;

use App\Events\EscalationCreated;
use App\Notifications\EscalationNotification;

class SendEscalationNotification
{
    public function handle(EscalationCreated $event): void
    {
        $escalation = $event->escalation->load('toUser', 'fromUser');
        
        $escalation->toUser->notify(
            new EscalationNotification($escalation)
        );
    }
}