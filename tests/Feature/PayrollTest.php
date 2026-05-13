<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('payroll.index'))->assertOk();
    }

    public function test_admin_can_generate_payslips(): void
    {
        $user = User::factory()->create();
        $structure = SalaryStructure::create([
            'name' => 'Standard',
            'code' => 'STD',
            'base_salary' => 5000,
            'is_active' => true,
        ]);

        SalaryComponent::create([
            'salary_structure_id' => $structure->id,
            'name' => 'Housing',
            'code' => 'HOUSING',
            'type' => 'allowance',
            'amount_type' => 'percentage',
            'amount' => 20,
        ]);

        SalaryComponent::create([
            'salary_structure_id' => $structure->id,
            'name' => 'Tax',
            'code' => 'TAX',
            'type' => 'deduction',
            'amount_type' => 'fixed',
            'amount' => 500,
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'salary_structure_id' => $structure->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('payroll.generate'), [
                'month' => now()->month,
                'year' => now()->year,
                'employee_ids' => [$employee->id],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payslips', [
            'employee_id' => $employee->id,
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function test_payslip_show_page_loads(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $payslip = Payslip::create([
            'employee_id' => $employee->id,
            'month' => now()->month,
            'year' => now()->year,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'basic_salary' => 5000,
            'total_allowance' => 1000,
            'total_deduction' => 500,
            'net_salary' => 5500,
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->get(route('payroll.show', $payslip))
            ->assertOk()
            ->assertSee($payslip->net_salary);
    }

    public function test_employee_can_view_own_payslips(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        Payslip::create([
            'employee_id' => $employee->id,
            'month' => now()->month,
            'year' => now()->year,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'basic_salary' => 5000,
            'total_allowance' => 1000,
            'total_deduction' => 500,
            'net_salary' => 5500,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('payroll.my'))
            ->assertOk();
    }
}
