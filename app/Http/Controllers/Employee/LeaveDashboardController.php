<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveAllocation;
use App\Models\Attendance;
use App\Services\LeaveBalanceService;
use App\Services\LeaveDashboardService;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class LeaveDashboardController extends Controller
{
    public function __construct(
        private LeaveBalanceService $balanceService,
        private LeaveDashboardService $dashboardService,
    ) {}

    public function calendar(Request $request): View
    {
        $user = $request->user();
        $year = (int) ($request->get('year', now()->year));
        $month = (int) ($request->get('month', now()->month));

        $events = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orderBy('start_date')
            ->get();

        $daysInMonth = now()->setYear($year)->setMonth($month)->daysInMonth;

        return view('erp.employee.calendar', compact('events', 'year', 'month', 'daysInMonth'));
    }

    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $year = (int) ($request->get('year', now()->year));

        $leaveTypes = LeaveType::query()->where('active', true)->get();
        $balances = collect();

        foreach ($leaveTypes as $type) {
            $allocation = $this->balanceService->getAllocation($user, $type, $year);
            $remaining = $this->balanceService->getRemaining($user, $type, $year);

            $balances->push((object) [
                'type' => $type,
                'remaining' => $remaining,
                'allocated' => ($allocation?->total_allocated_days ?? $allocation?->allocated_days ?? 0) + ($allocation?->carried_over_days ?? 0),
                'used' => $allocation?->used_days ?? 0,
            ]);
        }

        $recentRequests = LeaveRequest::query()
            ->with('leaveType')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $calendarData = $this->dashboardService->getYearCalendarData($user, $year);
        $overviewStats = $this->dashboardService->getOverviewStats($user, $year);

        // 1. Weekly Attendance (Bar Chart)
        $startOfWeek = now()->startOfWeek();
        if (!$user->employee) {
            return view('erp.employee.profile-incomplete');
        }
        $attendanceData = Attendance::where('employee_id', $user->employee->id)
            ->whereBetween('check_in', [$startOfWeek, now()->endOfWeek()])
            ->selectRaw('COUNT(*) as count, ' . DatabaseHelper::dayOfWeek('check_in'))
            ->groupBy('day')
            ->get()
            ->pluck('count', 'day');
        
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $chartAttendance = [];
        for ($i = 1; $i <= 7; $i++) {
            $dayIndex = $i % 7; // Sunday is 0 in strftime %w
            $chartAttendance[] = $attendanceData[$dayIndex] ?? 0;
        }

        $years = range(now()->year - 2, now()->year + 2);
        $hoursPerDay = $this->balanceService->getHoursPerDay();

        return view('erp.employee.dashboard', compact(
            'leaveTypes',
            'balances',
            'recentRequests',
            'calendarData',
            'overviewStats',
            'year',
            'years',
            'hoursPerDay',
            'weekDays',
            'chartAttendance'
        ));
    }
}
