<?php

namespace Tests\Unit;

use App\Models\AccrualPlan;
use App\Models\AccrualPlanLevel;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\AccrualService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccrualServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccrualService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AccrualService::class);
    }

    public function test_process_accrual_adds_days_to_allocation(): void
    {
        $leaveType = LeaveType::factory()->create(['allocation_type' => 'accrual']);
        $user = User::factory()->create();

        $plan = AccrualPlan::create([
            'name' => 'Monthly Accrual',
            'leave_type_id' => $leaveType->id,
            'transition_mode' => 'based_on_level',
            'is_active' => true,
        ]);

        AccrualPlanLevel::create([
            'accrual_plan_id' => $plan->id,
            'sequence' => 1,
            'name' => 'Level 1',
            'added_value' => 1.5,
            'added_value_type' => 'fixed',
            'frequency' => 'monthly',
            'cap_accrued_time' => false,
            'cap_accrued_time_yearly' => false,
        ]);

        $allocation = LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocation_type' => 'accrual',
            'accrual_plan_id' => $plan->id,
            'allocated_days' => 0,
            'total_allocated_days' => 0,
            'used_days' => 0,
            'next_accrual_date' => now()->subDay(),
        ]);

        $result = $this->service->processAccrual($allocation);

        $this->assertTrue($result);
        $allocation->refresh();
        $this->assertEquals(1.5, (float) $allocation->allocated_days);
        $this->assertEquals(1.5, (float) $allocation->yearly_accrued_amount);
    }

    public function test_process_accrual_respects_cap(): void
    {
        $leaveType = LeaveType::factory()->create(['allocation_type' => 'accrual']);
        $user = User::factory()->create();

        $plan = AccrualPlan::create([
            'name' => 'Capped Accrual',
            'leave_type_id' => $leaveType->id,
            'transition_mode' => 'based_on_level',
            'is_active' => true,
        ]);

        AccrualPlanLevel::create([
            'accrual_plan_id' => $plan->id,
            'sequence' => 1,
            'name' => 'Level 1',
            'added_value' => 2.0,
            'added_value_type' => 'fixed',
            'frequency' => 'monthly',
            'cap_accrued_time' => true,
            'cap_accrued_time_amount' => 5.0,
            'cap_accrued_time_yearly' => false,
        ]);

        $allocation = LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocation_type' => 'accrual',
            'accrual_plan_id' => $plan->id,
            'allocated_days' => 4.5,
            'total_allocated_days' => 4.5,
            'used_days' => 0,
            'next_accrual_date' => now()->subDay(),
        ]);

        $this->service->processAccrual($allocation);
        $allocation->refresh();

        $this->assertEquals(5.0, (float) $allocation->allocated_days);
    }

    public function test_process_accrual_inactive_plan_returns_false(): void
    {
        $leaveType = LeaveType::factory()->create(['allocation_type' => 'accrual']);
        $user = User::factory()->create();

        $plan = AccrualPlan::create([
            'name' => 'Inactive Plan',
            'leave_type_id' => $leaveType->id,
            'is_active' => false,
        ]);

        $allocation = LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocation_type' => 'accrual',
            'accrual_plan_id' => $plan->id,
            'allocated_days' => 0,
            'used_days' => 0,
        ]);

        $result = $this->service->processAccrual($allocation);
        $this->assertFalse($result);
    }

    public function test_compute_projected_accrual(): void
    {
        $leaveType = LeaveType::factory()->create(['allocation_type' => 'accrual']);
        $user = User::factory()->create();

        $plan = AccrualPlan::create([
            'name' => 'Monthly 1.5',
            'leave_type_id' => $leaveType->id,
            'transition_mode' => 'based_on_level',
            'is_active' => true,
        ]);

        AccrualPlanLevel::create([
            'accrual_plan_id' => $plan->id,
            'sequence' => 1,
            'name' => 'Level 1',
            'added_value' => 1.5,
            'added_value_type' => 'fixed',
            'frequency' => 'monthly',
            'cap_accrued_time' => false,
            'cap_accrued_time_yearly' => false,
        ]);

        LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocation_type' => 'accrual',
            'accrual_plan_id' => $plan->id,
            'allocated_days' => 3,
            'total_allocated_days' => 3,
            'used_days' => 0,
            'last_accrual_date' => now()->subMonths(2),
        ]);

        $projected = $this->service->computeProjectedAccrual($user, $leaveType->id);
        $this->assertGreaterThan(0, $projected);
    }
}
