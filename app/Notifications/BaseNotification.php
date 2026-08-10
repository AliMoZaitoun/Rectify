<?php

namespace App\Notifications;

use App\Broadcasting\V1\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class BaseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
        public ?string $actionUrl = null
    ) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'      => $this->title,
            'body'       => $this->body,
            'data'       => $this->data,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toFcm($notifiable)
    {
        $tokens = $notifiable->deviceTokens()->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            return;
        }

        $messaging = app('firebase.messaging');

        $safeData = array_map('strval', $this->data);
        $message = CloudMessage::new()
            ->withNotification(FcmNotification::create($this->title, $this->body))
            ->withData(array_merge($safeData, [
                'action_url' => $this->actionUrl ?? '',
            ]));

        $messaging->sendMulticast($message, $tokens);
    }
}
