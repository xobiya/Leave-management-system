<x-layouts.erp :title="'Personal Calendar'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Personal Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->setYear($year)->setMonth($month)->format('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employee.calendar', ['year' => $year, 'month' => $month - 1]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">&larr; Previous</a>
            <a href="{{ route('employee.calendar', ['year' => now()->year, 'month' => now()->month]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Today</a>
            <a href="{{ route('employee.calendar', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Next &rarr;</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="grid grid-cols-7 gap-3 text-center text-sm text-gray-500">
                <span class="font-medium text-gray-700">Mo</span><span class="font-medium text-gray-700">Tu</span><span class="font-medium text-gray-700">We</span><span class="font-medium text-gray-700">Th</span><span class="font-medium text-gray-700">Fr</span><span>Sa</span><span>Su</span>
                @php
                    $firstDay = now()->setYear($year)->setMonth($month)->startOfMonth()->dayOfWeek;
                    $firstDay = $firstDay == 0 ? 6 : $firstDay - 1;
                @endphp
                @for ($i = 0; $i < $firstDay; $i++)
                    <div></div>
                @endfor
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = now()->setYear($year)->setMonth($month)->setDay($day)->format('Y-m-d');
                        $dayEvents = $events->filter(fn ($e) => $e->start_date->format('Y-m-d') <= $date && $e->end_date->format('Y-m-d') >= $date);
                        $hasEvent = $dayEvents->isNotEmpty();
                        $statusColor = $hasEvent ? match($dayEvents->first()->status) {
                            'approved', 'manager_approved', 'hr_approved' => 'border-green-500 bg-green-50 text-green-700',
                            'submitted', 'under_review' => 'border-yellow-500 bg-yellow-50 text-yellow-700',
                            'rejected' => 'border-red-500 bg-red-50 text-red-700',
                            default => 'border-indigo-600 bg-indigo-50 text-indigo-700',
                        } : 'border-gray-100 bg-gray-50 text-gray-700';
                    @endphp
                    <div class="rounded-xl border {{ $statusColor }} px-2 py-4 relative">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        @if($hasEvent)
                            <div class="mt-1 text-[11px] leading-tight">
                                @foreach($dayEvents->take(2) as $event)
                                    <div class="truncate">{{ $event->leaveType->name ?? 'Leave' }}</div>
                                @endforeach
                                @if($dayEvents->count() > 2)
                                    <div class="text-gray-400">+{{ $dayEvents->count() - 2 }} more</div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Legend</p>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="flex items-center justify-between">
                        <span>Approved</span>
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">{{ $events->whereIn('status', ['approved', 'manager_approved', 'hr_approved'])->count() }} days</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Pending</span>
                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">{{ $events->whereIn('status', ['submitted', 'under_review'])->count() }} days</span>
                    </li>
                </ul>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming</p>
                <h3 class="mt-2 text-lg font-bold text-gray-900">Next events</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    @forelse($events->where('start_date', '>=', today())->sortBy('start_date')->take(5) as $event)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $event->leaveType->name ?? 'Leave' }}</p>
                            <p class="text-xs text-gray-500">{{ $event->start_date->format('M j') }} - {{ $event->end_date->format('M j') }}</p>
                            <span class="text-xs {{ $event->status === 'approved' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($event->status) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">No upcoming leave planned.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</x-layouts.erp>