<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ChangePasswordRequest;
use App\Http\Requests\V1\ForgotPasswordRequest;
use App\Http\Requests\V1\ResetPasswordRequest;
use App\Services\User\PasswordManagementService;
use App\Traits\ResponseTrait;

class PasswordManagementController extends Controller
{
    use ResponseTrait;

    public function __construct(private PasswordManagementService $passwordService) {}

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->passwordService->changePassword($request->user(), $request->validated());
        return $this->successResponse([], __('messages.auth.password_changed'));
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $this->passwordService->forgotPassword($request->validated());
        return $this->successResponse($data, __('messages.auth.otp_sent'));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $this->passwordService->resetPassword($request->validated());
        return $this->successResponse([], __('messages.auth.password_changed'));
    }
}
