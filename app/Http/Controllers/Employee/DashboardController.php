<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $leaveTypes = LeaveType::query()->where('active', '=', true)->get();
        $recentRequests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->take(5)
            ->get();

        $hoursPerDay = app(LeaveBalanceService::class)->getHoursPerDay();

        return view('erp.employee.dashboard', compact('leaveTypes', 'recentRequests', 'hoursPerDay'));
    }
}
