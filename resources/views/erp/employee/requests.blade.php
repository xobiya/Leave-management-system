<x-app-layout title="My Leave Requests" panel="employee">
    <div class="grid gap-6 erp-stagger">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Filters</p>
                    <h2 class="mt-2 text-xl font-semibold">Request center</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <input class="erp-input" placeholder="Search by type or manager" />
                    <select class="erp-input">
                        <option>All time</option>
                        <option>Last 30 days</option>
                        <option>Last 6 months</option>
                    </select>
                    <select class="erp-input">
                        <option>Status: Any</option>
                        <option>Approved</option>
                        <option>Pending</option>
                        <option>Rejected</option>
                    </select>
                </div>
            </div>
            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Workflow</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $request->leaveType->name }}</td>
                            <td>{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}</td>
                            <td>{{ $request->days }}</td>
                            <td>
                                @php
                                    $workflow = match ($request->leaveType->validation_type) {
                                        'both' => 'Manager + HR',
                                        'hr' => 'HR only',
                                        'no' => 'No validation',
                                        default => 'Manager',
                                    };
                                @endphp
                                {{ $workflow }}
                            </td>
                            <td>
                                <span class="erp-badge {{ $request->status === 'approved' ? 'erp-badge-success' : ($request->status === 'rejected' ? 'erp-badge-danger' : 'erp-badge-warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-xs text-base-500">No requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Progress feedback</p>
            <h3 class="mt-2 text-lg font-semibold">Approval timeline</h3>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <p class="text-xs text-base-500">Step 1</p>
                    <p class="mt-2 font-semibold">Submitted</p>
                    <div class="mt-3 h-2 rounded-full bg-base-100">
                        <div class="h-2 w-full rounded-full bg-brand-600"></div>
                    </div>
                </div>
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <p class="text-xs text-base-500">Step 2</p>
                    <p class="mt-2 font-semibold">Manager review</p>
                    <div class="mt-3 h-2 rounded-full bg-base-100">
                        <div class="h-2 w-3/5 rounded-full bg-accent-500"></div>
                    </div>
                </div>
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <p class="text-xs text-base-500">Step 3</p>
                    <p class="mt-2 font-semibold">HR validation</p>
                    <div class="mt-3 h-2 rounded-full bg-base-100">
                        <div class="h-2 w-1/6 rounded-full bg-base-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
