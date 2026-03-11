<x-app-layout title="System Settings" panel="admin">
    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="erp-card p-6">
            <p class="erp-label">System configuration</p>
            <h2 class="mt-2 text-xl font-semibold">Global settings</h2>
            <form class="mt-6 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="erp-label">Default approval flow</label>
                    <select class="erp-input mt-2">
                        <option>Manager only</option>
                        <option>Manager + HR</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Yearly reset date</label>
                    <input type="date" class="erp-input mt-2" />
                </div>
                <div>
                    <label class="erp-label">Time zone</label>
                    <select class="erp-input mt-2">
                        <option>UTC</option>
                        <option>GMT+1</option>
                        <option>GMT+3</option>
                    </select>
                </div>
                <div>
                    <label class="erp-label">Default locale</label>
                    <select class="erp-input mt-2">
                        <option>English (US)</option>
                        <option>English (UK)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="erp-label">HR policy notes</label>
                    <textarea class="erp-input mt-2 h-28" placeholder="Share key policy details"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="erp-button">Save settings</button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="erp-card p-6">
                <p class="erp-label">Security</p>
                <h3 class="mt-2 text-lg font-semibold">Access controls</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Require MFA</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Session timeout</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                </div>
            </div>

            <div class="erp-card p-6">
                <p class="erp-label">Notifications</p>
                <h3 class="mt-2 text-lg font-semibold">Delivery channels</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Email alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>In-app alerts</span>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                    <label class="flex items-center justify-between rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <span>Slack webhook</span>
                        <input type="checkbox" class="h-4 w-4 rounded border-base-200 text-brand-600 focus:ring-brand-400" />
                    </label>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
