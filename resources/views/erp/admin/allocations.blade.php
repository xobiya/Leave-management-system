<x-app-layout title="Leave Allocations" panel="admin">
    <div class="grid gap-6">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Allocation engine</p>
                    <h2 class="mt-2 text-xl font-semibold">Annual allocations</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="erp-button-ghost">Run preview</button>
                    <button class="erp-button">Publish allocation</button>
                </div>
            </div>
            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave type</th>
                        <th>Allocated</th>
                        <th>Used</th>
                        <th>Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allocations as $allocation)
                        <tr>
                            <td>{{ $allocation->user->name }}</td>
                            <td>{{ $allocation->leaveType->name }}</td>
                            <td>{{ $allocation->allocated_days }}</td>
                            <td>{{ $allocation->used_days }}</td>
                            <td>{{ max(0, ($allocation->allocated_days + $allocation->carried_over_days) - $allocation->used_days) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-xs text-base-500">No allocations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Carry-forward</p>
            <h3 class="mt-2 text-lg font-semibold">Policy rules</h3>
            <form method="POST" action="{{ route('admin.allocations.store') }}" class="mt-4 grid gap-4 md:grid-cols-4">
                @csrf
                <div>
                    <label class="erp-label">Employee</label>
                    <select name="user_id" class="erp-input mt-2" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="erp-label">Leave type</label>
                    <select name="leave_type_id" class="erp-input mt-2" required>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="erp-label">Year</label>
                    <input name="year" class="erp-input mt-2" value="{{ now()->format('Y') }}" required />
                </div>
                <div>
                    <label class="erp-label">Allocated days</label>
                    <input name="allocated_days" class="erp-input mt-2" value="20" required />
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button class="erp-button">Save allocation</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
