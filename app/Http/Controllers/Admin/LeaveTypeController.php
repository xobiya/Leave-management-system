<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(): View
    {
        $leaveTypes = LeaveType::query()->latest('created_at')->get();

        return view('erp.admin.leave-types', compact('leaveTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:30', 'unique:leave_types,code'],
            'is_paid' => ['required', 'boolean'],
            'validation_type' => ['required', 'in:no,manager,hr,both'],
            'carry_forward_cap' => ['nullable', 'integer', 'min:0'],
            'allocation_type' => ['required', 'in:fixed,accrual'],
            'request_unit' => ['required', 'in:day,half_day,hour'],
            'allow_half_day' => ['required', 'boolean'],
            'allow_hour' => ['required', 'boolean'],
            'accrual_rate' => ['nullable', 'numeric', 'min:0'],
            'accrual_cap' => ['nullable', 'numeric', 'min:0'],
        ]);

        LeaveType::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'is_paid' => (bool) $data['is_paid'],
            'requires_manager_approval' => in_array($data['validation_type'], ['manager', 'both'], true),
            'requires_hr_approval' => in_array($data['validation_type'], ['hr', 'both'], true),
            'carry_forward' => ((int) ($data['carry_forward_cap'] ?? 0)) > 0,
            'carry_forward_cap' => (int) ($data['carry_forward_cap'] ?? 0),
            'active' => true,
            'validation_type' => $data['validation_type'],
            'allocation_type' => $data['allocation_type'],
            'request_unit' => $data['request_unit'],
            'allow_half_day' => (bool) $data['allow_half_day'],
            'allow_hour' => (bool) $data['allow_hour'],
            'accrual_rate' => (float) ($data['accrual_rate'] ?? 0),
            'accrual_cap' => $data['accrual_cap'] !== null ? (float) $data['accrual_cap'] : null,
            'requires_allocation' => $data['allocation_type'] === 'fixed',
        ]);

        return back()->with('status', 'leave-type-created');
    }
}
