<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Transaction $transaction)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transaction Approved')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("Your transaction has been approved.")
            ->line("**Transaction:** {$this->transaction->transaction_number}")
            ->line("**Description:** {$this->transaction->description}")
            ->line("**Amount:** " . number_format($this->transaction->amount, 2))
            ->line("**Approved By:** {$this->transaction->approver->full_name}")
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
            ->salutation('Best regards, Dantata Foods UBMS');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'description' => $this->transaction->description,
            'amount' => $this->transaction->amount,
            'approved_by' => $this->transaction->approver->full_name,
        ];
    }
}