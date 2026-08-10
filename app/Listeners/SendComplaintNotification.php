<?php

namespace App\Listeners;

use App\Events\ComplaintReplyAdded;
use App\Events\ComplaintStatusUpdated;
use App\Notifications\BaseNotification;
use Illuminate\Support\Facades\App;

class SendComplaintNotification
{
    public function handle(object $event): void
    {
        $event->complaint->loadMissing('client.user');

        $user = $event->complaint->client?->user;

        if (!$user) {
            return;
        }

        $originalLocale = App::getLocale();

        $userLocale = $user->locale ?? $user->lang ?? $originalLocale;

        App::setLocale($userLocale);

        try {
            if ($event instanceof ComplaintStatusUpdated) {
                $statusLabel = $event->complaint->status->value;

                $user->notify(new BaseNotification(
                    title: __('notifications.complaint_status_updated.title'),
                    body: __('notifications.complaint_status_updated.body', [
                        'status' => $statusLabel
                    ]),
                    data: [
                        'type'         => 'complaint_status_updated',
                        'complaint_id' => (string) $event->complaint->id,
                        'status'       => $event->complaint->status->value,
                    ],
                    actionUrl: "/complaints/{$event->complaint->id}"
                ));
            }

            if ($event instanceof ComplaintReplyAdded) {
                $user->notify(new BaseNotification(
                    title: __('notifications.complaint_reply_added.title'),
                    body: __('notifications.complaint_reply_added.body', [
                        'id' => $event->complaint->id
                    ]),
                    data: [
                        'type'         => 'complaint_reply_added',
                        'complaint_id' => (string) $event->complaint->id,
                    ],
                    actionUrl: "/complaints/{$event->complaint->id}"
                ));
            }
        } finally {
            App::setLocale($originalLocale);
        }
    }
}
