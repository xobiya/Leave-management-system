<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveAllocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'carried_over_days',
        'expires_at',
        'notes',
        'allocation_type',
        'accrual_plan_id',
        'last_accrual_date',
        'next_accrual_date',
        'yearly_accrued_amount',
        'expiring_carryover_days',
        'carried_over_expiration',
        'total_allocated_days',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'last_accrual_date' => 'date',
        'next_accrual_date' => 'date',
        'carried_over_expiration' => 'date',
        'allocated_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'carried_over_days' => 'decimal:2',
        'yearly_accrued_amount' => 'decimal:2',
        'expiring_carryover_days' => 'decimal:2',
        'total_allocated_days' => 'decimal:2',
        'year' => 'integer',
        'allocation_type' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function accrualPlan(): BelongsTo
    {
        return $this->belongsTo(AccrualPlan::class);
    }

    public function getRemainingDaysAttribute(): float
    {
        return (float) max(0, ($this->total_allocated_days ?: $this->allocated_days + $this->carried_over_days) - $this->used_days);
    }
}
