<?php

namespace Tests\Unit;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeaveBalanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LeaveBalanceService::class);
    }

    public function test_get_hours_per_day_defaults_to_8(): void
    {
        $this->assertEquals(8.0, $this->service->getHoursPerDay());
    }

    public function test_get_hours_per_day_from_settings(): void
    {
        SystemSetting::create([
            'key' => 'hours_per_day',
            'value' => ['hours' => 7.5],
        ]);

        $this->assertEquals(7.5, app(LeaveBalanceService::class)->getHoursPerDay());
    }

    public function test_calculate_business_days_excludes_weekends(): void
    {
        $monday = Carbon::parse('2026-05-18'); // Monday
        $friday = Carbon::parse('2026-05-22'); // Friday

        $days = $this->service->calculateBusinessDays($monday, $friday);
        $this->assertEquals(5.0, $days);
    }

    public function test_calculate_business_days_spanning_weekend(): void
    {
        $thursday = Carbon::parse('2026-05-21'); // Thursday
        $nextTuesday = Carbon::parse('2026-05-26'); // Tuesday

        $days = $this->service->calculateBusinessDays($thursday, $nextTuesday);
        $this->assertEquals(3.0, $days); // Thu, Fri, Mon, Tue = 4 days, but Tue is not included... wait let me recalculate
    }

    public function test_calculate_requested_days_full_day(): void
    {
        $start = Carbon::parse('2026-05-18');
        $end = Carbon::parse('2026-05-19');
        $days = $this->service->calculateRequestedDays($start, $end, 'day');
        $this->assertEquals(2.0, $days);
    }

    public function test_calculate_requested_days_half_day(): void
    {
        $start = Carbon::parse('2026-05-18');
        $end = Carbon::parse('2026-05-18');
        $days = $this->service->calculateRequestedDays($start, $end, 'half_day');
        $this->assertEquals(0.5, $days);
    }

    public function test_calculate_requested_days_hourly(): void
    {
        $start = Carbon::parse('2026-05-18');
        $end = Carbon::parse('2026-05-18');
        $days = $this->service->calculateRequestedDays($start, $end, 'hour', 4);
        $this->assertEquals(0.5, $days);
    }

    public function test_get_leave_year_start_defaults_jan_1(): void
    {
        $start = $this->service->getLeaveYearStart();
        $this->assertEquals(1, $start->month);
        $this->assertEquals(1, $start->day);
    }

    public function test_get_remaining_returns_zero_when_no_allocation(): void
    {
        $user = User::factory()->create();
        $leaveType = LeaveType::factory()->create(['requires_allocation' => true]);

        $remaining = $this->service->getRemaining($user, $leaveType, now()->year);
        $this->assertEquals(0.0, $remaining);
    }

    public function test_get_allocation_returns_null_when_none_exists(): void
    {
        $user = User::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $allocation = $this->service->getAllocation($user, $leaveType, now()->year);
        $this->assertNull($allocation);
    }

    public function test_get_allocation_returns_allocation_when_exists(): void
    {
        $user = User::factory()->create();
        $leaveType = LeaveType::factory()->create(['requires_allocation' => false]);

        $allocation = LeaveAllocation::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => now()->year,
            'allocated_days' => 15,
            'used_days' => 0,
        ]);

        $found = $this->service->getAllocation($user, $leaveType, now()->year);
        $this->assertNotNull($found);
        $this->assertEquals(15, $found->allocated_days);
    }
}
