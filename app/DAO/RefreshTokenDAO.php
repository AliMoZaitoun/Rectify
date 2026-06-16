<?php

namespace App\DAO;

use App\Models\RefreshToken;

class RefreshTokenDAO
{
    public function findByToken(string $token)
    {
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function store(int $user_id, string $token, $device_name)
    {
        return RefreshToken::create([
            'user_id' => $user_id,
            'token' => $token,
            'device_name' => $device_name ?? 'unknown',
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function update(RefreshToken $storedToken, string $new_refresh_token)
    {
        $storedToken->update([
            'token' => $new_refresh_token,
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function delete(int $user_id, $device_name)
    {
        RefreshToken::where('user_id', $user_id)
            ->where('device_name', $device_name)
            ->delete();
    }
}
