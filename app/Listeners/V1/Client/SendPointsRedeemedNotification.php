<?php

namespace App\Listeners\V1\Client;

use App\Events\V1\Client\PointsRedeemedEvent;
use App\Notifications\V1\Client\PointsRedeemedNotification;

class SendPointsRedeemedNotification
{
    public function handle(PointsRedeemedEvent $event): void
    {
        $user = $event->client->user;

        if ($user) {
            $employeeName = $event->employee->user->full_name ?? $event->employee->user->first_name ?? 'موظف';

            $user->notify(new PointsRedeemedNotification($event->points, $employeeName));
        }
    }
}
