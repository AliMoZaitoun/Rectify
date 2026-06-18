<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\PasswordManagementController;
use App\Http\Controllers\V1\Auth\VerificationController;
use App\Http\Controllers\V1\Core\BranchController;
use App\Http\Controllers\V1\ClientController;
use App\Http\Controllers\V1\Core\EmployeeController;
use App\Http\Controllers\V1\Core\EmployeeBranchController;
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
    Route::put('', [ClientController::class, 'update']);
    Route::delete('{id}', [ClientController::class, 'destroy']);


    Route::get('profile', [ClientController::class, 'profile']);
});

Route::prefix('branch')->group(function () {
    Route::get('', [BranchController::class, 'index']);
    Route::post('', [BranchController::class, 'store']);
    Route::put('/{id}', [BranchController::class, 'update']);
    Route::delete('/{id}', [BranchController::class, 'destroy']);
});

Route::prefix('employee-branch')->group(function () {
    Route::get('', [EmployeeBranchController::class, 'index']);
    Route::post('', [EmployeeBranchController::class, 'store']);
    Route::put('/{id}', [EmployeeBranchController::class, 'update']);
    Route::delete('/{id}', [EmployeeBranchController::class, 'destroy']);
});

Route::prefix('employee')->group(function () {
    Route::get('', [EmployeeController::class, 'index']);
    Route::post('', [EmployeeController::class, 'store']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});
