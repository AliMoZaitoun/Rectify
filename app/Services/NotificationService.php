<?php

namespace App\Services;

use App\DAO\NotificationDAO;
use App\Models\User;

class NotificationService
{
    public function __construct(
        protected NotificationDAO $notificationDAO
    ) {}

    public function getUserNotifications(User $user, int $perPage = 15)
    {
        return $this->notificationDAO->getUserNotifications($user, $perPage);
    }

    public function getUnreadCount(User $user): int
    {
        return $this->notificationDAO->getUnreadCount($user);
    }

    public function markAsRead(User $user, string $notificationId): bool
    {
        return $this->notificationDAO->markAsRead($user, $notificationId);
    }

    public function markAllAsRead(User $user): void
    {
        $this->notificationDAO->markAllAsRead($user);
    }
}
