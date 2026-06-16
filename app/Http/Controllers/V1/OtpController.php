<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private OtpService $otpService
    ) {}

    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required']);
        $email = $request->input('email');
        $otpCode = $this->otpService->resendCode($email);
        $data = ['otp' => $otpCode];
        return $this->successResponse($data, __('messages.auth.otp_sent'), 201);
    }
}
