<x-app-layout title="Admin Control Center" panel="admin">
    <div class="grid gap-6 erp-stagger">
        <section class="grid gap-4 lg:grid-cols-4">
            <div class="erp-kpi">
                <p class="erp-label">Active employees</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">412</span>
                    <span class="erp-badge erp-badge-success">+12</span>
                </div>
                <p class="text-xs text-base-500">Across 8 departments.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Leave types</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">9</span>
                    <span class="erp-badge">Configured</span>
                </div>
                <p class="text-xs text-base-500">Policies audited this quarter.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Allocations</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">98%</span>
                    <span class="erp-badge erp-badge-info">Coverage</span>
                </div>
                <p class="text-xs text-base-500">Auto-allocation completed.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Audit logs</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">1.4k</span>
                    <span class="erp-badge erp-badge-warning">7 alerts</span>
                </div>
                <p class="text-xs text-base-500">Security and policy events.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">System control</p>
                        <h2 class="mt-2 text-xl font-semibold">Leave type management</h2>
                    </div>
                    <button class="erp-button">Add leave type</button>
                </div>
                <table class="erp-table mt-6">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Paid</th>
                            <th>Approval</th>
                            <th>Carry forward</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Annual Leave</td>
                            <td>Yes</td>
                            <td>Required</td>
                            <td>Up to 5 days</td>
                            <td><span class="erp-badge erp-badge-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Sick Leave</td>
                            <td>Yes</td>
                            <td>Required</td>
                            <td>No</td>
                            <td><span class="erp-badge erp-badge-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Remote Day</td>
                            <td>Yes</td>
                            <td>Auto</td>
                            <td>No</td>
                            <td><span class="erp-badge">Limited</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="space-y-6">
                <div class="erp-card p-6">
                    <p class="erp-label">Allocation engine</p>
                    <h3 class="mt-2 text-lg font-semibold">Next auto-run</h3>
                    <p class="mt-3 text-sm text-base-500">Scheduled for Mar 1, 2026.</p>
                    <div class="mt-4 rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3 text-sm">
                        98% of employees already allocated.
                    </div>
                    <button class="erp-button mt-4 w-full">Run allocation now</button>
                </div>

                <div class="erp-card p-6">
                    <p class="erp-label">Security</p>
                    <h3 class="mt-2 text-lg font-semibold">Role & permission audit</h3>
                    <ul class="mt-4 space-y-3 text-sm text-base-600">
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Admin roles</span>
                            <span class="erp-badge erp-badge-info">12 users</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Pending access requests</span>
                            <span class="erp-badge erp-badge-warning">4</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="erp-card p-6">
                <p class="erp-label">User management</p>
                <h3 class="mt-2 text-lg font-semibold">Recent hires</h3>
                <table class="erp-table mt-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A. Kim</td>
                            <td>Engineering</td>
                            <td>Employee</td>
                            <td><span class="erp-badge erp-badge-info">Onboarding</span></td>
                        </tr>
                        <tr>
                            <td>L. Brown</td>
                            <td>Design</td>
                            <td>Manager</td>
                            <td><span class="erp-badge erp-badge-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>E. Patel</td>
                            <td>Finance</td>
                            <td>Employee</td>
                            <td><span class="erp-badge">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="erp-card p-6">
                <p class="erp-label">Settings</p>
                <h3 class="mt-2 text-lg font-semibold">System toggles</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <label class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Multi-level approvals</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Auto-allocation</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Carry-forward enforcement</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
