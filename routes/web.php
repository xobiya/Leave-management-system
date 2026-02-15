<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Employee\LeaveRequestController as EmployeeLeaveRequestController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeavePreviewController as EmployeeLeavePreviewController;
use App\Http\Controllers\Manager\ApprovalController as ManagerApprovalController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\AllocationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::prefix('employee')->name('employee.')->middleware('role:employee|manager|admin|hr')->group(function () {
        Route::get('/', EmployeeDashboardController::class)->name('dashboard');
        Route::get('/requests', [EmployeeLeaveRequestController::class, 'index'])->name('requests');
        Route::post('/requests', [EmployeeLeaveRequestController::class, 'store'])->name('requests.store');
        Route::get('/requests/preview', EmployeeLeavePreviewController::class)->name('requests.preview');
        Route::view('/calendar', 'erp.employee.calendar')->name('calendar');
        Route::view('/notifications', 'erp.employee.notifications')->name('notifications');
    });

    Route::prefix('manager')->name('manager.')->middleware('role:manager|admin|hr')->group(function () {
        Route::get('/', [ManagerApprovalController::class, 'index'])->name('dashboard');
        Route::view('/team', 'erp.manager.team')->name('team');
        Route::view('/calendar', 'erp.manager.calendar')->name('calendar');
        Route::view('/reports', 'erp.manager.reports')->name('reports');
        Route::post('/approvals/{leaveRequest}/manager', [ManagerApprovalController::class, 'approveManager'])->name('approvals.manager');
        Route::post('/approvals/{leaveRequest}/hr', [ManagerApprovalController::class, 'approveHr'])->name('approvals.hr');
        Route::post('/approvals/{leaveRequest}/reject', [ManagerApprovalController::class, 'reject'])->name('approvals.reject');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::view('/', 'erp.admin.dashboard')->name('dashboard');
        Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('leave-types');
        Route::post('/leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
        Route::get('/allocations', [AllocationController::class, 'index'])->name('allocations');
        Route::post('/allocations', [AllocationController::class, 'store'])->name('allocations.store');
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users/{user}/role', [UserController::class, 'assignRole'])->name('users.assign-role');
        Route::view('/settings', 'erp.admin.settings')->name('settings');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
