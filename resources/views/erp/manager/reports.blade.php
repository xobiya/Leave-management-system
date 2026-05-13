<x-layouts.erp :title="'Reports'">
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Analytics</p>
                    <h2 class="mt-2 text-xl font-semibold">Leave trends</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('manager.reports.balance') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Detailed reports</a>
                </div>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Top request type</p>
                    <p class="mt-2 text-lg font-semibold">{{ $topType?->leaveType?->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500">{{ $topType ? round($topType->total / max($requests + $rejected, 1) * 100) : 0 }}% of all requests</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Average duration</p>
                    <p class="mt-2 text-lg font-semibold">{{ $avgDuration ? number_format($avgDuration, 1) : '0' }} days</p>
                    <p class="text-xs text-gray-500">Per approved request</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</p>
                    <p class="mt-2 text-lg font-semibold text-green-600">{{ $requests }}</p>
                    <p class="text-xs text-gray-500">{{ $requests + $rejected > 0 ? round($requests / ($requests + $rejected) * 100) : 0 }}% approval rate</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</p>
                    <p class="mt-2 text-lg font-semibold text-red-600">{{ $rejected }}</p>
                    <p class="text-xs text-gray-500">{{ $requests + $rejected > 0 ? round($rejected / ($requests + $rejected) * 100) : 0 }}% rejection rate</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Detail report</p>
            <h3 class="mt-2 text-lg font-semibold">Department breakdown</h3>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Team</th>
                            <th class="px-4 py-3 font-medium">Requests</th>
                            <th class="px-4 py-3 font-medium">Approved</th>
                            <th class="px-4 py-3 font-medium">Rejected</th>
                            <th class="px-4 py-3 font-medium">Avg. days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($departmentBreakdown as $dept)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $dept->department }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dept->total_requests }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dept->approved }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dept->rejected }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($dept->avg_days, 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No leave data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.erp>
