<?php

namespace App\Console\Commands;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CarryForwardLeave extends Command
{
    protected $signature = 'erp:carry-forward {--dry-run : Preview changes without saving}';
    protected $description = 'Carry over remaining leave balances to the next year for types with carry_forward enabled';

    public function handle(): int
    {
        $carryForwardTypes = LeaveType::where('carry_forward', true)->where('active', true)->get();

        if ($carryForwardTypes->isEmpty()) {
            $this->warn('No leave types with carry-forward enabled.');
            return Command::SUCCESS;
        }

        $setting = SystemSetting::where('key', 'leave_year_start')->first();
        $yearStart = $setting && isset($setting->value['month'], $setting->value['day'])
            ? Carbon::create(null, $setting->value['month'], $setting->value['day'])
            : Carbon::create(null, 1, 1);

        $currentYear = now()->year;

        foreach ($carryForwardTypes as $leaveType) {
            $this->info("Processing carry-forward for: {$leaveType->name}");

            $allocations = LeaveAllocation::where('leave_type_id', $leaveType->id)
                ->where('year', $currentYear - 1)
                ->get();

            foreach ($allocations as $allocation) {
                $remaining = max(0, ($allocation->total_allocated_days ?: $allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days);

                if ($remaining <= 0) {
                    continue;
                }

                $carryCap = $leaveType->carry_forward_cap;
                $carryOver = $carryCap ? min($remaining, $carryCap) : $remaining;

                if ($this->option('dry-run')) {
                    $this->line("  [DRY-RUN] User {$allocation->user_id}: {$remaining} days → carry {$carryOver} days to {$currentYear}");
                    continue;
                }

                DB::transaction(function () use ($allocation, $carryOver, $currentYear, $yearStart) {
                    $nextAllocation = LeaveAllocation::firstOrNew([
                        'user_id' => $allocation->user_id,
                        'leave_type_id' => $allocation->leave_type_id,
                        'year' => $currentYear,
                    ]);

                    $nextAllocation->carried_over_days = ($nextAllocation->carried_over_days ?? 0) + $carryOver;
                    $nextAllocation->carried_over_expiration = $yearStart->copy()->addYear();
                    $nextAllocation->expiring_carryover_days = $carryOver;
                    $nextAllocation->total_allocated_days = ($nextAllocation->total_allocated_days ?? 0) + $carryOver;
                    if (!$nextAllocation->exists) {
                        $nextAllocation->allocated_days = $nextAllocation->allocated_days ?? 0;
                        $nextAllocation->used_days = 0;
                    }
                    $nextAllocation->save();
                });
            }
        }

        $this->info('Carry-forward processing completed.');
        return Command::SUCCESS;
    }
}
