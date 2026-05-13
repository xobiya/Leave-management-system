<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_check_in(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('attendance.check-in'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);
    }

    public function test_employee_cannot_check_in_twice(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'status' => 'present',
        ]);

        $this->actingAs($user)
            ->post(route('attendance.check-in'))
            ->assertSessionHas('error');
    }

    public function test_employee_can_check_out(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'check_in' => now()->subHours(8),
            'status' => 'present',
        ]);

        $this->actingAs($user)
            ->post(route('attendance.check-out'))
            ->assertSessionHas('success');
    }

    public function test_attendance_index_shows_daily_records(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'present',
            'check_in' => now()->subHours(8),
            'check_out' => now(),
            'total_hours' => 8,
        ]);

        $this->actingAs($user)
            ->get(route('attendance.index'))
            ->assertOk()
            ->assertSee($employee->employee_code);
    }

    public function test_admin_can_store_attendance_manually(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
                'status' => 'present',
                'check_in' => '09:00',
                'check_out' => '18:00',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'present',
            'total_hours' => 9.0,
        ]);
    }

    public function test_my_attendance_shows_employee_records(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'present',
            'check_in' => now()->subHours(8),
            'check_out' => now(),
            'total_hours' => 8,
        ]);

        $this->actingAs($user)
            ->get(route('attendance.my'))
            ->assertOk();
    }

    public function test_export_csv_returns_download(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.export-csv'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv');
    }
}
