<?php

namespace App\Services;

use App\DAO\DeviceTokenDAO;
use App\DTOs\DeviceTokenData;

class DeviceTokenService
{
    public function __construct(
        protected DeviceTokenDAO $deviceTokenDAO
    ) {}

    public function registerToken(DeviceTokenData $dto)
    {
        return $this->deviceTokenDAO->updateOrCreateToken($dto);
    }

    public function removeToken(?int $userId, string $fcmToken): bool
    {
        return $this->deviceTokenDAO->deleteToken($userId, $fcmToken);
    }
}
