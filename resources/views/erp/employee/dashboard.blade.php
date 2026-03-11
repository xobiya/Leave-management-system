<x-app-layout title="Employee Dashboard" panel="employee">
    <div class="grid gap-6 erp-stagger">
        <section class="grid gap-4 lg:grid-cols-4">
            <div class="erp-kpi">
                <p class="erp-label">Leave balance</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">18.5</span>
                    <span class="erp-badge erp-badge-success">+2 days</span>
                </div>
                <p class="text-xs text-base-500">Available across annual and personal leave.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Pending requests</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">2</span>
                    <span class="erp-badge erp-badge-warning">Awaiting</span>
                </div>
                <p class="text-xs text-base-500">Manager review expected in 24 hrs.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Approved this year</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">9.0</span>
                    <span class="erp-badge erp-badge-info">Days</span>
                </div>
                <p class="text-xs text-base-500">Includes annual, sick, and remote days.</p>
            </div>
            <div class="erp-kpi">
                <p class="erp-label">Next leave</p>
                <div class="flex items-end justify-between">
                    <span class="text-3xl font-semibold font-display">Mar 12</span>
                    <span class="erp-badge">2 days</span>
                </div>
                <p class="text-xs text-base-500">Approved PTO with auto-delegation.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
            <div class="erp-card p-6 relative overflow-hidden">
                <div class="pointer-events-none absolute right-0 top-0 h-24 w-24 -translate-y-1/3 translate-x-1/3 rounded-full bg-accent-500/15" aria-hidden="true"></div>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Quick apply</p>
                        <h2 class="mt-2 text-xl font-semibold">Request time off</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="erp-badge">Smart defaults</span>
                        <span class="erp-badge erp-badge-info">Auto-calc days</span>
                    </div>
                </div>
                @php
                    $leaveTypeConfig = $leaveTypes->mapWithKeys(function ($type) {
                        return [
                            $type->id => [
                                'unit' => $type->request_unit,
                                'allow_half_day' => (bool) $type->allow_half_day,
                                'allow_hour' => (bool) $type->allow_hour,
                            ],
                        ];
                    });
                    $firstLeaveTypeId = $leaveTypes->first()?->id;
                @endphp
                <form
                    method="POST"
                    action="{{ route('employee.requests.store') }}"
                    class="mt-6 grid gap-4 md:grid-cols-2"
                    x-data="{
                        types: @js($leaveTypeConfig),
                        selectedId: @js($firstLeaveTypeId),
                        unit: 'day',
                        allowHalfDay: false,
                        allowHour: false,
                        startDate: '',
                        endDate: '',
                        requestedHours: '',
                        halfDayPeriod: 'am',
                        previewDays: 0,
                        previewError: '',
                        toast: '',
                        toastType: 'error',
                        previewTimer: null,
                        init() {
                            this.setUnit();
                            this.queuePreview();
                            this.$watch('selectedId', () => this.queuePreview());
                            this.$watch('unit', () => this.queuePreview());
                            this.$watch('startDate', () => this.queuePreview());
                            this.$watch('endDate', () => this.queuePreview());
                            this.$watch('requestedHours', () => this.queuePreview());
                            this.$watch('halfDayPeriod', () => this.queuePreview());
                        },
                        setUnit() {
                            const type = this.types[this.selectedId];
                            this.unit = type ? type.unit : 'day';
                            this.allowHalfDay = type ? !!type.allow_half_day : false;
                            this.allowHour = type ? !!type.allow_hour : false;
                            if (this.unit === 'day') {
                                this.requestedHours = '';
                            }
                        },
                        businessDays() {
                            if (!this.startDate || !this.endDate) {
                                return 0;
                            }
                            const start = new Date(this.startDate);
                            const end = new Date(this.endDate);
                            if (start > end) {
                                return 0;
                            }
                            let count = 0;
                            const current = new Date(start);
                            while (current <= end) {
                                const day = current.getDay();
                                if (day !== 0 && day !== 6) {
                                    count++;
                                }
                                current.setDate(current.getDate() + 1);
                            }
                            return count;
                        },
                        requestedDays() {
                            if (this.unit === 'half_day') {
                                return 0.5;
                            }
                            if (this.unit === 'hour') {
                                const hours = parseFloat(this.requestedHours || 0);
                                return hours > 0 ? (hours / 8).toFixed(2) : 0;
                            }
                            return this.businessDays();
                        },
                        queuePreview() {
                            clearTimeout(this.previewTimer);
                            this.previewTimer = setTimeout(() => this.fetchPreview(), 400);
                        },
                        async fetchPreview() {
                            this.previewError = '';
                            if (!this.startDate || !this.endDate || !this.selectedId) {
                                this.previewDays = 0;
                                return;
                            }
                            const params = new URLSearchParams({
                                leave_type_id: this.selectedId,
                                start_date: this.startDate,
                                end_date: this.endDate,
                                request_unit: this.unit,
                                requested_hours: this.requestedHours || '',
                                half_day_period: this.halfDayPeriod || '',
                            });
                            try {
                                const response = await fetch(`{{ route('employee.requests.preview') }}?${params.toString()}`);
                                if (!response.ok) {
                                    const payload = await response.json();
                                    this.previewError = payload.message || 'Unable to calculate preview.';
                                    this.previewDays = 0;
                                    return;
                                }
                                const payload = await response.json();
                                this.previewDays = payload.days;
                            } catch (error) {
                                this.previewError = 'Preview service unavailable.';
                                this.previewDays = 0;
                            }
                        },
                    }"
                >
                    @csrf
                    <div
                        x-show="toast"
                        x-transition
                        class="md:col-span-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                        x-text="toast"
                        x-cloak
                    ></div>
                    @if (session('status') === 'request-submitted')
                        <div class="md:col-span-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            Request submitted successfully.
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="md:col-span-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <div>
                        <label class="erp-label">Leave type</label>
                        <select name="leave_type_id" class="erp-input mt-2" required x-model="selectedId" @change="setUnit()">
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="erp-label">Request unit</label>
                        <select name="request_unit" class="erp-input mt-2" x-model="unit">
                            <option value="day" :selected="unit === 'day'">Full day</option>
                            <option value="half_day" x-show="allowHalfDay" :selected="unit === 'half_day'">Half day</option>
                            <option value="hour" x-show="allowHour" :selected="unit === 'hour'">Hours</option>
                        </select>
                        <p class="mt-2 text-xs text-base-500">Unit options are driven by leave type rules.</p>
                        @error('request_unit')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="erp-label">Delegate to</label>
                        <select class="erp-input mt-2">
                            <option>Auto-assign</option>
                            <option>W. Martinez</option>
                            <option>N. Diop</option>
                        </select>
                    </div>
                    <div>
                        <label class="erp-label">Start date</label>
                        <input type="date" name="start_date" class="erp-input mt-2" required x-model="startDate" />
                        <p class="mt-2 text-xs text-base-500">Suggested: align with team coverage window.</p>
                        @error('start_date')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="erp-label">End date</label>
                        <input type="date" name="end_date" class="erp-input mt-2" required x-model="endDate" />
                        <p class="mt-2 text-xs text-base-500">Auto-calculated: 2 business days.</p>
                        @error('end_date')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div x-show="unit === 'half_day'" x-cloak>
                        <label class="erp-label">Half-day period</label>
                        <select name="half_day_period" class="erp-input mt-2" x-model="halfDayPeriod">
                            <option value="am">Morning</option>
                            <option value="pm">Afternoon</option>
                        </select>
                        @error('half_day_period')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div x-show="unit === 'hour'" x-cloak>
                        <label class="erp-label">Requested hours</label>
                        <input type="number" name="requested_hours" step="0.5" min="0.5" class="erp-input mt-2" placeholder="e.g. 2" x-model="requestedHours" />
                        @error('requested_hours')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="erp-label">Reason</label>
                        <textarea name="reason" class="erp-input mt-2 h-28" placeholder="Add context for your manager..."></textarea>
                        <p class="mt-2 text-xs text-base-500">Inline validation appears here when needed.</p>
                        @error('reason')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-xs text-base-500">
                            Preview: <span class="font-semibold" x-text="previewDays"></span> days
                            <span x-show="previewError" class="ml-2 text-red-600" x-text="previewError"></span>
                            <template x-if="unit === 'hour'"><span> ({{ $hoursPerDay ?? 8 }} hrs/day)</span></template>
                        </div>
                        <button class="erp-button" type="submit">Submit request</button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="erp-card p-6">
                    <p class="erp-label">Status indicators</p>
                    <h3 class="mt-2 text-lg font-semibold">Request pipeline</h3>
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span class="text-sm font-semibold">Submitted</span>
                            <span class="erp-badge erp-badge-info">2</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span class="text-sm font-semibold">Manager review</span>
                            <span class="erp-badge erp-badge-warning">1</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl border border-base-100/70 bg-base-0/70 px-4 py-3">
                            <span class="text-sm font-semibold">Approved</span>
                            <span class="erp-badge erp-badge-success">8</span>
                        </div>
                    </div>
                </div>

                <div class="erp-card p-6">
                    <p class="erp-label">Notifications</p>
                    <h3 class="mt-2 text-lg font-semibold">Latest updates</h3>
                    <ul class="mt-4 space-y-4 text-sm text-base-600">
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-accent-500"></span>
                            <div>
                                <p class="font-semibold text-base-900">Your request for Feb 22-23 is approved.</p>
                                <p class="text-xs text-base-500">2 hours ago</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-brand-600"></span>
                            <div>
                                <p class="font-semibold text-base-900">Carry-forward policy updated.</p>
                                <p class="text-xs text-base-500">Yesterday</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-amber-400"></span>
                            <div>
                                <p class="font-semibold text-base-900">Manager added a comment on your request.</p>
                                <p class="text-xs text-base-500">2 days ago</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Request history</p>
                        <h3 class="mt-2 text-lg font-semibold">Timeline</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="erp-badge">Last 90 days</span>
                        <span class="erp-badge erp-badge-info">Export</span>
                    </div>
                </div>
                <ol class="mt-6 space-y-5 text-sm">
                    @forelse ($recentRequests as $request)
                        @php
                            $statusColor = match ($request->status) {
                                'approved' => 'bg-status-success',
                                'rejected' => 'bg-status-danger',
                                default => 'bg-status-warning',
                            };
                        @endphp
                        <li class="flex items-start gap-4">
                            <div class="mt-1 h-2 w-2 rounded-full {{ $statusColor }}"></div>
                            <div>
                                <p class="font-semibold text-base-900">{{ $request->leaveType->name }} {{ $request->status }}</p>
                                <p class="text-xs text-base-500">{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }} · {{ $request->days }} days</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-xs text-base-500">No requests yet.</li>
                    @endforelse
                </ol>
            </div>

            <div class="erp-card p-6">
                <p class="erp-label">Calendar</p>
                <h3 class="mt-2 text-lg font-semibold">February overview</h3>
                <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs text-base-500">
                    <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                    @for ($day = 1; $day <= 28; $day++)
                        <div class="rounded-2xl border border-base-100/70 bg-base-0/70 px-2 py-2 {{ $day === 12 ? 'border-brand-600 bg-brand-50 text-brand-700' : '' }}">
                            {{ $day }}
                        </div>
                    @endfor
                </div>
                <div class="mt-4 rounded-xl border border-base-100/70 bg-base-0/70 p-3 text-xs text-base-500">
                    Feb 12-13 marked as approved PTO.
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="erp-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="erp-label">Request list</p>
                        <h3 class="mt-2 text-lg font-semibold">My leave requests</h3>
                    </div>
                    <div class="flex gap-2">
                        <input class="erp-input" placeholder="Search requests" />
                        <select class="erp-input">
                            <option>All statuses</option>
                            <option>Approved</option>
                            <option>Pending</option>
                            <option>Rejected</option>
                        </select>
                    </div>
                </div>
                <table class="erp-table mt-4">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Manager</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRequests as $request)
                            <tr>
                                <td>{{ $request->leaveType->name }}</td>
                                <td>{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}</td>
                                <td>{{ $request->days }}</td>
                                <td>
                                    <span class="erp-badge {{ $request->status === 'approved' ? 'erp-badge-success' : ($request->status === 'rejected' ? 'erp-badge-danger' : 'erp-badge-warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->manager?->name ?? 'Unassigned' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-xs text-base-500">No requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="erp-card p-6">
                <p class="erp-label">System feedback</p>
                <h3 class="mt-2 text-lg font-semibold">Loading states</h3>
                <div class="mt-6 space-y-3">
                    <div class="h-3 w-2/3 rounded-full bg-base-100"></div>
                    <div class="h-3 w-5/6 rounded-full bg-base-100"></div>
                    <div class="h-3 w-1/2 rounded-full bg-base-100"></div>
                </div>
                <p class="mt-4 text-xs text-base-500">Skeleton loaders keep the UI responsive.</p>
            </div>
        </section>
    </div>
</x-app-layout>
