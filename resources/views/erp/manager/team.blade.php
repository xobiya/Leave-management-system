<x-layouts.erp :title="'Team Availability'">
    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage</p>
                    <h2 class="mt-2 text-xl font-semibold">Team availability view</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Export</button>
                </div>
            </div>
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Employee</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Next leave</th>
                            <th class="px-4 py-3 font-medium">Coverage status</th>
                            <th class="px-4 py-3 font-medium">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($teamMembers as $member)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $member->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $member->employee?->position?->title ?? $member->job_title ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if($member->next_leave)
                                        {{ $member->next_leave->start_date->format('M j') }}@if($member->next_leave->start_date != $member->next_leave->end_date) - {{ $member->next_leave->end_date->format('M j') }}@endif
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $balance = $member->total_balance;
                                        $status = $balance > 15 ? 'Healthy' : ($balance > 5 ? 'Limited' : 'Risk');
                                        $color = $balance > 15 ? 'bg-green-100 text-green-700' : ($balance > 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">{{ $status }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($balance, 1) }} days</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No team members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team size</p>
                <h3 class="mt-2 text-lg font-semibold">{{ $teamMembers->count() }} members</h3>
                <p class="mt-2 text-sm text-gray-500">Direct reports under your supervision.</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Plan ahead</p>
                <h3 class="mt-2 text-lg font-semibold">Capacity overview</h3>
                <p class="mt-2 text-sm text-gray-500">Monitor team availability and plan coverage.</p>
                <a href="{{ route('manager.calendar') }}" class="inline-block px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4">View calendar</a>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tools</p>
                <h3 class="mt-2 text-lg font-semibold">Bulk actions</h3>
                <p class="mt-2 text-sm text-gray-500">Approve pending requests in one click.</p>
                <a href="{{ route('manager.dashboard') }}" class="inline-block px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4">Go to dashboard</a>
            </div>
        </div>
    </div>
</x-layouts.erp>