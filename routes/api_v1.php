<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\PasswordManagementController;
use App\Http\Controllers\V1\Auth\VerificationController;
use App\Http\Controllers\V1\ClientController;
use App\Http\Controllers\V1\OtpController;

Route::post('verifyEmail', [VerificationController::class, 'verifyEmail']);
Route::post('login', [LoginController::class, 'login']);
Route::post('changePassword', [PasswordManagementController::class, 'changePassword'])->middleware('auth:sanctum');
Route::post('refreshToken', [LoginController::class, 'refreshToken'])->middleware('auth:sanctum');
Route::post('forgotPassword', [PasswordManagementController::class, 'forgotPassword']);
Route::post('resetPassword', [PasswordManagementController::class, 'resetPassword']);
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');


// OTP
Route::post('resendCode', [OtpController::class, 'resendCode']);

Route::prefix('client')->group(function () {
    Route::get('', [ClientController::class, 'index']);
    Route::post('', [ClientController::class, 'store']);
});
