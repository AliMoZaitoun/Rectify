<?php

namespace App\DTOs;

use App\Enums\DeviceType;

class DeviceTokenData
{
    public function __construct(
        public readonly int $userId,
        public readonly string $fcmToken,
        public readonly DeviceType $deviceType,
    ) {}

    public static function fromRequest(int $userId, array $validated): self
    {
        return new self(
            userId: $userId,
            fcmToken: $validated['fcm_token'],
            deviceType: DeviceType::from($validated['device_type']),
        );
    }
}
