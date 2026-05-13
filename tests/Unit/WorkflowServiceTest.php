<?php

namespace Tests\Unit;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transition_moves_from_draft_to_submitted(): void
    {
        $leaveType = LeaveType::factory()->create();
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'status' => 'draft',
            'request_unit' => 'day',
        ]);

        WorkflowService::transition($request, 'submit', $user);
        $request->refresh();

        $this->assertEquals('submitted', $request->status);
    }

    public function test_transition_throws_on_invalid_action(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $leaveType = LeaveType::factory()->create();
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'status' => 'draft',
            'request_unit' => 'day',
        ]);

        WorkflowService::transition($request, 'invalid_action', $user);
    }

    public function test_can_checks_allowed_transition(): void
    {
        $leaveType = LeaveType::factory()->create();
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'status' => 'draft',
            'request_unit' => 'day',
        ]);

        $this->assertTrue(WorkflowService::can($request, 'submit'));
        $this->assertFalse(WorkflowService::can($request, 'approve'));
    }

    public function test_available_actions_returns_correct_actions(): void
    {
        $leaveType = LeaveType::factory()->create();
        $user = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'status' => 'submitted',
            'request_unit' => 'day',
        ]);

        $actions = WorkflowService::availableActions($request);
        $this->assertContains('review', $actions);
        $this->assertContains('reject', $actions);
        $this->assertContains('cancel', $actions);
    }

    public function test_transition_handles_leave_request_approval_flow(): void
    {
        $leaveType = LeaveType::factory()->create();
        $user = User::factory()->create();
        $manager = User::factory()->create();

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'status' => 'draft',
            'request_unit' => 'day',
        ]);

        WorkflowService::transition($request, 'submit', $user);
        $this->assertEquals('submitted', $request->status);

        WorkflowService::transition($request, 'review', $manager);
        $this->assertEquals('under_review', $request->status);

        WorkflowService::transition($request, 'approve', $manager);
        $this->assertEquals('approved', $request->status);
    }
}
