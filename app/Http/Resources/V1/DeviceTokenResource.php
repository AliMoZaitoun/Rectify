<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'fcm_token'   => $this->fcm_token,
            'device_type' => $this->device_type->value ?? $this->device_type,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
