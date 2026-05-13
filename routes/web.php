<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Database Maintenance (Moved outside auth for initial setup) ────────────
Route::get('/system/migrate', function() {
    if (request('token') !== config('app.secret_token', env('APP_SECRET', 'change-me'))) abort(403);
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migration successful: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
})->name('system.migrate');

Route::get('/system/seed', function() {
    if (request('token') !== config('app.secret_token', env('APP_SECRET', 'change-me'))) abort(403);
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return "Seeding successful: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Seeding failed: " . $e->getMessage();
    }
})->name('system.seed');

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Global Search ─────────────────────────────────────────────────────
    Route::get('/search', \App\Http\Controllers\SearchController::class)->name('global.search');

    // ── Dashboard (role-aware) ────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Notifications ─────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{id}/read',     [NotificationController::class, 'markRead'])->name('mark-read');
    });

    // ── Companies & Branches (Phase 1) ────────────────────────────────────
    Route::resource('companies', CompanyController::class);
    Route::resource('companies.branches', BranchController::class)->shallow()->only(['store', 'update', 'destroy']);

    // ── Profile ────────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Phase 2 — HR & Employees ────────────────────────────────────────────
    Route::get('employees/org-chart', [\App\Http\Controllers\EmployeeController::class, 'orgChart'])->name('employees.org-chart');
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::post('employees/{employee}/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('employees.contracts.store');
    Route::delete('employees/{employee}/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'destroy'])->name('employees.contracts.destroy');
    Route::post('employees/{employee}/documents', [\App\Http\Controllers\EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
    Route::delete('employees/{employee}/documents/{document}', [\App\Http\Controllers\EmployeeDocumentController::class, 'destroy'])->name('employees.documents.destroy');

    // ── Positions ───────────────────────────────────────────────────────────
    Route::resource('positions', \App\Http\Controllers\PositionController::class);

    // ── Employee Leave ──────────────────────────────────────────────────────
    Route::get('/leave-requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/leave-requests/create', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'store'])->name('employee.requests.store');
    Route::get('/leave-requests/preview', [\App\Http\Controllers\Employee\LeavePreviewController::class, '__invoke'])->name('employee.requests.preview');

    // ── Employee Leave Dashboard (Odoo-style) ────────────────────────────────
    Route::get('/employee/leave-dashboard', [\App\Http\Controllers\Employee\LeaveDashboardController::class, 'index'])->name('employee.dashboard');

    // ── Manager Approval ────────────────────────────────────────────────────
    Route::get('/manager', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('manager.dashboard');
    Route::post('/manager/approvals/{leaveRequest}/approve-manager', [\App\Http\Controllers\Manager\ApprovalController::class, 'approveManager'])->name('manager.approvals.manager');
    Route::post('/manager/approvals/{leaveRequest}/approve-hr', [\App\Http\Controllers\Manager\ApprovalController::class, 'approveHr'])->name('manager.approvals.hr');
    Route::post('/manager/approvals/{leaveRequest}/reject', [\App\Http\Controllers\Manager\ApprovalController::class, 'reject'])->name('manager.approvals.reject');

    // ── Manager Reports ──────────────────────────────────────────────────────
    Route::get('/manager/reports', [\App\Http\Controllers\Manager\DashboardController::class, 'reports'])->name('manager.reports');
    Route::prefix('manager/reports')->name('manager.reports.')->group(function () {
        Route::get('/by-employee', [\App\Http\Controllers\Manager\ReportController::class, 'byEmployee'])->name('by-employee');
        Route::get('/summary', [\App\Http\Controllers\Manager\ReportController::class, 'summary'])->name('summary');
        Route::get('/balance', [\App\Http\Controllers\Manager\ReportController::class, 'balanceReport'])->name('balance');
    });

    // ── Manager Sidebar ─────────────────────────────────────────────────────
    Route::get('/manager/team', [\App\Http\Controllers\Manager\DashboardController::class, 'team'])->name('manager.team');
    Route::get('/manager/calendar', [\App\Http\Controllers\Manager\DashboardController::class, 'calendar'])->name('manager.calendar');

    // ── Employee Sidebar ────────────────────────────────────────────────────
    Route::get('/employee/requests', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('employee.requests');
    Route::get('/employee/calendar', [\App\Http\Controllers\Employee\LeaveDashboardController::class, 'calendar'])->name('employee.calendar');

    // ── Admin / HR Management ──────────────────────────────────────────────
    Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/leave-types', [\App\Http\Controllers\Admin\LeaveTypeController::class, 'index'])->name('admin.leave-types');
    Route::post('/admin/leave-types', [\App\Http\Controllers\Admin\LeaveTypeController::class, 'store'])->name('admin.leave-types.store');
    Route::get('/admin/leave-policies', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'index'])->name('admin.leave-policies');
    Route::post('/admin/leave-policies', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'store'])->name('admin.leave-policies.store');
    Route::post('/admin/leave-policies/{leavePolicy}/activate', [\App\Http\Controllers\Admin\LeavePolicyController::class, 'activate'])->name('admin.leave-policies.activate');
    Route::get('/admin/allocations', [\App\Http\Controllers\Admin\AllocationController::class, 'index'])->name('admin.allocations');
    Route::post('/admin/allocations', [\App\Http\Controllers\Admin\AllocationController::class, 'store'])->name('admin.allocations.store');
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/assign-role', [\App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('admin.users.assign-role');
    
    // Roles & Permissions
    Route::get('/admin/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'editPermissions'])->name('admin.roles.permissions');
    Route::post('/admin/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update');

    Route::get('/admin/settings', fn() => view('erp.admin.settings'))->name('admin.settings');
    Route::get('/admin/react-dashboard', fn() => view('erp.admin.react-dashboard'))->name('admin.react-dashboard');

    // ── Admin Accrual Plans ──────────────────────────────────────────────────
    Route::get('/admin/accrual-plans', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'index'])->name('admin.accrual-plans');
    Route::post('/admin/accrual-plans', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'store'])->name('admin.accrual-plans.store');
    Route::delete('/admin/accrual-plans/{accrualPlan}', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'destroy'])->name('admin.accrual-plans.destroy');
    Route::post('/admin/accrual-plans/{accrualPlan}/levels', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'storeLevel'])->name('admin.accrual-levels.store');
    Route::delete('/admin/accrual-plans/{accrualPlan}/levels/{level}', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'destroyLevel'])->name('admin.accrual-levels.destroy');
    Route::post('/admin/accrual-plans/run', [\App\Http\Controllers\Admin\AccrualPlanController::class, 'runAccruals'])->name('admin.accrual-plans.run');

    // ── HR Dashboard ────────────────────────────────────────────────────────
    Route::get('/hr/dashboard', [\App\Http\Controllers\Hr\DashboardController::class, 'index'])->name('hr.dashboard');

    // ── Placeholder routes for future phases ────────────────────────────────
    // ── Phase 3 — Attendance ──────────────────────────────────────────────
    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/my-attendance', [\App\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('attendance.my');
    Route::post('/attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{attendance}', [\App\Http\Controllers\AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/export-csv', [\App\Http\Controllers\AttendanceController::class, 'exportCsv'])->name('attendance.export-csv');
    Route::get('/attendance/monthly', [\App\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('attendance.monthly');
    // ── Phase 4 — Payroll ────────────────────────────────────────────────
    Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/my-payslips', [\App\Http\Controllers\PayrollController::class, 'myPayslips'])->name('payroll.my');
    Route::get('/payroll/{payroll}', [\App\Http\Controllers\PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/generate', [\App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
    // ── Phase 5 — Asset Management ───────────────────────────────────────
    Route::resource('assets', \App\Http\Controllers\AssetController::class);
    Route::get('/my-assets', [\App\Http\Controllers\AssetController::class, 'myAssets'])->name('assets.my');
    Route::post('/assets/{asset}/assign', [\App\Http\Controllers\AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/return', [\App\Http\Controllers\AssetController::class, 'return'])->name('assets.return');
    // ── Phase 6 — Inventory Management ───────────────────────────────────
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
    Route::post('/inventory/adjust', [\App\Http\Controllers\InventoryController::class, 'adjustStock'])->name('inventory.adjust');
    // ── Phase 7 — Procurement ────────────────────────────────────────────
    Route::resource('procurement', \App\Http\Controllers\ProcurementController::class);
    Route::post('/procurement/{order}/receive', [\App\Http\Controllers\ProcurementController::class, 'receive'])->name('procurement.receive');
    // ── Phase 8 — CRM ───────────────────────────────────────────────────
    Route::get('/crm', [\App\Http\Controllers\CrmController::class, 'index'])->name('crm.index');
    Route::get('/crm/pipeline', [\App\Http\Controllers\CrmController::class, 'pipeline'])->name('crm.pipeline');
    Route::post('/crm/customers', [\App\Http\Controllers\CrmController::class, 'storeCustomer'])->name('crm.customers.store');
    Route::post('/crm/opportunities', [\App\Http\Controllers\CrmController::class, 'storeOpportunity'])->name('crm.opportunities.store');
    Route::get('/crm/opportunities/{opportunity}', [\App\Http\Controllers\CrmController::class, 'showOpportunity'])->name('crm.opportunities.show');
    Route::post('/crm/opportunities/{opportunity}/stage', [\App\Http\Controllers\CrmController::class, 'updateStage'])->name('crm.opportunities.stage');
    Route::post('/crm/opportunities/{opportunity}/activity', [\App\Http\Controllers\CrmController::class, 'logActivity'])->name('crm.opportunities.activity');
    Route::post('/crm/opportunities/{opportunity}/convert', [\App\Http\Controllers\CrmController::class, 'convertToQuotation'])->name('crm.convert');
    // ── Phase 9 — Sales ─────────────────────────────────────────────────
    Route::resource('sales', \App\Http\Controllers\SalesController::class);
    Route::post('/sales/{order}/confirm', [\App\Http\Controllers\SalesController::class, 'confirm'])->name('sales.confirm');
    // ── Phase 10 — Accounting ───────────────────────────────────────────
    Route::get('/accounting', [\App\Http\Controllers\AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/accounting/coa', [\App\Http\Controllers\AccountingController::class, 'coa'])->name('accounting.coa');
    Route::get('/accounting/journals', [\App\Http\Controllers\AccountingController::class, 'journals'])->name('accounting.journals');
    Route::get('/accounting/reports/pnl', [\App\Http\Controllers\AccountingController::class, 'profitAndLoss'])->name('accounting.reports.pnl');
    Route::post('/accounting/accounts', [\App\Http\Controllers\AccountingController::class, 'storeAccount'])->name('accounting.accounts.store');
    
    // ── Phase 11 — Manufacturing ────────────────────────────────────────
    Route::resource('manufacturing', \App\Http\Controllers\ManufacturingController::class);
    Route::get('/manufacturing/boms', [\App\Http\Controllers\ManufacturingController::class, 'boms'])->name('manufacturing.boms');
    Route::post('/manufacturing/{order}/complete', [\App\Http\Controllers\ManufacturingController::class, 'complete'])->name('manufacturing.complete');
    // ── Phase 12 — Projects ─────────────────────────────────────────────
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::post('/projects/{project}/tasks', [\App\Http\Controllers\ProjectController::class, 'storeTask'])->name('projects.tasks.store');
    Route::post('/tasks/{task}/log-time', [\App\Http\Controllers\ProjectController::class, 'logTime'])->name('tasks.log-time');
    Route::post('/tasks/{task}/status', [\App\Http\Controllers\ProjectController::class, 'updateTaskStatus'])->name('tasks.status');

    // ── Phase 13 — Helpdesk / Support ───────────────────────────────────
    Route::get('/helpdesk', [\App\Http\Controllers\HelpdeskController::class, 'index'])->name('helpdesk.index');
    Route::post('/helpdesk', [\App\Http\Controllers\HelpdeskController::class, 'store'])->name('helpdesk.store');
    Route::get('/helpdesk/{helpdeskTicket}', [\App\Http\Controllers\HelpdeskController::class, 'show'])->name('helpdesk.show');
    Route::post('/helpdesk/{helpdeskTicket}/assign', [\App\Http\Controllers\HelpdeskController::class, 'assign'])->name('helpdesk.assign');
    Route::post('/helpdesk/{helpdeskTicket}/status', [\App\Http\Controllers\HelpdeskController::class, 'status'])->name('helpdesk.status');
    Route::post('/helpdesk/{helpdeskTicket}/respond', [\App\Http\Controllers\HelpdeskController::class, 'respond'])->name('helpdesk.respond');

    // ── Skills & Resume ─────────────────────────────────────────────────
    Route::get('/skills', [\App\Http\Controllers\SkillsController::class, 'index'])->name('skills.index');
    Route::post('/skills/types', [\App\Http\Controllers\SkillsController::class, 'storeSkillType'])->name('skills.types.store');
    Route::post('/skills', [\App\Http\Controllers\SkillsController::class, 'storeSkill'])->name('skills.store');
    Route::get('/skills/employees', [\App\Http\Controllers\SkillsController::class, 'employeeSkills'])->name('skills.employees');
    Route::post('/skills/employee-skills', [\App\Http\Controllers\SkillsController::class, 'storeEmployeeSkill'])->name('skills.employee-skills.store');
    Route::delete('/skills/employee-skills/{employeeSkill}', [\App\Http\Controllers\SkillsController::class, 'destroyEmployeeSkill'])->name('skills.employee-skills.destroy');
    Route::get('/skills/resume-lines', [\App\Http\Controllers\SkillsController::class, 'resumeLines'])->name('skills.resume-lines');
    Route::post('/skills/resume-lines', [\App\Http\Controllers\SkillsController::class, 'storeResumeLine'])->name('skills.resume-lines.store');
    Route::delete('/skills/resume-lines/{resumeLine}', [\App\Http\Controllers\SkillsController::class, 'destroyResumeLine'])->name('skills.resume-lines.destroy');
    Route::delete('/skills/types/{skillType}', [\App\Http\Controllers\SkillsController::class, 'destroySkillType'])->name('skills.types.destroy');
    Route::delete('/skills/{skill}', [\App\Http\Controllers\SkillsController::class, 'destroySkill'])->name('skills.destroy');

    // ── Gamification ────────────────────────────────────────────────────
    Route::get('/gamification', [\App\Http\Controllers\GamificationController::class, 'index'])->name('gamification.index');
    Route::get('/gamification/badges', [\App\Http\Controllers\GamificationController::class, 'badges'])->name('gamification.badges');
    Route::get('/gamification/challenges', [\App\Http\Controllers\GamificationController::class, 'challenges'])->name('gamification.challenges');
    Route::get('/gamification/leaderboard', [\App\Http\Controllers\GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
    Route::post('/gamification/badges', [\App\Http\Controllers\GamificationController::class, 'storeBadge'])->name('gamification.badges.store');
    Route::post('/gamification/challenges', [\App\Http\Controllers\GamificationController::class, 'storeChallenge'])->name('gamification.challenges.store');
    Route::post('/gamification/badges/{badge}/assign', [\App\Http\Controllers\GamificationController::class, 'assignBadge'])->name('gamification.badges.assign');
    Route::post('/gamification/challenges/{challenge}/start', [\App\Http\Controllers\GamificationController::class, 'startChallenge'])->name('gamification.challenges.start');

    // ── Expenses ────────────────────────────────────────────────────────
    Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('/my-expenses', [\App\Http\Controllers\ExpenseController::class, 'myExpenses'])->name('expenses.my');
    Route::post('/expenses/{expense}/submit', [\App\Http\Controllers\ExpenseController::class, 'submit'])->name('expenses.submit');
    Route::post('/expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('/expenses/{expense}/reject', [\App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');

    // ── Inventory Enhancements ──────────────────────────────────────────
    Route::get('/inventory/lots', [\App\Http\Controllers\StockLotController::class, 'index'])->name('inventory.lots');
    Route::post('/inventory/lots', [\App\Http\Controllers\StockLotController::class, 'store'])->name('inventory.lots.store');
    Route::get('/inventory/batches', [\App\Http\Controllers\PickingBatchController::class, 'index'])->name('inventory.batches');
    Route::post('/inventory/batches', [\App\Http\Controllers\PickingBatchController::class, 'store'])->name('inventory.batches.store');
    Route::post('/inventory/batches/{pickingBatch}/complete', [\App\Http\Controllers\PickingBatchController::class, 'complete'])->name('inventory.batches.complete');
    Route::get('/inventory/landed-costs', [\App\Http\Controllers\LandedCostController::class, 'index'])->name('inventory.landed-costs');
    Route::post('/inventory/landed-costs', [\App\Http\Controllers\LandedCostController::class, 'store'])->name('inventory.landed-costs.store');
    Route::post('/inventory/landed-costs/{landedCost}/validate', [\App\Http\Controllers\LandedCostController::class, 'validateCost'])->name('inventory.landed-costs.validate');

    // ── Procurement Enhancements ────────────────────────────────────────
    Route::get('/procurement/agreements', [\App\Http\Controllers\PurchaseAgreementController::class, 'index'])->name('procurement.agreements');
    Route::post('/procurement/agreements', [\App\Http\Controllers\PurchaseAgreementController::class, 'store'])->name('procurement.agreements.store');
    Route::post('/procurement/agreements/{purchaseAgreement}/activate', [\App\Http\Controllers\PurchaseAgreementController::class, 'activate'])->name('procurement.agreements.activate');
    Route::post('/procurement/agreements/{purchaseAgreement}/close', [\App\Http\Controllers\PurchaseAgreementController::class, 'close'])->name('procurement.agreements.close');
    Route::get('/procurement/requisitions', [\App\Http\Controllers\PurchaseRequisitionController::class, 'index'])->name('procurement.requisitions');
    Route::post('/procurement/requisitions', [\App\Http\Controllers\PurchaseRequisitionController::class, 'store'])->name('procurement.requisitions.store');
    Route::post('/procurement/requisitions/{purchaseRequisition}/approve', [\App\Http\Controllers\PurchaseRequisitionController::class, 'approve'])->name('procurement.requisitions.approve');
    Route::post('/procurement/requisitions/{purchaseRequisition}/reject', [\App\Http\Controllers\PurchaseRequisitionController::class, 'reject'])->name('procurement.requisitions.reject');

    // ── Phase 14 — Recruitment / ATS ────────────────────────────────────
    Route::get('/recruitment', [\App\Http\Controllers\RecruitmentController::class, 'index'])->name('recruitment.index');
    Route::post('/recruitment', [\App\Http\Controllers\RecruitmentController::class, 'store'])->name('recruitment.store');
    Route::get('/recruitment/{jobPosition}', [\App\Http\Controllers\RecruitmentController::class, 'show'])->name('recruitment.show');
    Route::put('/recruitment/{jobPosition}', [\App\Http\Controllers\RecruitmentController::class, 'update'])->name('recruitment.update');
    Route::delete('/recruitment/{jobPosition}', [\App\Http\Controllers\RecruitmentController::class, 'destroy'])->name('recruitment.destroy');
    Route::get('/recruitment/{jobPosition}/edit', [\App\Http\Controllers\RecruitmentController::class, 'show'])->name('recruitment.edit');

    Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'index'])->name('recruitment.applications');
    Route::post('/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('recruitment.applications.store');
    Route::get('/applications/{jobApplication}', [\App\Http\Controllers\ApplicationController::class, 'show'])->name('recruitment.applications.show');
    Route::post('/applications/{jobApplication}/stage', [\App\Http\Controllers\ApplicationController::class, 'stage'])->name('recruitment.applications.stage');
    Route::post('/applications/{jobApplication}/rate', [\App\Http\Controllers\ApplicationController::class, 'rate'])->name('recruitment.applications.rate');
    Route::post('/applications/{jobApplication}/interview', [\App\Http\Controllers\ApplicationController::class, 'scheduleInterview'])->name('recruitment.applications.interview');
    Route::post('/interviews/{jobInterview}', [\App\Http\Controllers\ApplicationController::class, 'updateInterview'])->name('recruitment.interviews.update');

    // ── Fleet Management ─────────────────────────────────────────────────
    Route::get('/fleet', [\App\Http\Controllers\FleetController::class, 'index'])->name('fleet.index');
    Route::get('/fleet/create', [\App\Http\Controllers\FleetController::class, 'create'])->name('fleet.create');
    Route::post('/fleet', [\App\Http\Controllers\FleetController::class, 'store'])->name('fleet.store');
    Route::get('/fleet/{fleetVehicle}', [\App\Http\Controllers\FleetController::class, 'show'])->name('fleet.show');
    Route::get('/fleet/{fleetVehicle}/edit', [\App\Http\Controllers\FleetController::class, 'edit'])->name('fleet.edit');
    Route::put('/fleet/{fleetVehicle}', [\App\Http\Controllers\FleetController::class, 'update'])->name('fleet.update');
    Route::delete('/fleet/{fleetVehicle}', [\App\Http\Controllers\FleetController::class, 'destroy'])->name('fleet.destroy');
    Route::post('/fleet/contracts', [\App\Http\Controllers\FleetContractController::class, 'store'])->name('fleet.contracts.store');
    Route::delete('/fleet/contracts/{fleetContract}', [\App\Http\Controllers\FleetContractController::class, 'destroy'])->name('fleet.contracts.destroy');
    Route::post('/fleet/services', [\App\Http\Controllers\FleetServiceController::class, 'store'])->name('fleet.services.store');
    Route::delete('/fleet/services/{fleetServiceLog}', [\App\Http\Controllers\FleetServiceController::class, 'destroy'])->name('fleet.services.destroy');
    Route::post('/fleet/fuel', [\App\Http\Controllers\FleetFuelController::class, 'store'])->name('fleet.fuel.store');
    Route::delete('/fleet/fuel/{fleetFuelLog}', [\App\Http\Controllers\FleetFuelController::class, 'destroy'])->name('fleet.fuel.destroy');

    // ── Maintenance ──────────────────────────────────────────────────────
    Route::get('/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{maintenanceEquipment}', [\App\Http\Controllers\MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::get('/maintenance/{maintenanceEquipment}/edit', [\App\Http\Controllers\MaintenanceController::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{maintenanceEquipment}', [\App\Http\Controllers\MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{maintenanceEquipment}', [\App\Http\Controllers\MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/maintenance/requests', [\App\Http\Controllers\MaintenanceController::class, 'requests'])->name('maintenance.requests');
    Route::post('/maintenance/requests', [\App\Http\Controllers\MaintenanceController::class, 'storeRequest'])->name('maintenance.requests.store');
    Route::post('/maintenance/requests/{maintenanceRequest}/status', [\App\Http\Controllers\MaintenanceController::class, 'updateRequestStatus'])->name('maintenance.requests.status');

    // ── Lunch ────────────────────────────────────────────────────────────
    Route::get('/lunch', [\App\Http\Controllers\LunchController::class, 'index'])->name('lunch.index');
    Route::post('/lunch', [\App\Http\Controllers\LunchController::class, 'store'])->name('lunch.store');
    Route::get('/lunch/orders', [\App\Http\Controllers\LunchController::class, 'orders'])->name('lunch.orders');
    Route::post('/lunch/orders', [\App\Http\Controllers\LunchController::class, 'storeOrder'])->name('lunch.orders.store');
    Route::post('/lunch/orders/{lunchOrder}/status', [\App\Http\Controllers\LunchController::class, 'updateOrderStatus'])->name('lunch.orders.status');

    // ── Repair ──────────────────────────────────────────────────────────
    Route::get('/repair', [\App\Http\Controllers\RepairController::class, 'index'])->name('repair.index');
    Route::get('/repair/create', [\App\Http\Controllers\RepairController::class, 'create'])->name('repair.create');
    Route::post('/repair', [\App\Http\Controllers\RepairController::class, 'store'])->name('repair.store');
    Route::get('/repair/{repairOrder}', [\App\Http\Controllers\RepairController::class, 'show'])->name('repair.show');
    Route::post('/repair/{repairOrder}/status', [\App\Http\Controllers\RepairController::class, 'updateStatus'])->name('repair.status');
    Route::post('/repair/{repairOrder}/lines', [\App\Http\Controllers\RepairController::class, 'addLine'])->name('repair.lines.store');
    Route::delete('/repair/lines/{repairLine}', [\App\Http\Controllers\RepairController::class, 'destroyLine'])->name('repair.lines.destroy');

    // ── Accounting Enhancements ──────────────────────────────────────────
    Route::get('/accounting/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('accounting.invoices');
    Route::post('/accounting/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('accounting.invoices.store');
    Route::get('/accounting/invoices/{accountInvoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('accounting.invoices.show');
    Route::post('/accounting/invoices/{accountInvoice}/validate', [\App\Http\Controllers\InvoiceController::class, 'validateInvoice'])->name('accounting.invoices.validate');
    Route::post('/accounting/invoices/{accountInvoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'registerPayment'])->name('accounting.invoices.pay');
    Route::get('/accounting/taxes', [\App\Http\Controllers\TaxController::class, 'index'])->name('accounting.taxes');
    Route::post('/accounting/taxes', [\App\Http\Controllers\TaxController::class, 'store'])->name('accounting.taxes.store');
    Route::delete('/accounting/taxes/{tax}', [\App\Http\Controllers\TaxController::class, 'destroy'])->name('accounting.taxes.destroy');

    // ── Manufacturing Enhancements ───────────────────────────────────────
    Route::get('/manufacturing/work-centers', [\App\Http\Controllers\WorkCenterController::class, 'index'])->name('manufacturing.work-centers');
    Route::post('/manufacturing/work-centers', [\App\Http\Controllers\WorkCenterController::class, 'store'])->name('manufacturing.work-centers.store');
    Route::get('/manufacturing/routings', [\App\Http\Controllers\RoutingController::class, 'index'])->name('manufacturing.routings');
    Route::post('/manufacturing/routings', [\App\Http\Controllers\RoutingController::class, 'store'])->name('manufacturing.routings.store');
    Route::post('/manufacturing/routings/{routing}/steps', [\App\Http\Controllers\RoutingController::class, 'addStep'])->name('manufacturing.routings.steps.store');

    // ── Projects Enhancements ────────────────────────────────────────────
    Route::post('/projects/{project}/milestones', [\App\Http\Controllers\MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::post('/milestones/{milestone}/progress', [\App\Http\Controllers\MilestoneController::class, 'updateProgress'])->name('milestones.progress');

    // ── Payroll Enhancements ─────────────────────────────────────────────
    Route::get('/payroll/runs', [\App\Http\Controllers\PayrollRunController::class, 'index'])->name('payroll.runs');
    Route::post('/payroll/runs', [\App\Http\Controllers\PayrollRunController::class, 'store'])->name('payroll.runs.store');
    Route::post('/payroll/runs/{payrollRun}/generate', [\App\Http\Controllers\PayrollRunController::class, 'generatePayslips'])->name('payroll.runs.generate');
    Route::post('/payroll/runs/{payrollRun}/approve', [\App\Http\Controllers\PayrollRunController::class, 'approve'])->name('payroll.runs.approve');
    Route::post('/payroll/runs/{payrollRun}/post', [\App\Http\Controllers\PayrollRunController::class, 'post'])->name('payroll.runs.post');

    // ── Asset Enhancements ───────────────────────────────────────────────
    Route::get('/assets/{asset}/depreciation', [\App\Http\Controllers\AssetEnhanceController::class, 'depreciation'])->name('assets.depreciation');
    Route::post('/assets/{asset}/depreciation', [\App\Http\Controllers\AssetEnhanceController::class, 'storeDepreciation'])->name('assets.depreciation.store');
    Route::get('/assets-maintenance', [\App\Http\Controllers\AssetEnhanceController::class, 'maintenance'])->name('assets.maintenance');
    Route::post('/assets-maintenance', [\App\Http\Controllers\AssetEnhanceController::class, 'storeMaintenance'])->name('assets.maintenance.store');
    Route::post('/assets-maintenance/{assetMaintenance}/complete', [\App\Http\Controllers\AssetEnhanceController::class, 'completeMaintenance'])->name('assets.maintenance.complete');

    // ── CRM Enhancements ─────────────────────────────────────────────────
    Route::get('/crm/stages', [\App\Http\Controllers\CrmStageController::class, 'index'])->name('crm.stages');
    Route::post('/crm/stages', [\App\Http\Controllers\CrmStageController::class, 'store'])->name('crm.stages.store');
    Route::delete('/crm/stages/{crmStage}', [\App\Http\Controllers\CrmStageController::class, 'destroy'])->name('crm.stages.destroy');
    Route::get('/crm/teams', [\App\Http\Controllers\CrmTeamController::class, 'index'])->name('crm.teams');
    Route::post('/crm/teams', [\App\Http\Controllers\CrmTeamController::class, 'store'])->name('crm.teams.store');
    Route::delete('/crm/teams/{crmTeam}', [\App\Http\Controllers\CrmTeamController::class, 'destroy'])->name('crm.teams.destroy');

    // ── Reports ─────────────────────────────────────────────────────────
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/payroll-summary', [\App\Http\Controllers\ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
    Route::get('/reports/attendance-summary', [\App\Http\Controllers\ReportController::class, 'attendanceSummary'])->name('reports.attendance-summary');
    Route::get('/reports/accounting', [\App\Http\Controllers\ReportController::class, 'accountingReports'])->name('reports.accounting');

    // ── Notifications ───────────────────────────────────────────────────
    Route::get('/employee/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('employee.notifications');
});

require __DIR__ . '/auth.php';
