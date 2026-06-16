<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\VerifyEmailRequest;
use App\Services\User\EmailVerificationService;
use App\Traits\ResponseTrait;

class VerificationController extends Controller
{
    use ResponseTrait;

    public function __construct(private EmailVerificationService $emailService) {}

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $this->emailService->verifyEmail($request->validated());
        return $this->successResponse([], __('messages.auth.email_verified'));
    }
}
