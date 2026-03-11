<x-app-layout title="Manager Dashboard" panel="manager">
    <div class="grid gap-6 erp-stagger">
        <section class="grid gap-4 lg:grid-cols-4">
            <div class="erp-kpi">
                <p class="erp-label">Pending approvals</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">6</span>
                    <span class="erp-badge erp-badge-warning">Urgent</span>
                </div>
                <p class="text-xs text-base-500">3 requests exceed SLA window.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Team capacity</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">86%</span>
                    <span class="erp-badge">This week</span>
                </div>
                <p class="text-xs text-base-500">Coverage remains above threshold.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Upcoming absences</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">4</span>
                    <span class="erp-badge erp-badge-info">Next 14 days</span>
                </div>
                <p class="text-xs text-base-500">Plan standups with coverage in mind.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Approvals rate</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">94%</span>
                    <span class="erp-badge erp-badge-success">On track</span>
                </div>
                <p class="text-xs text-base-500">Avg. response time: 5.2 hrs.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Approval queue</p>
                        <h2 class="mt-2 text-xl font-semibold">Pending requests</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="erp-button-ghost">Bulk actions</button>
                        <button class="erp-button">Approve all safe</button>
                    </div>
                </div>
                <table class="erp-table mt-6" data-tour="approval-queue">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Impact</th>
                            <th>Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->leaveType->name }}</td>
                                <td>{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}</td>
                                <td><span class="erp-badge erp-badge-warning">Review</span></td>
                                <td class="flex gap-2">
                                    @if ($request->manager_status !== 'approved')
                                        <form method="POST" action="{{ route('manager.approvals.manager', $request) }}">
                                            @csrf
                                            <button class="erp-button" type="submit">Approve</button>
                                        </form>
                                    @endif
                                    @if (in_array($request->leaveType->validation_type, ['hr', 'both'], true) && $request->manager_status === 'approved' && $request->hr_status !== 'approved')
                                        <form method="POST" action="{{ route('manager.approvals.hr', $request) }}">
                                            @csrf
                                            <button class="erp-button" type="submit">HR Approve</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('manager.approvals.reject', $request) }}">
                                        @csrf
                                        <input type="hidden" name="reason" value="Needs review" />
                                        <button class="erp-button-ghost" type="submit">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-xs text-base-500">No pending requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-6">
                <div class="erp-card p-6">
                    <p class="erp-label">Team availability</p>
                    <h3 class="mt-2 text-lg font-semibold">Coverage heatmap</h3>
                    <div class="mt-5 space-y-3">
                        <div>
                            <div class="flex items-center justify-between text-xs text-base-500">
                                <span>Design</span>
                                <span>82%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-base-100">
                                <div class="h-2 w-[82%] rounded-full bg-gradient-to-r from-brand-600 to-accent-500"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs text-base-500">
                                <span>Engineering</span>
                                <span>90%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-base-100">
                                <div class="h-2 w-[90%] rounded-full bg-gradient-to-r from-accent-500 to-amber-500"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs text-base-500">
                                <span>Support</span>
                                <span>75%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-base-100">
                                <div class="h-2 w-[75%] rounded-full bg-gradient-to-r from-amber-500 to-accent-500"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="erp-card p-6">
                    <p class="erp-label">Workflow</p>
                    <h3 class="mt-2 text-lg font-semibold">Approval flow</h3>
                    <ol class="mt-4 space-y-3 text-sm text-base-600">
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Submitted</span>
                            <span class="erp-badge erp-badge-info">6</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>Manager review</span>
                            <span class="erp-badge erp-badge-warning">3</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span>HR validation</span>
                            <span class="erp-badge">2</span>
                        </li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Department calendar</p>
                        <h3 class="mt-2 text-lg font-semibold">Team absences</h3>
                    </div>
                    <span class="erp-badge">Feb 2026</span>
                </div>
                <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs text-base-500">
                    <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                    @for ($day = 1; $day <= 28; $day++)
                        <div class="rounded-2xl border border-base-100/70 bg-base-0/70 px-2 py-2 {{ in_array($day, [18, 19, 20]) ? 'border-accent-500 bg-accent-50 text-accent-700' : '' }}">
                            {{ $day }}
                        </div>
                    @endfor
                </div>
            </div>

            <div class="erp-card p-6">
                <p class="erp-label">Reports summary</p>
                <h3 class="mt-2 text-lg font-semibold">Insights</h3>
                <div class="mt-4 space-y-4 text-sm text-base-600">
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Top leave type</p>
                        <p class="text-xs text-base-500">Annual Leave · 48% of requests</p>
                    </div>
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Peak month</p>
                        <p class="text-xs text-base-500">August · 23% of yearly volume</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
