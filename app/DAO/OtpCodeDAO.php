<?php

namespace App\DAO;

use App\Models\OtpCode;

class OtpCodeDAO
{
    public function store($userId, $code)
    {
        return OtpCode::create([
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => now()->addMinutes(10) // OTP expires in 10 minutes
        ]);
    }

    public function findValidCode($userId, $code)
    {
        return OtpCode::where('user_id', $userId)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteCodes(int $userId)
    {
        OtpCode::where('user_id', $userId)->delete();
    }
}
