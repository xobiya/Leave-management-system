<?php

namespace App\Services;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestState;
use App\Models\LeaveType;
use App\Models\User;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use App\Mail\LeaveRequestSubmitted;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LeaveRequestService
{
    public function __construct(
        private LeaveBalanceService $balanceService,
        private PolicyEngineService $policyEngine,
        private LeaveLedgerService $ledgerService,
    )
    {
    }

    public function createRequest(User $user, LeaveType $leaveType, Carbon $start, Carbon $end, ?string $reason, ?string $requestUnit = null, ?float $hours = null, ?string $halfDayPeriod = null): LeaveRequest
    {
        $unit = $requestUnit ?: $leaveType->request_unit;

        if ($unit === 'half_day' && !$leaveType->allow_half_day) {
            abort(422, 'Half-day requests are not allowed for this leave type.');
        }

        if ($unit === 'hour' && !$leaveType->allow_hour) {
            abort(422, 'Hourly requests are not allowed for this leave type.');
        }
        if ($unit === 'hour' && !$hours) {
            abort(422, 'Requested hours are required for hourly requests.');
        }

        if ($unit === 'half_day' && !$halfDayPeriod) {
            abort(422, 'Half-day period is required.');
        }

        $days = $this->balanceService->calculateRequestedDays($start, $end, $unit, $hours);
        $year = (int) $start->format('Y');

        $this->policyEngine->validateEligibility($user, $leaveType, $start, $end, $days);

        $allocation = $this->balanceService->getAllocation($user, $leaveType, $year);

        if (!$allocation && $leaveType->requires_allocation) {
            abort(422, 'No allocation found for this leave type.');
        }

        $remaining = $this->balanceService->getRemaining($user, $leaveType, $year);

        if ($remaining < $days) {
            abort(422, 'Insufficient leave balance.');
        }

        if ($leaveType->max_days_per_request && $days > $leaveType->max_days_per_request) {
            abort(422, 'Request exceeds max days allowed.');
        }

        $overlap = LeaveRequest::query()
            ->where('user_id', '=', $user->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($overlap) {
            abort(422, 'Overlapping leave request exists.');
        }

        $managerId = $user->manager_id;

        $validationType = $leaveType->validation_type;
        $requiresManager = in_array($validationType, ['manager', 'both'], true);
        $requiresHr = in_array($validationType, ['hr', 'both'], true);

        $request = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $start,
            'end_date' => $end,
            'days' => $days,
            'reason' => $reason,
            'request_unit' => $unit,
            'requested_hours' => $unit === 'hour' ? $hours : null,
            'half_day_period' => $unit === 'half_day' ? $halfDayPeriod : null,
            'status' => 'submitted',
            'manager_id' => $managerId,
            'manager_status' => $requiresManager ? 'pending' : 'approved',
            'hr_status' => $requiresHr ? 'pending' : 'approved',
        ]);

        $this->recordState($request, null, 'submitted', $user->id);

        if ($managerId && $requiresManager) {
            $manager = User::find($managerId);
            if ($manager?->email) {
                Mail::to($manager->email)->queue(new LeaveRequestSubmitted($request));
            }
        }

        if (!$requiresManager && !$requiresHr) {
            $this->finalizeApproval($request, null);
        }

        return $request;
    }

    public function approveManager(LeaveRequest $request, User $manager): LeaveRequest
    {
        $fromStatus = $request->status;
        $toStatus = $request->leaveType->validation_type === 'both' || $request->leaveType->validation_type === 'hr' ? 'manager_approved' : 'approved';

        $request->update([
            'manager_id' => $manager->id,
            'manager_status' => 'approved',
            'manager_approved_at' => now(),
            'status' => $toStatus,
        ]);

        $this->recordState($request, $fromStatus, $toStatus, $manager->id);

        if ($toStatus === 'approved') {
            $this->sendApprovalEmail($request);
        }

        if (!in_array($request->leaveType->validation_type, ['hr', 'both'], true)) {
            $this->finalizeApproval($request, $manager);
        }

        return $request->refresh();
    }

    public function approveHr(LeaveRequest $request, User $hr): LeaveRequest
    {
        $fromStatus = $request->status;

        $request->update([
            'hr_id' => $hr->id,
            'hr_status' => 'approved',
            'hr_approved_at' => now(),
            'status' => 'approved',
        ]);

        $this->recordState($request, $fromStatus, 'approved', $hr->id);

        $this->finalizeApproval($request, $hr);

        $this->sendApprovalEmail($request);

        return $request->refresh();
    }

    public function reject(LeaveRequest $request, User $actor, string $reason): LeaveRequest
    {
        $fromStatus = $request->status;

        $request->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'manager_id' => $request->manager_id ?? $actor->id,
            'hr_id' => $request->hr_id ?? $actor->id,
        ]);

        $this->recordState($request, $fromStatus, 'rejected', $actor->id, $reason);

        $request->load('user');
        if ($request->user?->email) {
            Mail::to($request->user->email)->queue(new LeaveRequestRejected($request));
        }

        return $request->refresh();
    }

    public function cancel(LeaveRequest $request, User $actor, ?string $reason = null): LeaveRequest
    {
        if ($request->user_id !== $actor->id && !$actor->hasAnyRole(['manager', 'admin', 'hr'])) {
            abort(403, 'You are not allowed to cancel this request.');
        }

        if (in_array($request->status, ['cancelled', 'rejected'], true)) {
            abort(422, 'This request is already closed.');
        }

        DB::transaction(function () use ($request, $actor, $reason) {
            $fromStatus = $request->status;

            if ($request->approved_at !== null) {
                /** @var LeaveAllocation|null $allocation */
                $allocation = LeaveAllocation::query()
                    ->where('user_id', '=', $request->user_id)
                    ->where('leave_type_id', '=', $request->leave_type_id)
                    ->where('year', '=', (int) $request->start_date->format('Y'))
                    ->lockForUpdate()
                    ->first();

                if ($allocation) {
                    $beforeRemaining = (float) (($allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days);
                    $nextUsed = max(0, (float) $allocation->used_days - (float) $request->days);

                    $allocation->update([
                        'used_days' => $nextUsed,
                    ]);

                    $afterRemaining = (float) (($allocation->allocated_days + $allocation->carried_over_days) - $nextUsed);

                    $this->ledgerService->recordCancellationRestore(
                        $request,
                        $beforeRemaining,
                        $afterRemaining,
                        $actor->id,
                    );
                }
            }

            $request->update([
                'status' => 'cancelled',
                'approved_at' => null,
                'rejection_reason' => $reason,
            ]);

            $this->recordState($request, $fromStatus, 'cancelled', $actor->id, $reason);
        });

        return $request->refresh();
    }

    private function finalizeApproval(LeaveRequest $request, ?User $actor = null): void
    {
        DB::transaction(function () use ($request, $actor) {
            /** @var LeaveAllocation|null $allocation */
            $allocation = LeaveAllocation::query()
                ->where('user_id', '=', $request->user_id)
                ->where('leave_type_id', '=', $request->leave_type_id)
                ->where('year', '=', (int) $request->start_date->format('Y'))
                ->lockForUpdate()
                ->first();

            if (!$allocation) {
                $allocation = LeaveAllocation::create([
                    'user_id' => $request->user_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => (int) $request->start_date->format('Y'),
                    'allocated_days' => 0,
                    'used_days' => 0,
                    'carried_over_days' => 0,
                ]);
            }

            $beforeRemaining = (float) (($allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days);
            $afterUsedDays = (float) ($allocation->used_days + $request->days);
            $afterRemaining = (float) (($allocation->allocated_days + $allocation->carried_over_days) - $afterUsedDays);

            $allocation->update([
                'used_days' => $afterUsedDays,
            ]);

            $request->update([
                'approved_at' => now(),
            ]);

            $this->ledgerService->recordApprovalDeduction(
                $request,
                $beforeRemaining,
                $afterRemaining,
                $actor?->id,
            );
        });
    }

    private function sendApprovalEmail(LeaveRequest $request): void
    {
        $request->load('user');
        if ($request->user?->email) {
            Mail::to($request->user->email)->queue(new LeaveRequestApproved($request));
        }
    }

    private function recordState(LeaveRequest $request, ?string $from, string $to, ?int $actorId = null, ?string $reason = null): void
    {
        LeaveRequestState::create([
            'leave_request_id' => $request->id,
            'from_status' => $from,
            'to_status' => $to,
            'actor_id' => $actorId,
            'reason' => $reason,
            'metadata' => [
                'manager_status' => $request->manager_status,
                'hr_status' => $request->hr_status,
            ],
        ]);
    }
}
