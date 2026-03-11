<x-app-layout title="Workspace Hub" panel="employee">
    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <section class="space-y-6">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Role-based workspaces</p>
                        <h2 class="mt-2 text-2xl font-semibold">Choose a panel to continue</h2>
                        <p class="mt-2 text-sm text-base-500">Switch context based on your role or responsibilities.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="erp-badge erp-badge-info">Live</span>
                        <span class="erp-badge">ERP-grade UI</span>
                    </div>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <a class="erp-card-muted p-5 transition hover:-translate-y-1 hover:shadow-soft" href="{{ route('employee.dashboard') }}">
                        <p class="erp-label">Employee</p>
                        <h3 class="mt-2 text-lg font-semibold">Personal dashboard</h3>
                        <p class="mt-2 text-sm text-base-500">Balances, requests, calendar, and notifications.</p>
                    </a>
                    <a class="erp-card-muted p-5 transition hover:-translate-y-1 hover:shadow-soft" href="{{ route('manager.dashboard') }}">
                        <p class="erp-label">Manager</p>
                        <h3 class="mt-2 text-lg font-semibold">Approval workspace</h3>
                        <p class="mt-2 text-sm text-base-500">Pending approvals, team coverage, and insights.</p>
                    </a>
                    <a class="erp-card-muted p-5 transition hover:-translate-y-1 hover:shadow-soft" href="{{ route('admin.dashboard') }}">
                        <p class="erp-label">Admin</p>
                        <h3 class="mt-2 text-lg font-semibold">System control</h3>
                        <p class="mt-2 text-sm text-base-500">Leave types, allocations, users, and policy settings.</p>
                    </a>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="erp-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="erp-label">Platform health</p>
                            <h3 class="mt-2 text-lg font-semibold">Operational readiness</h3>
                        </div>
                        <span class="erp-badge erp-badge-success">Healthy</span>
                    </div>
                    <div class="mt-6 space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs text-base-500">
                                <span>Automation workflows</span>
                                <span>92%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-base-100">
                                <div class="h-2 w-[92%] rounded-full bg-brand-600"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs text-base-500">
                                <span>Notification delivery</span>
                                <span>96%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-base-100">
                                <div class="h-2 w-[96%] rounded-full bg-accent-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="erp-card p-6">
                    <p class="erp-label">Upcoming launches</p>
                    <h3 class="mt-2 text-lg font-semibold">Delivery pipeline</h3>
                    <ul class="mt-6 space-y-4 text-sm text-base-600">
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Team calendar sync</span>
                            <span class="erp-badge erp-badge-info">2 days</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Payroll integration</span>
                            <span class="erp-badge">Roadmap</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="erp-card p-6">
                <p class="erp-label">Announcements</p>
                <h3 class="mt-2 text-lg font-semibold">Today in the org</h3>
                <ul class="mt-4 space-y-4 text-sm text-base-600">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-brand-600"></span>
                        <div>
                            <p class="font-semibold text-base-900">Leave policy refresh</p>
                            <p class="text-xs text-base-500">New carry-forward rules start March 1.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-accent-500"></span>
                        <div>
                            <p class="font-semibold text-base-900">Manager enablement</p>
                            <p class="text-xs text-base-500">Approval workshop scheduled for Friday.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="erp-card p-6">
                <p class="erp-label">Quick actions</p>
                <div class="mt-4 grid gap-3">
                    <button class="erp-button">Launch request flow</button>
                    <button class="erp-button-ghost">Review pending approvals</button>
                    <button class="erp-button-ghost">Export leave report</button>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
