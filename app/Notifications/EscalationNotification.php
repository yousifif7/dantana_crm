<?php

namespace App\Notifications;

use App\Models\Escalation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Escalation $escalation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $escalatableType = class_basename($this->escalation->escalatable_type);
        
        return (new MailMessage)
            ->subject('Item Escalated to You - Action Required')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("A {$escalatableType} has been escalated to you.")
            ->line("**Reason:** {$this->escalation->reason}")
            ->line("**From:** {$this->escalation->fromUser->full_name}")
            ->line("**Escalated At:** {$this->escalation->escalated_at->format('Y-m-d H:i:s')}")
            ->when($this->escalation->description, function ($mail) {
                return $mail->line("**Description:** {$this->escalation->description}");
            })
            ->action('View Details', url('/escalations/' . $this->escalation->id))
            ->line('Please review and take appropriate action.')
            ->salutation('Best regards, Dantata Foods UBMS');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'escalation_id' => $this->escalation->id,
            'escalatable_type' => class_basename($this->escalation->escalatable_type),
            'escalatable_id' => $this->escalation->escalatable_id,
            'from_user' => $this->escalation->fromUser->full_name,
            'reason' => $this->escalation->reason,
            'escalated_at' => $this->escalation->escalated_at->toIso8601String(),
        ];
    }
}
