<?php

namespace App\DAO;

use App\DTOs\DeviceTokenData;
use App\Models\UserDeviceToken;

class DeviceTokenDAO
{
    public function updateOrCreateToken(DeviceTokenData $dto): UserDeviceToken
    {
        return UserDeviceToken::updateOrCreate(
            ['fcm_token' => $dto->fcmToken],
            [
                'user_id'     => $dto->userId,
                'device_type' => $dto->deviceType,
            ]
        );
    }

    public function deleteToken(int $userId, string $fcmToken): bool
    {
        return UserDeviceToken::where('fcm_token', $fcmToken)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
