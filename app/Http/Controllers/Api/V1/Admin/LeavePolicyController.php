<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeavePolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeavePolicyController extends Controller
{
    public function index(): JsonResponse
    {
        $policies = LeavePolicy::query()
            ->with('leaveType:id,name,code')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $policies,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'min_service_months' => ['required', 'integer', 'min:0'],
            'max_days_per_year' => ['nullable', 'numeric', 'min:0'],
            'max_unpaid_days_per_year' => ['nullable', 'numeric', 'min:0'],
            'allow_backdate' => ['required', 'boolean'],
            'allow_future_apply_days' => ['nullable', 'integer', 'min:0'],
            'yearly_reset' => ['required', 'boolean'],
            'expiry_days' => ['nullable', 'integer', 'min:0'],
            'carry_forward_limit' => ['nullable', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active' => ['required', 'boolean'],
        ]);

        $policy = DB::transaction(function () use ($data) {
            $nextVersion = (int) LeavePolicy::query()
                ->where('leave_type_id', '=', $data['leave_type_id'])
                ->max('version') + 1;

            if ((bool) $data['is_active']) {
                LeavePolicy::query()
                    ->where('leave_type_id', '=', $data['leave_type_id'])
                    ->update(['is_active' => false]);
            }

            return LeavePolicy::query()->create([
                'leave_type_id' => $data['leave_type_id'],
                'version' => $nextVersion,
                'min_service_months' => (int) $data['min_service_months'],
                'max_days_per_year' => $data['max_days_per_year'] !== null ? (float) $data['max_days_per_year'] : null,
                'max_unpaid_days_per_year' => $data['max_unpaid_days_per_year'] !== null ? (float) $data['max_unpaid_days_per_year'] : null,
                'allow_backdate' => (bool) $data['allow_backdate'],
                'allow_future_apply_days' => $data['allow_future_apply_days'] !== null ? (int) $data['allow_future_apply_days'] : null,
                'yearly_reset' => (bool) $data['yearly_reset'],
                'expiry_days' => $data['expiry_days'] !== null ? (int) $data['expiry_days'] : null,
                'carry_forward_limit' => $data['carry_forward_limit'] !== null ? (float) $data['carry_forward_limit'] : null,
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);
        });

        return response()->json([
            'message' => 'Leave policy created.',
            'data' => $policy->load('leaveType:id,name,code'),
        ], 201);
    }

    public function activate(LeavePolicy $leavePolicy): JsonResponse
    {
        DB::transaction(function () use ($leavePolicy) {
            LeavePolicy::query()
                ->where('leave_type_id', '=', $leavePolicy->leave_type_id)
                ->update(['is_active' => false]);

            $leavePolicy->update(['is_active' => true]);
        });

        return response()->json([
            'message' => 'Leave policy activated.',
            'data' => $leavePolicy->fresh(['leaveType:id,name,code']),
        ]);
    }
}
