<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_index_page_loads(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSee($employee->employee_code);
    }

    public function test_employee_creation_page_loads(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $this->actingAs($user)
            ->get(route('employees.create'))
            ->assertOk();
    }

    public function test_admin_can_create_employee(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $position = Position::create(['title' => 'Developer', 'department_id' => $department->id]);

        $this->actingAs($user)
            ->post(route('employees.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'employee_code' => 'EMP100',
                'department_id' => $department->id,
                'position_id' => $position->id,
                'hire_date' => now()->toDateString(),
                'gender' => 'male',
            ])
            ->assertRedirect(route('employees.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('employees', ['employee_code' => 'EMP100']);
    }

    public function test_employee_show_page_loads(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('employees.show', $employee))
            ->assertOk()
            ->assertSee($employee->employee_code);
    }

    public function test_employee_edit_page_loads(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('employees.edit', $employee))
            ->assertOk();
    }

    public function test_admin_can_update_employee(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->put(route('employees.update', $employee), [
                'department_id' => $department->id,
                'status' => 'active',
                'gender' => 'male',
            ])
            ->assertRedirect(route('employees.show', $employee))
            ->assertSessionHas('success');
    }

    public function test_admin_can_delete_employee(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->delete(route('employees.destroy', $employee))
            ->assertRedirect(route('employees.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($employee);
    }

    public function test_org_chart_page_loads(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $manager = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'department_id' => $department->id,
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('employees.org-chart'))
            ->assertOk();
    }
}
