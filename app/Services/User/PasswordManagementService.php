<?php

namespace App\Services\User;

use App\DAO\UserDAO;
use App\Exceptions\OtpCodeInvalidException;
use App\Exceptions\V1\InvalidPasswordException;
use App\Models\User;
use App\Services\OtpService;

class PasswordManagementService
{
    public function __construct(
        private UserDAO $userDAO,
        private OtpService $otpService
    ) {}

    public function changePassword(User $user, array $data)
    {
        if (!password_verify($data['current_password'], $user->password)) {
            throw new InvalidPasswordException();
        }

        return $this->userDAO->updatePassword($user, $data['new_password']);
    }

    public function forgotPassword(array $data)
    {
        $user = $this->userDAO->findByEmail($data['email']);

        return $this->otpService->createCode($user->id);
    }

    public function resetPassword(array $data)
    {
        $user = $this->userDAO->findByEmail($data['email']);

        if (!$this->otpService->verifyCode($user->id, $data['code'])) {
            throw new OtpCodeInvalidException();
        }

        return $this->userDAO->updatePassword($user, $data['new_password']);
    }
}
