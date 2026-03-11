<x-app-layout title="Personal Calendar" panel="employee">
    <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr] erp-stagger">
        <div class="erp-card p-6 relative overflow-hidden">
            <div class="pointer-events-none absolute -right-8 -top-8 h-20 w-20 rounded-full bg-brand-600/10" aria-hidden="true"></div>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Calendar view</p>
                    <h2 class="mt-2 text-xl font-semibold">Monthly schedule</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button class="erp-button-ghost">Today</button>
                    <button class="erp-button-ghost">Sync</button>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-7 gap-3 text-center text-sm text-base-500">
                <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                @for ($day = 1; $day <= 28; $day++)
                    <div class="rounded-2xl border border-base-100/70 bg-base-0/70 px-2 py-4 {{ in_array($day, [12, 13]) ? 'border-brand-600 bg-brand-50 text-brand-700' : '' }}">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        <div class="mt-2 text-[11px] text-base-400">{{ $day === 12 ? 'PTO' : '' }}</div>
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="erp-card p-6">
                <p class="erp-label">Legend</p>
                <ul class="mt-4 space-y-3 text-sm text-base-600">
                    <li class="flex items-center justify-between">
                        <span>Approved PTO</span>
                        <span class="erp-badge erp-badge-success">2 days</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Pending</span>
                        <span class="erp-badge erp-badge-warning">1 day</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Team holidays</span>
                        <span class="erp-badge">3 days</span>
                    </li>
                </ul>
            </div>
            <div class="erp-card p-6">
                <p class="erp-label">Upcoming</p>
                <h3 class="mt-2 text-lg font-semibold">Next events</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Annual leave</p>
                        <p class="text-xs text-base-500">Mar 12 - Mar 13</p>
                    </div>
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Team planning day</p>
                        <p class="text-xs text-base-500">Mar 20</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
