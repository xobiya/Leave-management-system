<x-app-layout title="Notifications" panel="employee">
    <div class="grid gap-6 lg:grid-cols-[1.3fr_1fr] erp-stagger">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Inbox</p>
                    <h2 class="mt-2 text-xl font-semibold">System alerts</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button class="erp-button-ghost">Mark all read</button>
                    <button class="erp-button-ghost">Preferences</button>
                </div>
            </div>
            <div class="mt-6 space-y-4">
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-base-900">Leave approved</p>
                        <span class="erp-badge erp-badge-success">Approved</span>
                    </div>
                    <p class="mt-2 text-sm text-base-500">Your request for Feb 12-13 was approved by R. Mendez.</p>
                    <p class="mt-3 text-xs text-base-400">2 hours ago</p>
                </div>
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-base-900">Action required</p>
                        <span class="erp-badge erp-badge-warning">Pending</span>
                    </div>
                    <p class="mt-2 text-sm text-base-500">Please update your delegate before March leave window.</p>
                    <p class="mt-3 text-xs text-base-400">Yesterday</p>
                </div>
                <div class="rounded-3xl border border-base-100/70 bg-base-0/70 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-base-900">Policy update</p>
                        <span class="erp-badge erp-badge-info">Info</span>
                    </div>
                    <p class="mt-2 text-sm text-base-500">Carry-forward caps updated for 2026.</p>
                    <p class="mt-3 text-xs text-base-400">2 days ago</p>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="erp-card p-6">
                <p class="erp-label">Preferences</p>
                <h3 class="mt-2 text-lg font-semibold">Notification channels</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Email alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>In-app notifications</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Daily digest</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                </div>
            </div>
            <div class="erp-card p-6">
                <p class="erp-label">Help</p>
                <h3 class="mt-2 text-lg font-semibold">Need assistance?</h3>
                <p class="mt-3 text-sm text-base-500">Use contextual help or reach HR operations for policy questions.</p>
                <button class="erp-button mt-4 w-full">Open help center</button>
            </div>
        </aside>
    </div>
</x-app-layout>
