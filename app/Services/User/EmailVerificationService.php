<?php

namespace App\Services\User;

use App\DAO\UserDAO;
use App\Services\OtpService;

class EmailVerificationService
{
    public function __construct(
        private UserDAO $userDAO,
        private OtpService $otpService
    ) {}

    public function verifyEmail(array $data)
    {
        $user = $this->userDAO->findByEmail($data['email']);

        $this->otpService->verifyCode($user->id, $data['code']);

        return $this->userDAO->verify($user);
    }
}
