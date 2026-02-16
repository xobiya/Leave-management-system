<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\AllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllocationController extends Controller
{
    public function index(): View
    {
        $allocations = LeaveAllocation::query()->with(['user', 'leaveType'])->latest()->get();
        $leaveTypes = LeaveType::query()->where('active', '=', true)->get();
        $users = User::query()->orderBy('name', 'asc')->get();

        return view('erp.admin.allocations', compact('allocations', 'leaveTypes', 'users'));
    }

    public function store(Request $request, AllocationService $service): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'year' => ['required', 'integer', 'min:2000'],
            'allocated_days' => ['required', 'numeric', 'min:0'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);
        $leaveType = LeaveType::query()->findOrFail($data['leave_type_id']);

        $service->allocate($user, $leaveType, (int) $data['year'], (float) $data['allocated_days']);

        return back()->with('status', 'allocation-created');
    }
}
