<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\LeaveAllocation;
use App\Models\LeavePolicy;
use App\Models\LeaveType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        $roles = [
            'admin' => Role::query()->updateOrCreate(['slug' => 'admin'], ['name' => 'Admin']),
            'manager' => Role::query()->updateOrCreate(['slug' => 'manager'], ['name' => 'Manager']),
            'employee' => Role::query()->updateOrCreate(['slug' => 'employee'], ['name' => 'Employee']),
            'hr' => Role::query()->updateOrCreate(['slug' => 'hr'], ['name' => 'HR']),
        ];

        $permissions = [
            'manage-leave-types',
            'manage-allocations',
            'approve-requests',
            'view-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission],
                ['name' => ucwords(str_replace('-', ' ', $permission))],
            );
        }

        $departments = Department::query()->updateOrCreate(
            ['code' => 'HR'],
            ['name' => 'Human Resources'],
        );

        SystemSetting::query()->updateOrCreate(
            ['key' => 'leave_year_start'],
            [
                'value' => ['month' => 1, 'day' => 1],
                'type' => 'json',
                'description' => 'Leave year start month/day',
            ],
        );

        SystemSetting::query()->updateOrCreate(
            ['key' => 'hours_per_day'],
            [
                'value' => ['hours' => 8],
                'type' => 'json',
                'description' => 'Hours per working day',
            ],
        );

        $leaveTypes = [
            'annual' => LeaveType::query()->updateOrCreate(['code' => 'ANNUAL'], [
                'name' => 'Annual Leave',
                'is_paid' => true,
                'requires_manager_approval' => true,
                'requires_hr_approval' => true,
                'carry_forward' => true,
                'carry_forward_cap' => 5,
                'active' => true,
                'allocation_type' => 'accrual',
                'validation_type' => 'both',
                'request_unit' => 'day',
                'allow_half_day' => true,
                'allow_hour' => false,
                'accrual_rate' => 1.67,
                'accrual_cap' => 20,
                'requires_allocation' => false,
            ]),
            'sick' => LeaveType::query()->updateOrCreate(['code' => 'SICK'], [
                'name' => 'Sick Leave',
                'is_paid' => true,
                'requires_manager_approval' => true,
                'requires_hr_approval' => false,
                'carry_forward' => false,
                'carry_forward_cap' => 0,
                'active' => true,
                'allocation_type' => 'fixed',
                'validation_type' => 'manager',
                'request_unit' => 'day',
                'allow_half_day' => true,
                'allow_hour' => true,
                'accrual_rate' => 0,
                'accrual_cap' => null,
                'requires_allocation' => true,
            ]),
            'unpaid' => LeaveType::query()->updateOrCreate(['code' => 'UNPAID'], [
                'name' => 'Unpaid Leave',
                'is_paid' => false,
                'requires_manager_approval' => true,
                'requires_hr_approval' => true,
                'carry_forward' => false,
                'carry_forward_cap' => 0,
                'active' => true,
                'allocation_type' => 'fixed',
                'validation_type' => 'both',
                'request_unit' => 'day',
                'allow_half_day' => false,
                'allow_hour' => false,
                'accrual_rate' => 0,
                'accrual_cap' => null,
                'requires_allocation' => false,
            ]),
        ];

        $admin = User::query()->updateOrCreate(['email' => 'admin@hrleave.test'], [
            'name' => 'Admin User',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'department_id' => $departments->id,
        ]);

        $manager = User::query()->updateOrCreate(['email' => 'manager@hrleave.test'], [
            'name' => 'Manager User',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'department_id' => $departments->id,
        ]);

        $departments->update(['manager_id' => $manager->id]);

        $employee = User::query()->updateOrCreate(['email' => 'employee@hrleave.test'], [
            'name' => 'Employee User',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'department_id' => $departments->id,
            'manager_id' => $manager->id,
            'hire_date' => now()->subMonths(14)->toDateString(),
        ]);

        $hr = User::query()->updateOrCreate(['email' => 'hr@hrleave.test'], [
            'name' => 'HR User',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'department_id' => $departments->id,
        ]);

        $admin->roles()->sync([$roles['admin']->id]);
        $manager->roles()->sync([$roles['manager']->id]);
        $employee->roles()->sync([$roles['employee']->id]);
        $hr->roles()->sync([$roles['hr']->id]);

        $year = (int) Carbon::now()->format('Y');

        foreach ([$employee, $manager] as $user) {
            LeaveAllocation::query()->updateOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $leaveTypes['sick']->id,
                'year' => $year,
            ], [
                'allocated_days' => 10,
                'used_days' => 0,
                'carried_over_days' => 0,
            ]);
        }

        LeavePolicy::query()->where('leave_type_id', '=', $leaveTypes['annual']->id)->update(['is_active' => false]);
        LeavePolicy::query()->where('leave_type_id', '=', $leaveTypes['unpaid']->id)->update(['is_active' => false]);

        LeavePolicy::query()->updateOrCreate(
            ['leave_type_id' => $leaveTypes['annual']->id, 'version' => 1],
            [
                'min_service_months' => 11,
                'max_days_per_year' => 20,
                'max_unpaid_days_per_year' => null,
                'allow_backdate' => false,
                'allow_future_apply_days' => 365,
                'yearly_reset' => true,
                'expiry_days' => null,
                'carry_forward_limit' => 5,
                'effective_from' => now()->startOfYear()->toDateString(),
                'effective_to' => null,
                'is_active' => true,
            ],
        );

        LeavePolicy::query()->updateOrCreate(
            ['leave_type_id' => $leaveTypes['unpaid']->id, 'version' => 1],
            [
                'min_service_months' => 0,
                'max_days_per_year' => null,
                'max_unpaid_days_per_year' => 30,
                'allow_backdate' => false,
                'allow_future_apply_days' => 365,
                'yearly_reset' => true,
                'expiry_days' => null,
                'carry_forward_limit' => 0,
                'effective_from' => now()->startOfYear()->toDateString(),
                'effective_to' => null,
                'is_active' => true,
            ],
        );
    }
}
