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
                'device_id'   => $dto->deviceId,
                'device_type' => $dto->deviceType,
            ]
        );
    }

    public function deleteToken(?int $userId, string $fcmToken): bool
    {
        $query = UserDeviceToken::where('fcm_token', $fcmToken);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->delete() > 0;
    }
}
