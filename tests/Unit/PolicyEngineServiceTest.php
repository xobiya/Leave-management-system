<?php

namespace Tests\Unit;

use App\Models\LeavePolicy;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\PolicyEngineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PolicyEngineServiceTest extends TestCase
{
    use RefreshDatabase;

    private PolicyEngineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PolicyEngineService::class);
    }

    public function test_get_active_policy_returns_latest_version(): void
    {
        $leaveType = LeaveType::factory()->create();

        LeavePolicy::create([
            'leave_type_id' => $leaveType->id,
            'version' => 1,
            'is_active' => true,
            'min_service_months' => 0,
            'effective_from' => now()->subYear(),
        ]);

        LeavePolicy::create([
            'leave_type_id' => $leaveType->id,
            'version' => 2,
            'is_active' => true,
            'min_service_months' => 6,
            'effective_from' => now()->subMonth(),
        ]);

        $policy = $this->service->getActivePolicy($leaveType, now());
        $this->assertNotNull($policy);
        $this->assertEquals(2, $policy->version);
    }

    public function test_validate_eligibility_passes_without_policy(): void
    {
        $leaveType = LeaveType::factory()->create(['code' => 'SICK']);
        $user = User::factory()->create(['hire_date' => now()->subYear()]);

        $this->service->validateEligibility($user, $leaveType, now()->addDay(), now()->addDays(2), 1.0);
        $this->expectNotToPerformAssertions();
    }

    public function test_validate_eligibility_rejects_backdated_requests(): void
    {
        $leaveType = LeaveType::factory()->create(['code' => 'SICK']);

        LeavePolicy::create([
            'leave_type_id' => $leaveType->id,
            'version' => 1,
            'is_active' => true,
            'allow_backdate' => false,
            'effective_from' => now()->subYear(),
        ]);

        $user = User::factory()->create(['hire_date' => now()->subYear()]);

        $this->expectException(ValidationException::class);
        $this->service->validateEligibility($user, $leaveType, now()->subDay(), now()->addDay(), 1.0);
    }

    public function test_validate_eligibility_rejects_insufficient_service(): void
    {
        $leaveType = LeaveType::factory()->create(['code' => 'ANNUAL']);

        LeavePolicy::create([
            'leave_type_id' => $leaveType->id,
            'version' => 1,
            'is_active' => true,
            'min_service_months' => 12,
            'effective_from' => now()->subYear(),
        ]);

        $user = User::factory()->create(['hire_date' => now()->subMonths(3)]);

        $this->expectException(ValidationException::class);
        $this->service->validateEligibility($user, $leaveType, now()->addDay(), now()->addDays(2), 1.0);
    }

    public function test_validate_eligibility_rejects_exceeding_yearly_limit(): void
    {
        $leaveType = LeaveType::factory()->create(['code' => 'SICK']);

        LeavePolicy::create([
            'leave_type_id' => $leaveType->id,
            'version' => 1,
            'is_active' => true,
            'max_days_per_year' => 5,
            'effective_from' => now()->subYear(),
        ]);

        $user = User::factory()->create(['hire_date' => now()->subYear()]);

        $this->expectException(ValidationException::class);
        $this->service->validateEligibility($user, $leaveType, now()->addDay(), now()->addDays(2), 10.0);
    }

    public function test_start_date_must_precede_end_date(): void
    {
        $leaveType = LeaveType::factory()->create(['code' => 'SICK']);
        $user = User::factory()->create(['hire_date' => now()->subYear()]);

        $this->expectException(ValidationException::class);
        $this->service->validateEligibility($user, $leaveType, now()->addDays(2), now()->addDay(), 1.0);
    }
}
