<x-layouts.erp :title="'Department Calendar'">
    <div class="grid gap-6 lg:grid-cols-[1.7fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department view</p>
                    <h2 class="mt-2 text-xl font-semibold">{{ now()->setYear($year)->setMonth($month)->format('F Y') }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('manager.calendar', ['year' => $year, 'month' => $month - 1]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">&larr;</a>
                    <a href="{{ route('manager.calendar', ['year' => now()->year, 'month' => now()->month]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Today</a>
                    <a href="{{ route('manager.calendar', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">&rarr;</a>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-7 gap-2 text-center text-xs text-gray-500">
                <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
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
                        $count = $dayEvents->count();
                    @endphp
                    <div class="rounded-xl border {{ $count > 0 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-100 bg-gray-50' }} px-2 py-3">
                        <div class="text-sm font-semibold">{{ $day }}</div>
                        @if($count > 0)
                            <div class="mt-1 text-[11px] text-gray-500">{{ $count }} off</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Highlights</p>
                <h3 class="mt-2 text-lg font-semibold">Coverage insights</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    @php
                        $maxOverlap = $events->groupBy(fn($e) => $e->start_date->format('Y-m-d'))->sortByDesc->count()->first();
                    @endphp
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">On leave today</p>
                        <p class="text-xs text-gray-500">{{ $onLeaveToday }} team member(s)</p>
                    </div>
                    @if($maxOverlap)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <p class="font-semibold text-gray-900">Highest overlap</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($maxOverlap->first()->start_date)->format('M j') }} &middot; {{ $maxOverlap->count() }} people out</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Requests this month</p>
                <div class="mt-4 space-y-2">
                    @forelse($events->sortBy('start_date')->take(5) as $event)
                        <div class="text-sm flex items-center justify-between">
                            <span class="text-gray-700">{{ $event->user->name }}</span>
                            <span class="text-xs text-gray-500">{{ $event->start_date->format('M j') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No requests this month.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</x-layouts.erp>