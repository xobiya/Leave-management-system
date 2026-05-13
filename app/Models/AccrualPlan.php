<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccrualPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leave_type_id',
        'transition_mode',
        'accrued_gain_time',
        'carryover_date',
        'custom_carryover_date',
        'is_based_on_worked_time',
        'is_active',
    ];

    protected $casts = [
        'custom_carryover_date' => 'date',
        'is_based_on_worked_time' => 'boolean',
        'is_active' => 'boolean',
        'transition_mode' => 'string',
        'accrued_gain_time' => 'string',
        'carryover_date' => 'string',
    ];

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(AccrualPlanLevel::class)->orderBy('sequence');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(LeaveAllocation::class);
    }
}
