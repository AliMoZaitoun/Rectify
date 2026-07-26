<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\PasswordManagementController;
use App\Http\Controllers\V1\Auth\VerificationController;
use App\Http\Controllers\V1\Core\BranchController;
use App\Http\Controllers\V1\ClientController;
use App\Http\Controllers\V1\Complaint\CategoryController;
use App\Http\Controllers\V1\Complaint\ComplaintController;
use App\Http\Controllers\V1\Core\EmployeeController;
use App\Http\Controllers\V1\Core\EmployeeBranchController;
use App\Http\Controllers\V1\LocationController;
use App\Http\Controllers\V1\OtpController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Authentication & Password Routes
|--------------------------------------------------------------------------
*/

Route::post('verifyEmail', [VerificationController::class, 'verifyEmail']);
Route::post('login', [LoginController::class, 'login']);
Route::post('forgotPassword', [PasswordManagementController::class, 'forgotPassword']);
Route::post('resetPassword', [PasswordManagementController::class, 'resetPassword']);
Route::post('resendCode', [OtpController::class, 'resendCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('changePassword', [PasswordManagementController::class, 'changePassword']);
    Route::post('refreshToken', [LoginController::class, 'refreshToken']);
    Route::post('logout', [LoginController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| Client Management Routes
|--------------------------------------------------------------------------
*/
Route::prefix('client')->group(function () {
    Route::get('', [ClientController::class, 'index']);
    Route::post('', [ClientController::class, 'store']);
    Route::put('', [ClientController::class, 'update']);
    Route::delete('{id}', [ClientController::class, 'destroy']);
    Route::get('profile', [ClientController::class, 'profile']);

    // Client Branch read routes
    Route::get('branch', [BranchController::class, 'readForClient']);
    Route::get('branch/{id}', [BranchController::class, 'showForClient']);
});

/*
|--------------------------------------------------------------------------
| Core System Routes (Branches, Employees)
|--------------------------------------------------------------------------
*/
Route::prefix('branch')->group(function () {
    Route::get('', [BranchController::class, 'index']);
    Route::get('{id}', [BranchController::class, 'show']);
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

/*
|--------------------------------------------------------------------------
| Categories Routes
|--------------------------------------------------------------------------
*/
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Complaints Routes (Public & Dynamic Lifecycle)
|--------------------------------------------------------------------------
*/
Route::prefix('complaint')->group(function () {

    Route::post('/', [ComplaintController::class, 'store']);

    Route::get('/track/{token}', [ComplaintController::class, 'track']);
    Route::post('/track/{token}/reply', [ComplaintController::class, 'clientReply']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/my-complaints', [ComplaintController::class, 'myComplaints']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [ComplaintController::class, 'index']);
        Route::get('/branch/{branchId}', [ComplaintController::class, 'branchComplaints']);
        Route::get('/{id}', [ComplaintController::class, 'show']);

        Route::put('/{id}/status', [ComplaintController::class, 'changeStatus']);

        Route::post('/{id}/actions', [ComplaintController::class, 'employeeAction']);
    });
});

/*
|--------------------------------------------------------------------------
| System & Helpers
|--------------------------------------------------------------------------
*/
Route::get('location', [LocationController::class, 'index']);

Route::get('/run-seeder', function () {
    Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);
    return 'Database has been refreshed and seeded!';
});
