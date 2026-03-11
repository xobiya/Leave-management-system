<x-app-layout title="Leave Types" panel="admin">
    <div class="grid gap-6">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Configuration</p>
                    <h2 class="mt-2 text-xl font-semibold">Leave type library</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="erp-button-ghost">Import</button>
                    <button class="erp-button">Create leave type</button>
                </div>
            </div>
            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Paid</th>
                        <th>Approval</th>
                        <th>Carry forward</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveTypes as $leaveType)
                        <tr>
                            <td>{{ $leaveType->name }}</td>
                            <td>{{ $leaveType->is_paid ? 'Yes' : 'No' }}</td>
                            <td>{{ $leaveType->requires_hr_approval ? 'Manager + HR' : 'Manager' }}</td>
                            <td>{{ $leaveType->carry_forward ? 'Up to '.$leaveType->carry_forward_cap.' days' : 'No' }}</td>
                            <td>
                                <span class="erp-badge {{ $leaveType->active ? 'erp-badge-success' : 'erp-badge-warning' }}">
                                    {{ $leaveType->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-xs text-base-500">No leave types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Policy editor</p>
            <h3 class="mt-2 text-lg font-semibold">Create new type</h3>
            <form method="POST" action="{{ route('admin.leave-types.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="erp-label">Name</label>
                    <input name="name" class="erp-input mt-2" placeholder="e.g. Study Leave" required />
                </div>
                <div>
                    <label class="erp-label">Approval flow</label>
                    <select name="validation_type" class="erp-input mt-2">
                        <option value="manager">Manager only</option>
                        <option value="both">Manager + HR</option>
                        <option value="hr">HR only</option>
                        <option value="no">No validation</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Paid status</label>
                    <select name="is_paid" class="erp-input mt-2">
                        <option value="1">Paid</option>
                        <option value="0">Unpaid</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Allocation type</label>
                    <select name="allocation_type" class="erp-input mt-2">
                        <option value="fixed">Fixed allocation</option>
                        <option value="accrual">Accrual plan</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Request unit</label>
                    <select name="request_unit" class="erp-input mt-2">
                        <option value="day">Day</option>
                        <option value="half_day">Half day</option>
                        <option value="hour">Hour</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Allow half day</label>
                    <select name="allow_half_day" class="erp-input mt-2">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Allow hours</label>
                    <select name="allow_hour" class="erp-input mt-2">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Accrual rate (days/month)</label>
                    <input name="accrual_rate" class="erp-input mt-2" placeholder="1.5" />
                </div>
                <div>
                    <label class="erp-label">Accrual cap (days)</label>
                    <input name="accrual_cap" class="erp-input mt-2" placeholder="24" />
                </div>
                <div>
                    <label class="erp-label">Carry forward cap</label>
                    <input name="carry_forward_cap" class="erp-input mt-2" placeholder="0" />
                </div>
                <div>
                    <label class="erp-label">Code</label>
                    <input name="code" class="erp-input mt-2" placeholder="e.g. STUDY" required />
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="erp-button">Save leave type</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
