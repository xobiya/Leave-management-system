<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\AllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllocationServiceTest extends TestCase
{
    use RefreshDatabase;

    private AllocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AllocationService::class);
    }

    public function test_allocate_creates_new_allocation(): void
    {
        $user = User::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $allocation = $this->service->allocate($user, $leaveType, now()->year, 15);

        $this->assertNotNull($allocation);
        $this->assertEquals(15, (float) $allocation->allocated_days);
        $this->assertEquals($user->id, $allocation->user_id);
    }

    public function test_allocate_updates_existing_allocation(): void
    {
        $user = User::factory()->create();
        $leaveType = LeaveType::factory()->create();

        LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocated_days' => 10,
            'used_days' => 0,
        ]);

        $allocation = $this->service->allocate($user, $leaveType, now()->year, 20);

        $this->assertEquals(20, (float) $allocation->allocated_days);
        $this->assertEquals(1, LeaveAllocation::where('user_id', $user->id)->count());
    }

    public function test_bulk_allocate_creates_allocations_for_multiple_users(): void
    {
        $leaveType = LeaveType::factory()->create();
        $users = User::factory()->count(3)->create();

        $count = $this->service->bulkAllocate($users, $leaveType, now()->year, 15);

        $this->assertEquals(3, $count);
        $this->assertEquals(3, LeaveAllocation::where('leave_type_id', $leaveType->id)->count());
    }

    public function test_allocate_by_department(): void
    {
        $leaveType = LeaveType::factory()->create();
        $department = Department::factory()->create();

        $users = User::factory()->count(2)->create();
        foreach ($users as $user) {
            Employee::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'status' => 'active',
                'employee_code' => 'EMP' . $user->id,
            ]);
        }

        $count = $this->service->allocateByDepartment($department->id, $leaveType, now()->year, 15);
        $this->assertEquals(2, $count);
    }
}
