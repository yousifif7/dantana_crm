<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $temporaryPassword = '')
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Welcome to Dantata Foods UBMS')
            ->greeting("Welcome {$notifiable->first_name} {$notifiable->last_name}!")
            ->line("Your account has been created successfully.")
            ->line("**Employee ID:** {$notifiable->employee_id}")
            ->line("**Email:** {$notifiable->email}")
            ->line("**Role:** {$notifiable->role->display_name}")
            ->line("**Department:** " . ($notifiable->department?->name ?? 'Not assigned'));
        
        if ($this->temporaryPassword) {
            $mail->line("**Temporary Password:** {$this->temporaryPassword}")
                 ->line('**Important:** Please change your password after first login.');
        }
        
        return $mail
            ->action('Login to UBMS', url('/login'))
            ->line('If you have any questions, please contact the IT department.')
            ->salutation('Best regards, Dantata Foods IT Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'employee_id' => $notifiable->employee_id,
            'role' => $notifiable->role->display_name,
            'department' => $notifiable->department?->name,
            'has_temporary_password' => !empty($this->temporaryPassword),
        ];
    }
}