<?php

namespace App\Notifications;

use App\Models\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProcessOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Process $process)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysOverdue = now()->diffInDays($this->process->due_date);
        
        return (new MailMessage)
            ->subject('Process Overdue - Immediate Action Required')
            ->error()
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("**ALERT:** A process assigned to you is overdue.")
            ->line("**Process:** {$this->process->name}")
            ->line("**Due Date:** {$this->process->due_date->format('Y-m-d')}")
            ->line("**Days Overdue:** {$daysOverdue}")
            ->line("**Priority:** " . $this->getPriorityLabel())
            ->action('View Process', url('/processes/' . $this->process->id))
            ->line('Please complete this process immediately.')
            ->salutation('Best regards, Dantata Foods UBMS');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'process_id' => $this->process->id,
            'process_number' => $this->process->process_number,
            'process_name' => $this->process->name,
            'due_date' => $this->process->due_date->toIso8601String(),
            'days_overdue' => now()->diffInDays($this->process->due_date),
            'priority' => $this->process->priority,
        ];
    }

    private function getPriorityLabel(): string
    {
        return match($this->process->priority) {
            1 => 'Critical',
            2 => 'High',
            3 => 'Medium',
            4 => 'Low',
            5 => 'Very Low',
            default => 'Medium',
        };
    }
}
