<?php

namespace App\Jobs;

use App\Models\Escalation;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEscalation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Escalation $escalation)
    {
    }

    public function handle(NotificationService $notificationService): void
    {
        // Send notification to escalated user
        $notificationService->create(
            $this->escalation->toUser,
            'escalation',
            'Item Escalated to You',
            $this->escalation->reason,
            [
                'escalation_id' => $this->escalation->id,
                'from_user' => $this->escalation->fromUser->full_name,
            ]
        );
        
        // Send email notification (if email service is configured)
        // Mail::to($this->escalation->toUser->email)
        //     ->send(new EscalationMail($this->escalation));
    }
}