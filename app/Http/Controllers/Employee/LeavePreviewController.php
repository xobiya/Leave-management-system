<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeavePreviewController extends Controller
{
    public function __invoke(Request $request, LeaveBalanceService $balanceService): JsonResponse
    {
        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'request_unit' => ['nullable', 'in:day,half_day,hour'],
            'requested_hours' => ['nullable', 'numeric', 'min:0.5'],
            'half_day_period' => ['nullable', 'in:am,pm'],
        ]);

        $leaveType = LeaveType::query()->findOrFail($data['leave_type_id']);
        $unit = $data['request_unit'] ?? $leaveType->request_unit;

        if ($unit === 'half_day' && !$leaveType->allow_half_day) {
            return response()->json(['message' => 'Half-day requests are not allowed.'], 422);
        }

        if ($unit === 'hour' && !$leaveType->allow_hour) {
            return response()->json(['message' => 'Hourly requests are not allowed.'], 422);
        }

        if ($unit === 'hour' && empty($data['requested_hours'])) {
            return response()->json(['message' => 'Requested hours are required.'], 422);
        }

        if ($unit === 'half_day' && empty($data['half_day_period'])) {
            return response()->json(['message' => 'Half-day period is required.'], 422);
        }

        $days = $balanceService->calculateRequestedDays(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            $unit,
            $data['requested_hours'] ?? null
        );

        return response()->json([
            'days' => $days,
            'unit' => $unit,
        ]);
    }
}
