<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $leaveTypes = LeaveType::query()->orderBy('name')->get();

        return response()->json([
            'data' => $leaveTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);

        $leaveType = LeaveType::query()->create($this->mapPayload($data));

        return response()->json([
            'message' => 'Leave type created.',
            'data' => $leaveType,
        ], 201);
    }

    public function update(Request $request, LeaveType $leaveType): JsonResponse
    {
        $data = $this->validatePayload($request, $leaveType);

        $leaveType->update($this->mapPayload($data));

        return response()->json([
            'message' => 'Leave type updated.',
            'data' => $leaveType->fresh(),
        ]);
    }

    private function validatePayload(Request $request, ?LeaveType $leaveType = null): array
    {
        $codeRule = [
            'required',
            'string',
            'max:30',
            Rule::unique('leave_types', 'code')->ignore($leaveType?->id),
        ];

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => $codeRule,
            'is_paid' => ['required', 'boolean'],
            'validation_type' => ['required', 'in:no,manager,hr,both'],
            'carry_forward_cap' => ['nullable', 'integer', 'min:0'],
            'allocation_type' => ['required', 'in:fixed,accrual'],
            'request_unit' => ['required', 'in:day,half_day,hour'],
            'allow_half_day' => ['required', 'boolean'],
            'allow_hour' => ['required', 'boolean'],
            'accrual_rate' => ['nullable', 'numeric', 'min:0'],
            'accrual_cap' => ['nullable', 'numeric', 'min:0'],
            'active' => ['sometimes', 'boolean'],
            'max_days_per_request' => ['nullable', 'integer', 'min:1'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
    }

    private function mapPayload(array $data): array
    {
        $carryForwardCap = (int) ($data['carry_forward_cap'] ?? 0);

        return [
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'color' => $data['color'] ?? null,
            'is_paid' => (bool) $data['is_paid'],
            'requires_manager_approval' => in_array($data['validation_type'], ['manager', 'both'], true),
            'requires_hr_approval' => in_array($data['validation_type'], ['hr', 'both'], true),
            'carry_forward' => $carryForwardCap > 0,
            'carry_forward_cap' => $carryForwardCap,
            'max_days_per_request' => $data['max_days_per_request'] ?? null,
            'active' => array_key_exists('active', $data) ? (bool) $data['active'] : true,
            'allocation_type' => $data['allocation_type'],
            'validation_type' => $data['validation_type'],
            'request_unit' => $data['request_unit'],
            'allow_half_day' => (bool) $data['allow_half_day'],
            'allow_hour' => (bool) $data['allow_hour'],
            'accrual_rate' => (float) ($data['accrual_rate'] ?? 0),
            'accrual_cap' => $data['accrual_cap'] !== null ? (float) $data['accrual_cap'] : null,
            'requires_allocation' => $data['allocation_type'] === 'fixed',
        ];
    }
}
