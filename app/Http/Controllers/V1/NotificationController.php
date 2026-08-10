<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificationResource;
use App\Models\Client;
use App\Notifications\BaseNotification;
use App\Services\NotificationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ResponseTrait;
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getUserNotifications(
            user: $request->user(),
            perPage: (int) $request->get('per_page', 15)
        );

        return $this->successCollection($notifications, NotificationResource::class);
    }

    public function unreadCount(Request $request)
    {
        $count = $this->notificationService->getUnreadCount($request->user());

        return $this->successResponse(['unread_count' => $count]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $updated = $this->notificationService->markAsRead($request->user(), $id);

        return $this->successResponse([], __('messages.common.updated'));
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead($request->user());

        return $this->successResponse([], __('messages.common.updated'));
    }

    public function test(int $clientId)
    {
        $client = $clientId
            ? Client::with('user')->find($clientId)
            : Client::with('user')->first();

        if (! $client || ! $client->user) {
            return $this->successResponse([], "Not Found", 404);
        }

        $user = $client->user;
        $trackingCode = 'TRK-TEST-123';

        $user->notify(new BaseNotification(
            title: __('notifications.complaint_reply_added.title'),
            body: __('notifications.complaint_reply_added.body', ['tracking_code' => $trackingCode]),
            data: [
                'type'          => 'test_event',
                'tracking_code' => $trackingCode,
            ],
            actionUrl: "/complaints/{$trackingCode}"
        ));

        $latestNotification = $user->notifications()->latest()->first();

        return $this->useResource($latestNotification, NotificationResource::class);
    }
}
