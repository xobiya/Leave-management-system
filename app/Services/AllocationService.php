<?php

namespace App\Services;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Collection;

class AllocationService
{
    public function allocate(User $user, LeaveType $leaveType, int $year, float $days, ?string $notes = null): LeaveAllocation
    {
        return LeaveAllocation::updateOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ],
            [
                'allocated_days' => $days,
                'total_allocated_days' => $days,
                'notes' => $notes,
            ]
        );
    }

    public function bulkAllocate(Collection $users, LeaveType $leaveType, int $year, float $days, ?string $notes = null): int
    {
        $count = 0;
        foreach ($users as $user) {
            $this->allocate($user, $leaveType, $year, $days, $notes);
            $count++;
        }
        return $count;
    }

    public function allocateByDepartment(int $departmentId, LeaveType $leaveType, int $year, float $days, ?string $notes = null): int
    {
        $employees = \App\Models\Employee::where('department_id', $departmentId)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $users = $employees->pluck('user')->filter();
        return $this->bulkAllocate($users, $leaveType, $year, $days, $notes);
    }

    public function calculateServiceBasedAllocation(User $user, LeaveType $leaveType, float $daysPerYear): float
    {
        $hireDate = $user->hire_date ?? $user->employee?->hire_date;
        if (!$hireDate) return 0.0;

        $monthsEmployed = $hireDate->diffInMonths(now());
        $serviceYears = max(1, floor($monthsEmployed / 12));

        return round($daysPerYear * min($serviceYears, 5), 2);
    }
}
