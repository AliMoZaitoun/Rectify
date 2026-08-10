<?php

namespace App\DTOs;

class DeviceTokenData
{
    public function __construct(
        public ?int $userId,
        public string $deviceId,
        public string $fcmToken,
        public string $deviceType
    ) {}

    public static function fromRequest(?int $userId, array $validated): self
    {
        return new self(
            userId: $userId,
            deviceId: $validated['device_id'],
            fcmToken: $validated['fcm_token'],
            deviceType: $validated['device_type']
        );
    }
}
