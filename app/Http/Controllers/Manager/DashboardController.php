<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Task;
use App\Models\Employee;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = auth()->user();
        $reportIds = $manager->directReports()->pluck('id');
        $employeeIds = \App\Models\Employee::whereIn('user_id', $reportIds)->pluck('id');

        // 1. Team Summary
        $teamCount = $reportIds->count();
        $pendingApprovals = LeaveRequest::whereIn('user_id', $reportIds)
            ->where('status', 'pending')
            ->count();
        
        $teamAttendanceToday = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('check_in', today())
            ->count();

        // 2. Team Capacity Chart (Doughnut)
        $onLeaveToday = LeaveRequest::whereIn('user_id', $reportIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        
        $availableToday = $teamCount - $onLeaveToday;

        // 3. Team Productivity (Weekly Tasks - Bar Chart)
        $tasksCompleted = Task::whereIn('assigned_to', $reportIds)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->startOfWeek())
            ->selectRaw('COUNT(*) as count, ' . DatabaseHelper::dayOfWeek('updated_at'))
            ->groupBy('day')
            ->get()
            ->pluck('count', 'day');
        
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $chartTasks = [];
        for ($i = 1; $i <= 7; $i++) {
            $dayIndex = $i % 7;
            $chartTasks[] = $tasksCompleted[$dayIndex] ?? 0;
        }

        // 4. Recent Team Activity
        $recentLeaves = LeaveRequest::with('user', 'leaveType')
            ->whereIn('user_id', $reportIds)
            ->latest()
            ->take(5)
            ->get();

        return view('erp.manager.dashboard', compact(
            'teamCount',
            'pendingApprovals',
            'teamAttendanceToday',
            'onLeaveToday',
            'availableToday',
            'chartTasks',
            'weekDays',
            'recentLeaves'
        ));
    }

    public function team()
    {
        $manager = auth()->user();
        $reportIds = $manager->directReports()->pluck('id');
        $teamMembers = User::with('employee.department', 'leaveAllocations.leaveType')
            ->whereIn('id', $reportIds)
            ->get()
            ->map(function ($member) {
                $nextLeave = LeaveRequest::with('leaveType')
                    ->where('user_id', $member->id)
                    ->whereIn('status', ['approved', 'submitted'])
                    ->whereDate('start_date', '>=', now())
                    ->orderBy('start_date')
                    ->first();
                $totalBalance = $member->leaveAllocations
                    ->where('year', now()->year)
                    ->sum(fn ($a) => ($a->total_allocated_days ?? $a->allocated_days ?? 0) + ($a->carried_over_days ?? 0) - ($a->used_days ?? 0));
                $member->next_leave = $nextLeave;
                $member->total_balance = $totalBalance;
                return $member;
            });

        return view('erp.manager.team', compact('teamMembers'));
    }

    public function calendar()
    {
        $manager = auth()->user();
        $reportIds = $manager->directReports()->pluck('id');
        $year = request()->get('year', now()->year);
        $month = request()->get('month', now()->month);

        $events = LeaveRequest::with('user', 'leaveType')
            ->whereIn('user_id', $reportIds)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->whereIn('status', ['approved', 'submitted', 'manager_approved'])
            ->orderBy('start_date')
            ->get();

        $onLeaveToday = $events->filter(fn ($e) => $e->start_date <= today() && $e->end_date >= today())->count();
        $daysInMonth = now()->setYear($year)->setMonth($month)->daysInMonth;

        return view('erp.manager.calendar', compact('events', 'year', 'month', 'daysInMonth', 'onLeaveToday'));
    }

    public function reports()
    {
        $manager = auth()->user();
        $reportIds = $manager->directReports()->pluck('id');

        $topType = LeaveRequest::with('leaveType')
            ->whereIn('user_id', $reportIds)
            ->select('leave_type_id', DB::raw('COUNT(*) as total'))
            ->groupBy('leave_type_id')
            ->orderByDesc('total')
            ->first();

        $avgDuration = LeaveRequest::whereIn('user_id', $reportIds)
            ->whereIn('status', ['approved', 'manager_approved'])
            ->avg('days');

        $requests = LeaveRequest::whereIn('user_id', $reportIds)
            ->whereIn('status', ['approved', 'manager_approved'])
            ->count();

        $rejected = LeaveRequest::whereIn('user_id', $reportIds)
            ->where('status', 'rejected')
            ->count();

        $departmentBreakdown = DB::table('leave_requests')
            ->join('users', 'leave_requests.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->whereIn('leave_requests.user_id', $reportIds)
            ->select(
                'departments.name as department',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw("SUM(CASE WHEN leave_requests.status IN ('approved','manager_approved') THEN 1 ELSE 0 END) as approved"),
                DB::raw("SUM(CASE WHEN leave_requests.status = 'rejected' THEN 1 ELSE 0 END) as rejected"),
                DB::raw('AVG(leave_requests.days) as avg_days')
            )
            ->groupBy('departments.name')
            ->get();

        return view('erp.manager.reports', compact(
            'topType', 'avgDuration', 'requests', 'rejected', 'departmentBreakdown'
        ));
    }
}
