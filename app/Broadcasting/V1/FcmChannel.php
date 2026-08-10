<?php

namespace App\Broadcasting\V1;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toFcm')) {
            $notification->toFcm($notifiable);
        }
    }
}
