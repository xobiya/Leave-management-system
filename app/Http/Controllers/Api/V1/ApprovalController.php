<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function managerApprove(LeaveRequest $leaveRequest, LeaveRequestService $service): JsonResponse
    {
        $updated = $service->approveManager($leaveRequest, request()->user());

        return response()->json([
            'message' => 'Manager approval completed.',
            'data' => $updated,
        ]);
    }

    public function hrApprove(LeaveRequest $leaveRequest, LeaveRequestService $service): JsonResponse
    {
        $updated = $service->approveHr($leaveRequest, request()->user());

        return response()->json([
            'message' => 'HR approval completed.',
            'data' => $updated,
        ]);
    }

    public function reject(Request $request, LeaveRequest $leaveRequest, LeaveRequestService $service): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $updated = $service->reject($leaveRequest, $request->user(), $data['reason']);

        return response()->json([
            'message' => 'Leave request rejected.',
            'data' => $updated,
        ]);
    }
}
