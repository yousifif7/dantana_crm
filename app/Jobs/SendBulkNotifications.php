<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $userIds,
        public string $type,
        public string $title,
        public string $message,
        public ?array $data = null
    ) {
    }

    public function handle(NotificationService $notificationService): void
    {
        $users = \App\Models\User::whereIn('id', $this->userIds)->get();
        
        foreach ($users as $user) {
            $notificationService->create(
                $user,
                $this->type,
                $this->title,
                $this->message,
                $this->data
            );
        }
    }
}