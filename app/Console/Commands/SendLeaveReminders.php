<?php

namespace App\Console\Commands;

use App\Models\LeaveAllocation;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLeaveReminders extends Command
{
    protected $signature = 'erp:reminders-send';
    protected $description = 'Send reminder notifications for pending approvals and expiring leave';

    public function handle(): int
    {
        $this->sendPendingApprovalReminders();
        $this->sendExpiringLeaveReminders();

        $this->info('Reminders sent successfully.');
        return Command::SUCCESS;
    }

    private function sendPendingApprovalReminders(): void
    {
        $pendingRequests = LeaveRequest::with(['user', 'leaveType'])
            ->whereIn('status', ['submitted', 'manager_approved'])
            ->get()
            ->groupBy('manager_id');

        foreach ($pendingRequests as $managerId => $requests) {
            if (!$managerId) continue;
            $manager = User::find($managerId);
            if (!$manager) continue;

            $count = $requests->count();
            $names = $requests->take(3)->map(fn($r) => $r->user->name)->implode(', ');
            $extra = $count > 3 ? " and " . ($count - 3) . " more" : '';

            NotificationService::send(
                $manager,
                'Pending Leave Approvals',
                "You have {$count} pending leave request(s) from: {$names}{$extra}",
                null,
                'reminder',
                route('manager.dashboard'),
                'bell'
            );
        }
    }

    private function sendExpiringLeaveReminders(): void
    {
        $thirtyDaysFromNow = now()->addDays(30)->toDateString();

        $allocations = LeaveAllocation::with(['user', 'leaveType'])
            ->where(function ($q) use ($thirtyDaysFromNow) {
                $q->where('carried_over_expiration', '<=', $thirtyDaysFromNow)
                    ->where('carried_over_expiration', '>=', now()->toDateString())
                    ->where('expiring_carryover_days', '>', 0);
            })
            ->orWhere(function ($q) {
                $q->where('expires_at', '<=', now()->addDays(30))
                    ->where('expires_at', '>=', now()->toDateString())
                    ->whereRaw('(total_allocated_days - used_days) > 0');
            })
            ->get();

        foreach ($allocations as $allocation) {
            $remaining = ($allocation->total_allocated_days ?: $allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days;

            if ($remaining <= 0) continue;

            $expiryDate = $allocation->carried_over_expiration ?? $allocation->expires_at;
            $daysUntilExpiry = $expiryDate ? now()->diffInDays($expiryDate) : 0;

            NotificationService::send(
                $allocation->user,
                'Leave Balance Expiring Soon',
                "You have {$remaining} days of {$allocation->leaveType->name} leave expiring in {$daysUntilExpiry} days.",
                $allocation,
                'reminder',
                route('employee.dashboard'),
                'calendar'
            );
        }
    }
}
