<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Transaction $transaction, public string $reason = '')
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Transaction Rejected')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("Your transaction has been rejected.")
            ->line("**Transaction:** {$this->transaction->transaction_number}")
            ->line("**Description:** {$this->transaction->description}")
            ->line("**Amount:** " . number_format($this->transaction->amount, 2));
        
        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }
        
        return $mail
            ->line('Please review and resubmit if necessary.')
            ->salutation('Best regards, Dantata Foods UBMS');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'description' => $this->transaction->description,
            'amount' => $this->transaction->amount,
            'reason' => $this->reason,
        ];
    }
}
