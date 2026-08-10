<?php

namespace App\Models;

use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'fcm_token', 'device_type'])]
class UserDeviceToken extends Model
{
    protected $casts = [
        'device_type' => DeviceType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
