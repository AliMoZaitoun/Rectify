<?php

use App\Http\Controllers\V1\AI\AiPolicyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\PasswordManagementController;
use App\Http\Controllers\V1\Auth\VerificationController;
use App\Http\Controllers\V1\Core\BranchController;
use App\Http\Controllers\V1\ClientController;
use App\Http\Controllers\V1\Complaint\CategoryController;
use App\Http\Controllers\V1\Complaint\AppComplaintController;
use App\Http\Controllers\V1\Complaint\Compensation\CompensationController;
use App\Http\Controllers\V1\Complaint\ComplaintReportController;
use App\Http\Controllers\V1\Complaint\DashboardComplaintController;
use App\Http\Controllers\V1\Core\EmployeeController;
use App\Http\Controllers\V1\Core\EmployeeBranchController;
use App\Http\Controllers\V1\DeviceTokenController;
use App\Http\Controllers\V1\LocationController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\OtpController;
use App\Http\Controllers\V1\PermissionController;
use App\Http\Controllers\V1\RoleController;
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

Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('', [NotificationController::class, 'index']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
});

Route::get('testNot/{client_id}', [NotificationController::class, 'test']);

/*
|--------------------------------------------------------------------------
| Client Management Routes
|--------------------------------------------------------------------------
*/
Route::prefix('client')->group(function () {
    Route::get('', [ClientController::class, 'index']);
    Route::post('', [ClientController::class, 'store']);
    Route::put('{id}', [ClientController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('{id}', [ClientController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('profile', [ClientController::class, 'profile'])->middleware('auth:sanctum');

    // Client Branch read routes
    Route::get('branch', [BranchController::class, 'readForClient']);
    Route::get('branch/{id}', [BranchController::class, 'showForClient']);
});

/*
|--------------------------------------------------------------------------
| Core System Routes (Branches, Employees, Roles)
|--------------------------------------------------------------------------
*/
Route::prefix('branch')->group(function () {
    Route::get('', [BranchController::class, 'index']);
    Route::get('{id}', [BranchController::class, 'show']);
    Route::post('', [BranchController::class, 'store'])->middleware(['auth:sanctum', 'permission:create.branch']);
    Route::put('/{id}', [BranchController::class, 'update'])->middleware(['auth:sanctum', 'permission:update.branch']);
    Route::delete('/{id}', [BranchController::class, 'destroy'])->middleware(['auth:sanctum', 'permission:delete.branch']);
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

// Role
Route::prefix('role')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index'])
        ->middleware(['permission:read.role']);

    Route::post('/', [RoleController::class, 'store'])
        ->middleware(['permission:create.role']);

    Route::put('{id}', [RoleController::class, 'update'])
        ->middleware(['permission:update.role']);

    Route::post('assignRoles/{user_id}', [RoleController::class, 'assignRoles'])
        ->middleware(['permission:create.role']);

    # Search for role by id or name #
    Route::get('{id}', [RoleController::class, 'show'])
        ->middleware(['permission:read.role']);

    Route::get('name/{role_name}', [RoleController::class, 'showByName'])
        ->middleware(['permission:read.role']);

    # Manage Permissions for a role #
    Route::post('selectPermission/{role_id}', [RoleController::class, 'selectPermission'])
        ->middleware(['permission:update.role']);

    # Delete a role #
    Route::delete('{id}', [RoleController::class, 'destroy'])
        ->middleware(['permission:delete.role']);
});

Route::prefix('permission')->middleware('auth:sanctum')->group(function () {
    Route::get('', [PermissionController::class, 'index']);
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
| App / Client Complaints Routes
|--------------------------------------------------------------------------
*/
Route::prefix('complaint')->group(function () {
    Route::post('/', [AppComplaintController::class, 'store']);

    Route::get('/my-complaints', [AppComplaintController::class, 'myComplaints']);

    Route::get('/my-compensations', [AppComplaintController::class, 'myCompensations'])->middleware('auth:sanctum');

    Route::post('/track/{code}/reply', [AppComplaintController::class, 'clientReply']);

    Route::get('/track/{code}', [AppComplaintController::class, 'track']);

    Route::post('sync-device', [AppComplaintController::class, 'syncDeviceComplaints'])->middleware('auth:sanctum');

    Route::post('{code}/sync-device', [AppComplaintController::class, 'syncDeviceComplaint'])->middleware('auth:sanctum');

    # New
    Route::post('{code}/rate', [AppComplaintController::class, 'rate']);

    Route::post('{code}/reopen', [AppComplaintController::class, 'reopen']);
});

/*
|--------------------------------------------------------------------------
| Dashboard / Employee Complaints Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/complaint')->middleware(['auth:sanctum'])->group(function () {
    Route::get('reports', [ComplaintReportController::class, 'index']);

    Route::get('/', [DashboardComplaintController::class, 'index']);
    Route::get('/branch/{branchId}', [DashboardComplaintController::class, 'branchComplaints']);
    Route::get('/{id}', [DashboardComplaintController::class, 'show']);

    Route::put('/{id}/status', [DashboardComplaintController::class, 'changeStatus']);
    Route::post('/{id}/actions', [DashboardComplaintController::class, 'employeeAction']);
    Route::post('{id}/merge', [DashboardComplaintController::class, 'mergeComplaints']);
    Route::post('{id}/unmerge', [DashboardComplaintController::class, 'unmergeComplaint']);
});

Route::prefix('dashboard/compensations')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CompensationController::class, 'index']);
    Route::post('/complaint/{complaintId}', [CompensationController::class, 'store']);
    Route::get('/complaint/{complaintId}', [CompensationController::class, 'showByComplaint']);
    Route::put('/{id}/status', [CompensationController::class, 'updateStatus']);
});

Route::prefix('settings/ai-policy')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [AiPolicyController::class, 'show']);
    Route::put('/', [AiPolicyController::class, 'update']);
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


Route::get('/cron/escalate-complaints', function () {
    app(\App\Jobs\EscalateOverdueComplaintsJob::class)->handle(
        app(\App\DAO\Complaint\ComplaintDAO::class),
        app(\App\DAO\Complaint\ComplaintHistoryDAO::class)
    );
    return response()->json(['status' => 'success']);
});
