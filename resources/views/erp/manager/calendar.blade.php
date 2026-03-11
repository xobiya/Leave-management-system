<x-app-layout title="Department Calendar" panel="manager">
    <div class="grid gap-6 lg:grid-cols-[1.7fr_1fr]">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Department view</p>
                    <h2 class="mt-2 text-xl font-semibold">Shared calendar</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button class="erp-button-ghost">Week</button>
                    <button class="erp-button-ghost">Month</button>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-7 gap-2 text-center text-xs text-base-500">
                <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                @for ($day = 1; $day <= 28; $day++)
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-2 py-3 {{ in_array($day, [18, 19, 20, 21]) ? 'border-brand-600 bg-brand-50 text-brand-700' : '' }}">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        <div class="mt-2 text-[11px] text-base-400">{{ in_array($day, [18, 19]) ? '3 off' : '' }}</div>
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="erp-card p-6">
                <p class="erp-label">Highlights</p>
                <h3 class="mt-2 text-lg font-semibold">Coverage insights</h3>
                <div class="mt-4 space-y-3 text-sm text-base-600">
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Highest overlap</p>
                        <p class="text-xs text-base-500">Feb 19 · 4 people out</p>
                    </div>
                    <div class="rounded-xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                        <p class="font-semibold text-base-900">Lowest coverage</p>
                        <p class="text-xs text-base-500">Feb 21 · 68% capacity</p>
                    </div>
                </div>
            </div>
            <div class="erp-card p-6">
                <p class="erp-label">Actions</p>
                <h3 class="mt-2 text-lg font-semibold">Manager tools</h3>
                <button class="erp-button mt-4 w-full">Notify team</button>
                <button class="erp-button-ghost mt-3 w-full">Open staffing plan</button>
            </div>
        </aside>
    </div>
</x-app-layout>
