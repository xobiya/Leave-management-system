<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    protected $model = \App\Models\LeaveType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Leave',
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'is_paid' => true,
            'requires_manager_approval' => true,
            'requires_hr_approval' => false,
            'carry_forward' => false,
            'carry_forward_cap' => 0,
            'max_days_per_request' => 30,
            'active' => true,
            'allocation_type' => 'fixed',
            'validation_type' => 'manager',
            'request_unit' => 'day',
            'allow_half_day' => false,
            'allow_hour' => false,
            'accrual_rate' => 0,
            'accrual_cap' => null,
            'requires_allocation' => true,
        ];
    }

    public function accrual(): static
    {
        return $this->state(fn(array $attrs) => [
            'allocation_type' => 'accrual',
            'accrual_rate' => 1.5,
            'accrual_cap' => 20,
        ]);
    }

    public function bothApproval(): static
    {
        return $this->state(fn(array $attrs) => [
            'validation_type' => 'both',
            'requires_manager_approval' => true,
            'requires_hr_approval' => true,
        ]);
    }
}
