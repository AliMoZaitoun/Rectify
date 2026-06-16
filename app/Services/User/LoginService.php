<?php

namespace App\Services\User;

use App\DAO\RefreshTokenDAO;
use App\DAO\UserDAO;
use App\Exceptions\InvalidCredentialException;
use App\Exceptions\V1\InvalidRefreshTokenException;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Str;

class LoginService
{
    public function __construct(
        private UserDAO $userDAO,
        private RefreshTokenDAO $refreshTokenDAO,
        private OtpService $otpService
    ) {}

    public function login(array $credentials, string $loginType, ?string $userAgent)
    {
        $user = $this->userDAO->findByField($loginType, $credentials[$loginType]);

        if (!$user || !password_verify($credentials['password'], $user->password)) {
            throw new InvalidCredentialException();
        }

        $accessTokenResult = $user->createToken('auth_token', ['*']);
        $accessTokenResult->accessToken->update(['expires_at' => now()->addWeeks(7)]);

        $this->refreshTokenDAO->delete($user->id, $userAgent);

        $plainRefreshToken = Str::random(64);
        $hashedToken = hash('sha256', $plainRefreshToken);
        $this->refreshTokenDAO->store($user->id, $hashedToken, $userAgent);

        $permissions = $user->roles
            ->flatMap(fn($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values()
            ->toArray();

        return [
            'user'          => $user,
            'tokens'        => [
                'access_token'  => $accessTokenResult->plainTextToken,
                'refresh_token' => $plainRefreshToken,
            ],
            'permissions'   => $permissions
        ];
    }

    public function logout($currentAccessToken)
    {
        return $currentAccessToken->delete();
    }

    public function refreshToken(string $refreshToken, $currentToken)
    {
        $hashedToken = hash('sha256', $refreshToken);
        $storedToken = $this->refreshTokenDAO->findByToken($hashedToken);

        if (!$storedToken) {
            throw new InvalidRefreshTokenException();
        }

        $user = $this->userDAO->findById($storedToken->user_id);
        $this->logout($currentToken);

        $access_token = $user->createToken('auth_token')->plainTextToken;

        $plainRefresh = Str::random(64);
        $hashedRefresh = hash('sha256', $plainRefresh);
        $this->refreshTokenDAO->update($storedToken, $hashedRefresh);

        $tokens = [
            'access_token' => $access_token,
            'refresh_token' => $plainRefresh,
        ];

        return $tokens;
    }
}
