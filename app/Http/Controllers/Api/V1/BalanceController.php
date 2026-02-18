<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LeaveAllocation;
use App\Models\LeaveHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function balances(Request $request): JsonResponse
    {
        $year = (int) ($request->query('year', now()->format('Y')));

        $allocations = LeaveAllocation::query()
            ->with('leaveType:id,name,code')
            ->where('user_id', '=', $request->user()->id)
            ->where('year', '=', $year)
            ->get()
            ->map(function (LeaveAllocation $allocation) {
                $remaining = (float) (($allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days);

                return [
                    'leave_type_id' => $allocation->leave_type_id,
                    'leave_type' => $allocation->leaveType,
                    'year' => $allocation->year,
                    'allocated_days' => (float) $allocation->allocated_days,
                    'used_days' => (float) $allocation->used_days,
                    'carried_over_days' => (float) $allocation->carried_over_days,
                    'remaining_days' => max(0, $remaining),
                ];
            });

        return response()->json([
            'data' => $allocations,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $year = $request->query('year');

        $query = LeaveHistory::query()
            ->with('leaveType:id,name,code')
            ->where('user_id', '=', $request->user()->id)
            ->latest('id');

        if ($year !== null) {
            $query->where('year', '=', (int) $year);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }
}
