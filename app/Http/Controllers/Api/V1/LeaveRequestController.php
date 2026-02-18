<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveRequestService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LeaveRequest::query()
            ->with(['leaveType:id,name,code', 'manager:id,name', 'hr:id,name'])
            ->where('user_id', '=', $request->user()->id)
            ->latest('id');

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request, LeaveRequestService $service): JsonResponse
    {
        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
            'request_unit' => ['nullable', 'in:day,half_day,hour'],
            'requested_hours' => ['nullable', 'numeric', 'min:0.5'],
            'half_day_period' => ['nullable', 'in:am,pm'],
        ]);

        $leaveType = LeaveType::query()->findOrFail($data['leave_type_id']);

        $leaveRequest = $service->createRequest(
            $request->user(),
            $leaveType,
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            $data['reason'] ?? null,
            $data['request_unit'] ?? null,
            $data['requested_hours'] ?? null,
            $data['half_day_period'] ?? null,
        );

        return response()->json([
            'message' => 'Leave request submitted.',
            'data' => $leaveRequest->load(['leaveType:id,name,code']),
        ], 201);
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest, LeaveRequestService $service): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $leaveRequest = $service->cancel($leaveRequest, $request->user(), $data['reason'] ?? null);

        return response()->json([
            'message' => 'Leave request cancelled.',
            'data' => $leaveRequest,
        ]);
    }
}
