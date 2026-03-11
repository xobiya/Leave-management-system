<?php

use App\Http\Controllers\Api\V1\Admin\LeavePolicyController;
use App\Http\Controllers\Api\V1\Admin\LeaveTypeController;
use App\Http\Controllers\Api\V1\ApprovalController;
use App\Http\Controllers\Api\V1\BalanceController;
use App\Http\Controllers\Api\V1\LeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth')->group(function () {
    Route::prefix('leave')->middleware('role:employee|manager|admin|hr')->group(function () {
        Route::get('/requests', [LeaveRequestController::class, 'index']);
        Route::post('/requests', [LeaveRequestController::class, 'store']);
        Route::post('/requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel']);

        Route::get('/balances', [BalanceController::class, 'balances']);
        Route::get('/history', [BalanceController::class, 'history']);
    });

    Route::prefix('leave')->middleware('role:manager|admin|hr')->group(function () {
        Route::post('/requests/{leaveRequest}/approve/manager', [ApprovalController::class, 'managerApprove']);
        Route::post('/requests/{leaveRequest}/approve/hr', [ApprovalController::class, 'hrApprove']);
        Route::post('/requests/{leaveRequest}/reject', [ApprovalController::class, 'reject']);
    });

    Route::prefix('leave')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::get('/types', [LeaveTypeController::class, 'index']);
            Route::post('/types', [LeaveTypeController::class, 'store']);
            Route::put('/types/{leaveType}', [LeaveTypeController::class, 'update']);

            Route::get('/policies', [LeavePolicyController::class, 'index']);
            Route::post('/policies', [LeavePolicyController::class, 'store']);
            Route::put('/policies/{leavePolicy}/activate', [LeavePolicyController::class, 'activate']);
        });
    });
});
