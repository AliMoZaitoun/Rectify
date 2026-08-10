<?php

namespace App\Listeners;

use App\Events\ComplaintReplyAdded;
use App\Events\ComplaintStatusUpdated;
use App\Models\UserDeviceToken;
use App\Notifications\BaseNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SendComplaintNotification
{
    public function handle(object $event): void
    {
        $complaint = $event->complaint;
        $complaint->loadMissing('client.user');

        $user = $complaint->client?->user;
        $trackingCode = $complaint->tracking_code;

        if ($user && !$complaint->is_anonymous) {
            $originalLocale = App::getLocale();
            $userLocale = $user->locale ?? $user->lang ?? $originalLocale;

            App::setLocale($userLocale);

            try {
                if ($event instanceof ComplaintStatusUpdated) {
                    $statusLabel = method_exists($complaint->status, 'label')
                        ? $complaint->status->label()
                        : $complaint->status->value;

                    $user->notify(new BaseNotification(
                        title: __('notifications.complaint_status_updated.title'),
                        body: __('notifications.complaint_status_updated.body', [
                            'tracking_code' => $trackingCode,
                            'status'        => $statusLabel,
                        ]),
                        data: [
                            'type'          => 'complaint_status_updated',
                            'tracking_code' => (string) $trackingCode,
                            'status'        => $complaint->status->value,
                        ],
                        actionUrl: "/complaints/{$trackingCode}"
                    ));
                }

                if ($event instanceof ComplaintReplyAdded) {
                    $user->notify(new BaseNotification(
                        title: __('notifications.complaint_reply_added.title'),
                        body: __('notifications.complaint_reply_added.body', [
                            'tracking_code' => $trackingCode,
                        ]),
                        data: [
                            'type'          => 'complaint_reply_added',
                            'tracking_code' => (string) $trackingCode,
                        ],
                        actionUrl: "/complaints/{$trackingCode}"
                    ));
                }
            } finally {
                App::setLocale($originalLocale);
            }

            return;
        }

        if ($complaint->device_id) {
            $tokens = UserDeviceToken::where('device_id', $complaint->device_id)
                ->pluck('fcm_token')
                ->filter()
                ->toArray();

            if (empty($tokens)) {
                Log::warning("No FCM tokens found for complaint device_id: {$complaint->device_id}");
                return;
            }

            $title = '';
            $body = '';
            $data = [];

            if ($event instanceof ComplaintStatusUpdated) {
                $statusLabel = method_exists($complaint->status, 'label')
                    ? $complaint->status->label()
                    : $complaint->status->value;

                $title = __('notifications.complaint_status_updated.title');
                $body  = __('notifications.complaint_status_updated.body', [
                    'tracking_code' => $trackingCode,
                    'status'        => $statusLabel,
                ]);
                $data  = [
                    'type'          => 'complaint_status_updated',
                    'tracking_code' => (string) $trackingCode,
                    'status'        => $complaint->status->value,
                ];
            }

            if ($event instanceof ComplaintReplyAdded) {
                $title = __('notifications.complaint_reply_added.title');
                $body  = __('notifications.complaint_reply_added.body', [
                    'tracking_code' => $trackingCode,
                ]);
                $data  = [
                    'type'          => 'complaint_reply_added',
                    'tracking_code' => (string) $trackingCode,
                ];
            }

            $this->sendDirectFcmNotification($tokens, $title, $body, $data);
        }
    }

    protected function sendDirectFcmNotification(array $tokens, string $title, string $body, array $data = []): void
    {
        try {
            $messaging = app('firebase.messaging');

            $safeData = array_map('strval', $data);
            $message = \Kreait\Firebase\Messaging\CloudMessage::new()
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $body))
                ->withData($safeData);

            $report = $messaging->sendMulticast($message, $tokens);

            Log::info("Guest FCM Sent for Complaint.", [
                'success_count' => $report->successes()->count(),
                'failure_count' => $report->failures()->count(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to send Guest FCM Notification: " . $e->getMessage());
        }
    }
}
