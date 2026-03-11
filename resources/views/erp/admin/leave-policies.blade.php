<x-app-layout title="Leave Policies" panel="admin">
    <div class="grid gap-6">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Policy engine</p>
                    <h2 class="mt-2 text-xl font-semibold">Leave policies</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="erp-badge erp-badge-info">Versioned</span>
                    <span class="erp-badge">DB configurable</span>
                </div>
            </div>

            @if (session('status') === 'leave-policy-created')
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Leave policy created.
                </div>
            @endif

            @if (session('status') === 'leave-policy-activated')
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Leave policy activated.
                </div>
            @endif

            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Version</th>
                        <th>Service Months</th>
                        <th>Max/Year</th>
                        <th>Effective</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($policies as $policy)
                        <tr>
                            <td>{{ $policy->leaveType?->name ?? 'N/A' }}</td>
                            <td>v{{ $policy->version }}</td>
                            <td>{{ $policy->min_service_months }}</td>
                            <td>{{ $policy->max_days_per_year ?? '—' }}</td>
                            <td>
                                {{ $policy->effective_from?->format('Y-m-d') }}
                                @if ($policy->effective_to)
                                    - {{ $policy->effective_to->format('Y-m-d') }}
                                @endif
                            </td>
                            <td>
                                <span class="erp-badge {{ $policy->is_active ? 'erp-badge-success' : 'erp-badge-warning' }}">
                                    {{ $policy->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if (!$policy->is_active)
                                    <form method="POST" action="{{ route('admin.leave-policies.activate', $policy) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="erp-button-ghost" type="submit">Activate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-xs text-base-500">No leave policies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Create policy</p>
            <h3 class="mt-2 text-lg font-semibold">New policy version</h3>

            <form method="POST" action="{{ route('admin.leave-policies.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="erp-label">Leave type</label>
                    <select name="leave_type_id" class="erp-input mt-2" required>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="erp-label">Min service months</label>
                    <input name="min_service_months" type="number" min="0" value="0" class="erp-input mt-2" required />
                </div>

                <div>
                    <label class="erp-label">Max days per year</label>
                    <input name="max_days_per_year" type="number" min="0" step="0.5" class="erp-input mt-2" />
                </div>

                <div>
                    <label class="erp-label">Max unpaid days per year</label>
                    <input name="max_unpaid_days_per_year" type="number" min="0" step="0.5" class="erp-input mt-2" />
                </div>

                <div>
                    <label class="erp-label">Backdate allowed</label>
                    <select name="allow_backdate" class="erp-input mt-2">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div>
                    <label class="erp-label">Max future apply days</label>
                    <input name="allow_future_apply_days" type="number" min="0" class="erp-input mt-2" />
                </div>

                <div>
                    <label class="erp-label">Yearly reset</label>
                    <select name="yearly_reset" class="erp-input mt-2">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div>
                    <label class="erp-label">Expiry days</label>
                    <input name="expiry_days" type="number" min="0" class="erp-input mt-2" />
                </div>

                <div>
                    <label class="erp-label">Carry-forward limit</label>
                    <input name="carry_forward_limit" type="number" min="0" step="0.5" class="erp-input mt-2" />
                </div>

                <div>
                    <label class="erp-label">Activate now</label>
                    <select name="is_active" class="erp-input mt-2">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div>
                    <label class="erp-label">Effective from</label>
                    <input name="effective_from" type="date" value="{{ now()->toDateString() }}" class="erp-input mt-2" required />
                </div>

                <div>
                    <label class="erp-label">Effective to</label>
                    <input name="effective_to" type="date" class="erp-input mt-2" />
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button class="erp-button" type="submit">Save policy</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
