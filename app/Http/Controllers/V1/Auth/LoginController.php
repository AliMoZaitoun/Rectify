<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RefreshTokenRequest;
use App\Services\User\LoginService;
use App\Traits\ProvidesUserResource;
use App\Traits\ResponseTrait;

class LoginController extends Controller
{
    use ResponseTrait, ProvidesUserResource;

    public function __construct(private LoginService $loginService) {}

    public function login(LoginRequest $request)
    {
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $loginField => $request->input('login'),
            'password'  => $request->input('password'),
        ];

        $authData = $this->loginService->login($credentials, $loginField, $request->userAgent());
        $authData['user'] = $this->resolveUserResource($authData['user']);

        return $this->successResponse($authData);
    }

    public function logout()
    {
        $this->loginService->logout(request()->user()->currentAccessToken());
        return $this->successResponse([], __('messages.auth.logout'));
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        $currentToken = $request->user()->currentAccessToken();
        $data['tokens'] = $this->loginService->refreshToken($request->input('refresh_token'), $currentToken);
        return $this->successResponse($data, code: 201);
    }
}
