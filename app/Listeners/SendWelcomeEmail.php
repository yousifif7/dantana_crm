<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Notifications\WelcomeNotification;

class SendWelcomeEmail
{
    public function handle(UserCreated $event): void
    {
        $user = $event->user->load('role', 'department');
        
        $user->notify(
            new WelcomeNotification($event->temporaryPassword)
        );
    }
}
