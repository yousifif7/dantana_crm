<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InventoryItem $item)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert - Action Required')
            ->error()
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("**ALERT:** Low stock detected for an inventory item.")
            ->line("**Item:** {$this->item->name}")
            ->line("**Current Stock:** {$this->item->stock_quantity} {$this->item->unit_of_measure}")
            ->line("**Reorder Level:** {$this->item->reorder_level} {$this->item->unit_of_measure}")
            ->line("**Status:** {$this->item->status}")
            ->action('View Item', url('/inventory/' . $this->item->id))
            ->line('Please reorder this item as soon as possible.')
            ->salutation('Best regards, Dantata Foods UBMS');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_code' => $this->item->item_code,
            'item_name' => $this->item->name,
            'stock_quantity' => $this->item->stock_quantity,
            'reorder_level' => $this->item->reorder_level,
            'unit_of_measure' => $this->item->unit_of_measure,
            'status' => $this->item->status,
        ];
    }
}